<?php
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
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         Xoopartners
 * @since           2.6.0
 * @author          Laurent JEN (Aka DuGris)
 * @version         $Id$
 */

use Xoops\Core\Database\Connection;
use Xoops\Core\Kernel\XoopsObject;
use Xoops\Core\Kernel\XoopsPersistableObjectHandler;
use Xoops\Core\Kernel\Handlers\XoopsUser;

/**
 * Class XoopartnersPartners
 */
class XoopartnersPartners extends XoopsObject
{
    private $exclude_page
        = array(
            'index',
            'search',
            'tag',
            'userinfo',
            'partners',
            'sitemap.xml'
        );
    private $php_self = '';

    // constructor
    /**
     * XoopartnersPartners constructor.
     */
    public function __construct()
    {
        $xoops          = Xoops::getInstance();
        $this->php_self = basename($xoops->getEnv('PHP_SELF'), '.php');

        $this->initVar('xoopartners_id', XOBJ_DTYPE_INT, 0, true, 11);
        $this->initVar('xoopartners_category', XOBJ_DTYPE_INT, 0, false, 11);
        $this->initVar('xoopartners_uid', XOBJ_DTYPE_INT, 0, false, 8);
        $this->initVar('xoopartners_title', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('xoopartners_description', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('xoopartners_url', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('xoopartners_image', XOBJ_DTYPE_TXTBOX, 'blank.gif', false, 100);
        $this->initVar('xoopartners_order', XOBJ_DTYPE_INT, 0, false, 3);
        $this->initVar('xoopartners_online', XOBJ_DTYPE_INT, 1, false, 1);
        $this->initVar('xoopartners_visit', XOBJ_DTYPE_INT, 0, false, 10);
        $this->initVar('xoopartners_hits', XOBJ_DTYPE_INT, 0, false, 10);

        if ($xoops->isAdminSide) {
            $this->initVar('xoopartners_accepted', XOBJ_DTYPE_INT, 1, false, 1);
        } else {
            $this->initVar('xoopartners_accepted', XOBJ_DTYPE_INT, 0, false, 1);
        }
        $this->initVar('xoopartners_rates', XOBJ_DTYPE_INT, 0, false, 1);
        $this->initVar('xoopartners_like', XOBJ_DTYPE_INT, 0, false, 1);
        $this->initVar('xoopartners_dislike', XOBJ_DTYPE_INT, 0, false, 1);
        $this->initVar('xoopartners_comments', XOBJ_DTYPE_INT, 0, false, 1);
        $this->initVar('xoopartners_published', XOBJ_DTYPE_STIME, time(), false, 10);

        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);

        // Module
        $partnersModule   = Xoopartners::getInstance();
        $this->config      = $partnersModule->loadConfig();
        $this->catHandler = $partnersModule->getCategoriesHandler();
        $this->rldHandler = $partnersModule->getRldHandler();
    }

    /**
     * @return bool
     */
    public function setVisit()
    {
        $visit = $this->getVar('xoopartners_visit') + 1;
        $this->setVar('xoopartners_visit', $visit);

        return true;
    }

    /**
     * @param bool|true $addpost
     */
    public function setPost($addpost = true)
    {
        $xoops          = Xoops::getInstance();
        $memberHandler = $xoops->getHandlerMember();
        $poster         = $memberHandler->getUser($this->getVar('xoopartners_uid'));
        if ($poster instanceof XoopsUser) {
            if ($addpost) {
                $memberHandler->updateUserByField($poster, 'posts', $poster->getVar('posts') + 1);
            } else {
                $memberHandler->updateUserByField($poster, 'posts', $poster->getVar('posts') - 1);
            }
        }
    }

    /**
     * @return mixed|string
     */
    public function getMetaDescription()
    {
        $myts   = MyTextSanitizer::getInstance();
        $string = $myts->undoHtmlSpecialChars($this->getVar('xoopartners_description'));
        $string = str_replace('[breakpage]', '', $string);
        // remove html tags
        $string = strip_tags($string);
//        return preg_replace(array('/&amp;/i'), array('&'), $string);
        return $string;
    }

    /**
     * @param int $limit
     * @return string
     */
    public function getMetaKeywords($limit = 5)
    {
        $string = $this->getMetaDescription() . ', ' . $this->getVar('xoopartners_title');

        $string          = html_entity_decode($string, ENT_QUOTES);
        $search_pattern  = array("\t", "\r\n", "\r", "\n", ',', '.', "'", ';', ':', ')', '(', '"', '?', '!', '{', '}', '[', ']', '<', '>', '/', '+', '_', '\\', '*', 'pagebreak', 'page');
        $replace_pattern = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
        $string          = str_replace($search_pattern, $replace_pattern, $string);

        $tmpkeywords = explode(' ', $string);
        $tmpkeywords = array_count_values($tmpkeywords);
        arsort($tmpkeywords);
        $tmpkeywords = array_keys($tmpkeywords);

        $tmpkeywords = array_unique($tmpkeywords);
        foreach ($tmpkeywords as $keyword) {
            if (!is_numeric($keyword) && strlen(trim($keyword)) >= $limit) {
                $keywords[] = htmlentities(trim($keyword));
            }
        }

        return implode(', ', $keywords);
    }

    /**
     * @param null $keys
     * @param null $format
     * @param null $maxDepth
     * @return array
     */
    public function getValues($keys = null, $format = null, $maxDepth = null)
    {
        $xoops = Xoops::getInstance();
        $myts  = MyTextSanitizer::getInstance();
        $xoops_upload_path = \XoopsBaseConfig::get('uploads-path');
        $xoops_upload_url = \XoopsBaseConfig::get('uploads-url');

        $ret                           = parent::getValues();
        $ret['xoopartners_date_day']   = date('d', $this->getVar('xoopartners_published'));
        $ret['xoopartners_date_month'] = date('m', $this->getVar('xoopartners_published'));
        $ret['xoopartners_date_year']  = date('Y', $this->getVar('xoopartners_published'));
        $ret['xoopartners_time']       = $this->getVar('xoopartners_published');
        $ret['xoopartners_published']  = date(\XoopsLocale::getFormatShortDate(), $this->getVar('xoopartners_published'));

        $ret['xoopartners_link'] = \XoopsBaseConfig::get('url')  . '/modules/xoopartners/partner.php?partner_id=' . $this->getVar('xoopartners_id');
        if ($this->getVar('xoopartners_image') !== 'blank.gif') {
            $ret['xoopartners_image_link'] = $xoops_upload_url . '/xoopartners/partners/images/' . $this->getVar('xoopartners_image');
        } else {
            $ret['xoopartners_image_link'] = \XoopsBaseConfig::get('url')  . '/' . $xoops->theme()->resourcePath('/modules/xoopartners/assets/images/partners.png');
        }

        //mb -------------------------
        $ret['qrcode_image_link'] = $xoops->service('qrcode')->getImgTag($ret['xoopartners_link'], array('alt' => 'QR code', 'title'=>'Xoops.org'))->getValue();
        //mb -------------------------

        $ret['xoopartners_uid_name'] = XoopsUser::getUnameFromId($this->getVar('xoopartners_uid'), true);

        if ($this->config['xoopartners_category']['use_categories']) {
            $ret['xoopartners_categories'] = $this->catHandler->getParents($this->getVar('xoopartners_category'));
        }

        if (in_array($this->php_self, $this->exclude_page) && strpos($this->getVar('xoopartners_description'), '[breakpage]') !== false) {
            $ret['xoopartners_description'] = substr($this->getVar('xoopartners_description'), 0, strpos($this->getVar('xoopartners_description'), '[breakpage]'));
            $ret['readmore']                = true;
        } else {
            $ret['xoopartners_description'] = str_replace('[breakpage]', '', $this->getVar('xoopartners_description'));
        }

        // tags
        static $tags;
        if ($this->php_self === 'index' || $this->php_self === 'partner_print' || !in_array($this->php_self, $this->exclude_page)) {
            if ($xoops->registry()->offsetExists('XOOTAGS') && $xoops->registry()->get('XOOTAGS')) {
                $id = $this->getVar('xoopartners_id');
                if (!isset($tags[$this->getVar('xoopartners_id')])) {
                    $xootagsHandler                       = $xoops->getModuleHandler('tags', 'xootags');
                    $tags[$this->getVar('xoopartners_id')] = $xootagsHandler->getbyItem($this->getVar('xoopartners_id'));
                }
                $ret['tags'] = $tags[$this->getVar('xoopartners_id')];
            }
        }

        return $ret;
    }

    /**
     * @param $ret
     * @return mixed
     */
    public function getRLD($ret)
    {
        if (!in_array($this->php_self, $this->exclude_page)) {
            if ($this->config['xoopartners_rld']['rld_mode'] === 'rate') {
                $ret['xoopartners_vote']     = $this->rldHandler->getVotes($this->getVar('xoopartners_id'));
                $ret['xoopartners_yourvote'] = $this->rldHandler->getbyUser($this->getVar('xoopartners_id'));
            }
        }

        return $ret;
    }

    public function cleanVarsForDB()
    {
        $myts   = MyTextSanitizer::getInstance();
        $system = System::getInstance();
        foreach (parent::getValues() as $k => $v) {
            if ($k !== 'dohtml') {
                if ($this->vars[$k]['data_type'] == XOBJ_DTYPE_STIME || $this->vars[$k]['data_type'] == XOBJ_DTYPE_MTIME || $this->vars[$k]['data_type'] == XOBJ_DTYPE_LTIME) {
                    $value = $system->cleanVars($_POST[$k], 'date', date('Y-m-d'), 'date') + $system->cleanVars($_POST[$k], 'time', date('u'), 'int');
                    $this->setVar($k, isset($_POST[$k]) ? $value : $v);
                } elseif ($this->vars[$k]['data_type'] == XOBJ_DTYPE_INT) {
                    $value = $system->cleanVars($_POST, $k, $v, 'int');
                    $this->setVar($k, $value);
                } elseif ($this->vars[$k]['data_type'] == XOBJ_DTYPE_ARRAY) {
                    $value = $system->cleanVars($_POST, $k, $v, 'array');
                    $this->setVar($k, $value);
                } elseif ($this->vars[$k]['data_type'] == XOBJ_DTYPE_TXTAREA) {
                    $value = $system->cleanVars($_POST, $k, $v, 'string');
                    $this->setVar($k, stripslashes($value));
                } else {
                    $value = $system->cleanVars($_POST, $k, $v, 'string');
                    $this->setVar($k, stripslashes($value));
                }
            }
        }
    }

    public function sendNotifications()
    {
        $xoops = Xoops::getInstance();
        if ($xoops->isActiveModule('notifications')) {
            $notificationHandler = Notifications::getInstance()->getNotificationHandler();
            $tags                 = array();
            $tags['MODULE_NAME']  = $xoops->module->getVar('name');
            $tags['ITEM_NAME']    = $this->getVar('xoopartners_title');
            $tags['ITEM_URL']     = $xoops->url('/modules/xoopartners/partner.php?partner_id=' . $this->getVar('xoopartners_id'));
            $tags['ITEM_BODY']    = $this->getVar('xoopartners_description');
            $tags['DATESUB']      = $this->getVar('xoopartners_published');
            $notificationHandler->triggerEvent('global', 0, 'newcontent', $tags);
        }
    }
}

/**
 * Class XoopartnersXoopartnersPartnersHandler
 */
class XoopartnersPartnersHandler extends XoopsPersistableObjectHandler
{
    private $published;

    /**
     * XoopartnersXoopartnersPartnersHandler constructor.
     * @param Connection|null $db
     */
    public function __construct(Connection $db = null)
    {
        parent::__construct($db, 'xoopartners', 'XoopartnersPartners', 'xoopartners_id', 'xoopartners_title');

        // Module
        $partnersModule   = Xoopartners::getInstance();
        $this->config      = $partnersModule->loadConfig();
        $this->catHandler = $partnersModule->getCategoriesHandler();
        $this->rldHandler = $partnersModule->getRldHandler();
    }

    /**
     * @param XoopsObject $object
     * @param bool|true   $force
     * @return bool|mixed
     */
    public function insert(XoopsObject $object, $force = true)
    {
        $xoops = Xoops::getInstance();
        if (parent::insert($object, $force)) {
            if ($object->isNew()) {
                return $xoops->db()->getInsertId();
            } else {
                return $object->getVar('xoopartners_id');
            }
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
        $criteria = new CriteriaCompo();
        if ($this->config['xoopartners_category']['use_categories']) {
            $criteria->add(new Criteria('xoopartners_category', $category_id));
        }
        if ($online >= 0) {
            $criteria->add(new Criteria('xoopartners_online', $online));
            $criteria->add(new Criteria('xoopartners_accepted', $online), 'OR');
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
        $criteria = new CriteriaCompo();
        if ($category_id >= 0 && $this->config['xoopartners_category']['use_categories']) {
            $criteria->add(new Criteria('xoopartners_category', $category_id));
        }
        $criteria->add(new Criteria('xoopartners_accepted', 1));
        $criteria->add(new Criteria('xoopartners_online', 1));
        $criteria->add(new Criteria('xoopartners_published', 0, '>'));
        $criteria->add(new Criteria('xoopartners_published', time(), '<='));

        if ($sort === 'random') {
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
        if ($partner_id != 0) {
            $partner = $this->get($partner_id);
            if ($partner->getVar('xoopartners_online') == 1) {
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
        if ($partner_id != 0) {
            $partner = $this->get($partner_id);
            if ($partner->getVar('xoopartners_accepted') == 1) {
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
        if ($partner_id != 0) {
            $partner = $this->get($partner_id);
            if (is_object($partner) && count($partner) != 0) {
                $xoops = Xoops::getInstance();

                if ($ret = $this->rldHandler->setLikeDislike($partner_id, $like_dislike)) {
                    if ($like_dislike == 0) {
                        $xoopartners_dislike = $partner->getVar('xoopartners_dislike') + 1;
                        $partner->setVar('xoopartners_dislike', $xoopartners_dislike);
                    } elseif ($like_dislike == 1) {
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
        if ($partner_id != 0) {
            $partner = $this->get($partner_id);
            if (is_object($partner) && count($partner) != 0) {
                $xoops = Xoops::getInstance();

                if ($ret = $this->rldHandler->setRate($partner_id, $rate)) {
                    if (is_array($ret) && count($ret) == 3) {
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
        $xoops    = Xoops::getInstance();
        $autoload = XoopsLoad::loadConfig('xoopartners');

        $uploader = new XoopsMediaUploader(
            $xoops->path('uploads') . '/xoopartners/partners/images',
            $autoload['mimetypes'],
            $this->config['xoopartners_partner']['image_size'],
            $this->config['xoopartners_partner']['image_width'],
            $this->config['xoopartners_partner']['image_height']
        );

        $ret = array();
        foreach ($_POST['xoops_upload_file'] as $k => $input_image) {
            if ($_FILES[$input_image]['tmp_name'] != '' || is_readable($_FILES[$input_image]['tmp_name'])) {
                $path_parts = pathinfo($_FILES[$input_image]['name']);
                $uploader->setTargetFileName($this->cleanImage(strtolower($image_name . '.' . $path_parts['extension'])));
                if ($uploader->fetchMedia($_POST['xoops_upload_file'][$k])) {
                    if ($uploader->upload()) {
                        $ret[$input_image] = array('filename' => $uploader->getSavedFileName(), 'error' => false, 'message' => '');
                    } else {
                        $ret[$input_image] = array('filename' => $_FILES[$input_image]['name'], 'error' => true, 'message' => $uploader->getErrors());
                    }
                } else {
                    $ret[$input_image] = array('filename' => $_FILES[$input_image]['name'], 'error' => true, 'message' => $uploader->getErrors());
                }
            }
        }

        return $ret;
    }

    /**
     * @param $filename
     * @return string
     */
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
}
