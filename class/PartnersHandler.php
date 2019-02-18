<?php

namespace XoopsModules\Xoopartners;

/**
 * Xoopartners module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         Xoopartners
 * @since           2.6.0
 * @author          Laurent JEN (Aka DuGris)

 */
use Xoops\Core\Database\Connection;
use Xoops\Core\Request;

/**
 * Class PartnersHandler
 */
class PartnersHandler extends \XoopsPersistableObjectHandler
{
    private $published;

    /**
     * XoopartnersPartnersHandler constructor.
     * @param Connection|null $db
     */
    public function __construct(Connection $db = null)
    {
        parent::__construct($db, 'xoopartners', Partners::class, 'xoopartners_id', 'xoopartners_title');

        // Module
        $helper = \XoopsModules\Xoopartners\Helper::getInstance();
        $this->config = $helper->loadConfig();
        $this->catHandler = $helper->getHandler('Categories');
        $this->rldHandler = $helper->getHandler('Rld');
    }

    /**
     * @param \Xoops\Core\Kernel\XoopsObject $object
     * @param bool|true   $force
     * @return bool|mixed
     */
    public function insert(\Xoops\Core\Kernel\XoopsObject $object, $force = true)
    {
        $xoops = \Xoops::getInstance();
        if (parent::insert($object, $force)) {
            if ($object->isNew()) {
                return $xoops->db()->getInsertId();
            }

            return $object->getVar('xoopartners_id');
        }

        return false;
    }

    /**
     * @param int $category_id
     * @param int $online
     * @return array
     */
    public function renderAdminList($category_id = 0, $online = -1)
    {
        $criteria = new \CriteriaCompo();
        if ($this->config['xoopartners_category']['use_categories']) {
            $criteria->add(new \Criteria('xoopartners_category', $category_id));
        }
        if ($online >= 0) {
            $criteria->add(new \Criteria('xoopartners_online', $online));
            $criteria->add(new \Criteria('xoopartners_accepted', $online), 'OR');
        }
        $criteria->setSort('xoopartners_order');
        $criteria->setOrder('asc');

        return $this->getObjects($criteria, true, false);
    }

    /**
     * @param int    $category_id
     * @param string $sort
     * @param string $order
     * @param int    $start
     * @param int    $limit
     * @return array
     */
    public function getPartners($category_id = 0, $sort = 'order', $order = 'asc', $start = 0, $limit = 0)
    {
        $criteria = new \CriteriaCompo();
        if ($category_id >= 0 && $this->config['xoopartners_category']['use_categories']) {
            $criteria->add(new \Criteria('xoopartners_category', $category_id));
        }
        $criteria->add(new \Criteria('xoopartners_accepted', 1));
        $criteria->add(new \Criteria('xoopartners_online', 1));
        $criteria->add(new \Criteria('xoopartners_published', 0, '>'));
        $criteria->add(new \Criteria('xoopartners_published', time(), '<='));

        if ('random' === $sort) {
            $criteria->setSort('rand()');
        } else {
            $criteria->setSort('xoopartners_' . $sort);
        }
        $criteria->setOrder($order);
        $criteria->setStart($start);
        $criteria->setLimit($limit);

        return $this->getObjects($criteria, true, false);
    }

    /**
     * @param $partner_id
     * @return bool
     */
    public function setOnline($partner_id)
    {
        if (0 != $partner_id) {
            $partner = $this->get($partner_id);
            if (1 == $partner->getVar('xoopartners_online')) {
                $partner->setVar('xoopartners_online', 0);
            } else {
                $partner->setVar('xoopartners_online', 1);
            }
            $this->insert($partner);

            return true;
        }

        return false;
    }

    /**
     * @param $partner_id
     * @return bool
     */
    public function setAccept($partner_id)
    {
        if (0 != $partner_id) {
            $partner = $this->get($partner_id);
            if (1 == $partner->getVar('xoopartners_accepted')) {
                $partner->setVar('xoopartners_accepted', 0);
            } else {
                $partner->setVar('xoopartners_accepted', 1);
            }
            $this->insert($partner);

            return true;
        }

        return false;
    }

    /**
     * @param $partnerObj
     * @return bool
     */
    public function setRead($partnerObj)
    {
        $read = $partnerObj->getVar('xoopartners_hits') + 1;
        $partnerObj->setVar('xoopartners_hits', $read);
        $this->insert($partnerObj);

        return true;
    }

    /**
     * @param $partnerObj
     * @return bool
     */
    public function setVisit($partnerObj)
    {
        $read = $partnerObj->getVar('xoopartners_visit') + 1;
        $partnerObj->setVar('xoopartners_visit', $read);
        $this->insert($partnerObj);

        return true;
    }

    /**
     * @param $partner_id
     * @param $like_dislike
     * @return array|bool
     */
    public function setLikeDislike($partner_id, $like_dislike)
    {
        if (0 != $partner_id) {
            $partner = $this->get($partner_id);
            if (is_object($partner) && 0 != count($partner)) {
                $xoops = \Xoops::getInstance();

                if ($ret = $this->rldHandler->setLikeDislike($partner_id, $like_dislike)) {
                    if (0 == $like_dislike) {
                        $xoopartners_dislike = $partner->getVar('xoopartners_dislike') + 1;
                        $partner->setVar('xoopartners_dislike', $xoopartners_dislike);
                    } elseif (1 == $like_dislike) {
                        $xoopartners_like = $partner->getVar('xoopartners_like') + 1;
                        $partner->setVar('xoopartners_like', $xoopartners_like);
                    }
                    $this->insert($partner);

                    return $partner->getValues();
                }
            }

            return false;
        }

        return false;
    }

    /**
     * @param $partner_id
     * @param $rate
     * @return bool
     */
    public function setRate($partner_id, $rate)
    {
        if (0 != $partner_id) {
            $partner = $this->get($partner_id);
            if (is_object($partner) && 0 != count($partner)) {
                $xoops = \Xoops::getInstance();

                if ($ret = $this->rldHandler->setRate($partner_id, $rate)) {
                    if (is_array($ret) && 3 == count($ret)) {
                        $partner->setVar('xoopartners_rates', $ret['average']);
                        $this->insert($partner);

                        return $ret;
                    }
                }
            }

            return false;
        }

        return false;
    }

    /**
     * @param $image_name
     * @return array
     */
    public function uploadImages($image_name)
    {
        $xoops = \Xoops::getInstance();
        $autoload = \XoopsLoad::loadConfig('xoopartners');

        $uploader = new \XoopsMediaUploader(
            \XoopsBaseConfig::get('uploads-path') . '/xoopartners/partners/images',
            $autoload['mimetypes'],
            $this->config['xoopartners_partner']['image_size'],
            $this->config['xoopartners_partner']['image_width'],
            $this->config['xoopartners_partner']['image_height']
        );

        $ret = [];
        $utilities = new \XoopsModules\Xoopartners\Utility();
        foreach (Request::getArray('xoops_upload_file', [], 'POST') as $k => $input_image) {
            if ('' !== Request::getArray($input_image, [], 'FILES')['tmp_name'] || is_readable(Request::getArray($input_image, [], 'FILES'['tmp_name']))) {
                $path_parts = pathinfo(Request::getArray($input_image, [], 'FILES')['name']);
                $uploader->setTargetFileName($utilities->cleanImage(mb_strtolower($image_name . '.' . $path_parts['extension'])));
                if ($uploader->fetchMedia(Request::getArray('xoops_upload_file', [], 'POST')[$k])) {
                    if ($uploader->upload()) {
                        $ret[$input_image] = ['filename' => $uploader->getSavedFileName(), 'error' => false, 'message' => ''];
                    } else {
                        $ret[$input_image] = ['filename' => Request::getArray($input_image, [], 'FILES')['name'], 'error' => true, 'message' => $uploader->getErrors()];
                    }
                } else {
                    $ret[$input_image] = ['filename' => Request::getArray($input_image, [], 'FILES')['name'], 'error' => true, 'message' => $uploader->getErrors()];
                }
            }
        }

        return $ret;
    }

    /**
     * @param $filename
     * @return string
     */

    /*
    public function cleanImage($filename)
    {
        $path_parts = pathinfo($filename);
        $string     = $path_parts['filename'];

        $string = str_replace('_', md5('xoopartners'), $string);
        $string = str_replace('-', md5('xoopartners'), $string);
        $string = str_replace(' ', md5('xoopartners'), $string);

        $string = preg_replace('~\p{P}~', '', $string);
        $string = htmlentities($string, ENT_NOQUOTES, _CHARSET);
        $string = preg_replace("~\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;~", "$1", $string);
        $string = preg_replace("~\&([A-za-z]{2})(?:lig)\;~", "$1", $string); // pour les ligatures e.g. "&oelig;"
        $string = preg_replace("~\&[^;]+\;~", '', $string); // supprime les autres caract�res

        $string = str_replace(md5('xoopartners'), '_', $string);

        return $string . '.' . $path_parts['extension'];
    }
    */
}
