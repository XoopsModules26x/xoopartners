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
use Xoops\Core\Kernel\Handlers\XoopsUser;
use Xoops\Core\Request;

/**
 * Class Partners
 */
class Partners extends \XoopsObject
{
    private $exclude_page
        = [
            'index',
            'search',
            'tag',
            'userinfo',
            'partners',
            'sitemap.xml',
        ];
    private $php_self = '';

    // constructor

    /**
     * Partners constructor.
     */
    public function __construct()
    {
        $xoops = \Xoops::getInstance();
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
        $helper = \XoopsModules\Xoopartners\Helper::getInstance();
        $this->config = $helper->loadConfig();
        $this->catHandler = $helper->getHandler('Categories');
        $this->rldHandler = $helper->getHandler('Rld');
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
        $xoops = \Xoops::getInstance();
        $memberHandler = $xoops->getHandlerMember();
        $poster = $memberHandler->getUser($this->getVar('xoopartners_uid'));
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
        $myts = \MyTextSanitizer::getInstance();
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

        $string = html_entity_decode($string, ENT_QUOTES);
        $search_pattern = ["\t", "\r\n", "\r", "\n", ',', '.', "'", ';', ':', ')', '(', '"', '?', '!', '{', '}', '[', ']', '<', '>', '/', '+', '_', '\\', '*', 'pagebreak', 'page'];
        $replace_pattern = [' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $string = str_replace($search_pattern, $replace_pattern, $string);

        $tmpkeywords = explode(' ', $string);
        $tmpkeywords = array_count_values($tmpkeywords);
        arsort($tmpkeywords);
        $tmpkeywords = array_keys($tmpkeywords);

        $tmpkeywords = array_unique($tmpkeywords);
        foreach ($tmpkeywords as $keyword) {
            if (!is_numeric($keyword) && mb_strlen(trim($keyword)) >= $limit) {
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
        $xoops = \Xoops::getInstance();
        $myts = \MyTextSanitizer::getInstance();
        $xoops_upload_path = \XoopsBaseConfig::get('uploads-path');
        $xoops_upload_url = \XoopsBaseConfig::get('uploads-url');

        $ret = parent::getValues();
        $ret['xoopartners_date_day'] = date('d', $this->getVar('xoopartners_published'));
        $ret['xoopartners_date_month'] = date('m', $this->getVar('xoopartners_published'));
        $ret['xoopartners_date_year'] = date('Y', $this->getVar('xoopartners_published'));
        $ret['xoopartners_time'] = $this->getVar('xoopartners_published');
        $ret['xoopartners_published'] = date(\XoopsLocale::formatTimestamp($this->getVar('xoopartners_published'), 's'));

        $ret['xoopartners_link'] = \XoopsBaseConfig::get('url') . '/modules/xoopartners/partner.php?partner_id=' . $this->getVar('xoopartners_id');
        if ('blank.gif' !== $this->getVar('xoopartners_image')) {
            $ret['xoopartners_image_link'] = $xoops_upload_url . '/xoopartners/partners/images/' . $this->getVar('xoopartners_image');
        } else {
            $ret['xoopartners_image_link'] = \XoopsBaseConfig::get('url') . '/' . $xoops->theme()->resourcePath('/modules/xoopartners/assets/images/partners.png');
        }

        //mb -------------------------
        $ret['qrcode_image_link'] = $xoops->service('qrcode')->getImgTag($ret['xoopartners_link'], ['alt' => 'QR code', 'title' => 'Xoops.org'])->getValue();
        //mb -------------------------

        $ret['xoopartners_uid_name'] = \Xoops\Core\Kernel\Handlers\XoopsUser::getUnameFromId($this->getVar('xoopartners_uid'), true);

        if ($this->config['xoopartners_category']['use_categories']) {
            $ret['xoopartners_categories'] = $this->catHandler->getParents($this->getVar('xoopartners_category'));
        }

        if (in_array($this->php_self, $this->exclude_page, true) && false !== mb_strpos($this->getVar('xoopartners_description'), '[breakpage]')) {
            $ret['xoopartners_description'] = mb_substr($this->getVar('xoopartners_description'), 0, mb_strpos($this->getVar('xoopartners_description'), '[breakpage]'));
            $ret['readmore'] = true;
        } else {
            $ret['xoopartners_description'] = str_replace('[breakpage]', '', $this->getVar('xoopartners_description'));
        }

        // tags
        static $tags;
        if ('index' === $this->php_self || 'partner_print' === $this->php_self || !in_array($this->php_self, $this->exclude_page, true)) {
            if ($xoops->registry()->offsetExists('XOOTAGS') && $xoops->registry()->get('XOOTAGS')) {
                $id = $this->getVar('xoopartners_id');
                if (!isset($tags[$this->getVar('xoopartners_id')])) {
                    $xootagsHandler = \XoopsModules\Xootags\Helper::getInstance()->getHandler('Tags'); //$xoops->getModuleHandler('tags', 'xootags');
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
        if (!in_array($this->php_self, $this->exclude_page, true)) {
            if ('rate' === $this->config['xoopartners_rld']['rld_mode']) {
                $ret['xoopartners_vote'] = $this->rldHandler->getVotes($this->getVar('xoopartners_id'));
                $ret['xoopartners_yourvote'] = $this->rldHandler->getbyUser($this->getVar('xoopartners_id'));
            }
        }

        return $ret;
    }

    public function cleanVarsForDB()
    {
        $myts = \MyTextSanitizer::getInstance();
        $system = \System::getInstance();
        foreach (parent::getValues() as $k => $v) {
            if ('dohtml' !== $k) {
                if (XOBJ_DTYPE_STIME == $this->vars[$k]['data_type'] || XOBJ_DTYPE_MTIME == $this->vars[$k]['data_type'] || XOBJ_DTYPE_LTIME == $this->vars[$k]['data_type']) {
//                    $value = $system->cleanVars($_POST[$k], 'date', date('Y-m-d'), 'date') + $system->cleanVars($_POST[$k], 'time', date('u'), 'int');
                    //TODO should we use here getString??
                    $value = Request::getArray('date', date('Y-m-d'), 'POST')[$k] + Request::getArray('time', date('u'), 'POST')[$k];
                    $this->setVar($k, isset($_POST[$k]) ? $value : $v);
                } elseif (XOBJ_DTYPE_INT == $this->vars[$k]['data_type']) {
                    $value = Request::getInt($k, $v, 'POST'); //$system->cleanVars($_POST, $k, $v, 'int');
                    $this->setVar($k, $value);
                } elseif (XOBJ_DTYPE_ARRAY == $this->vars[$k]['data_type']) {
                    $value = Request::getArray($k, $v, 'POST'); // $system->cleanVars($_POST, $k, $v, 'array');
                    $this->setVar($k, $value);
                } else {
                    $value = Request::getString($k, $v, 'POST'); //$system->cleanVars($_POST, $k, $v, 'string');
                    $this->setVar($k, stripslashes($value));
                }
            }
        }
    }

    public function sendNotifications()
    {
        $xoops = \Xoops::getInstance();
        if ($xoops->isActiveModule('notifications')) {
            $notificationHandler = \Notifications::getInstance()->getHandlerNotification();
            $tags = [];
            $tags['MODULE_NAME'] = $xoops->module->getVar('name');
            $tags['ITEM_NAME'] = $this->getVar('xoopartners_title');
            $tags['ITEM_URL'] = $xoops->url('/modules/xoopartners/partner.php?partner_id=' . $this->getVar('xoopartners_id'));
            $tags['ITEM_BODY'] = $this->getVar('xoopartners_description');
            $tags['DATESUB'] = $this->getVar('xoopartners_published');
            $notificationHandler->triggerEvent('global', 0, 'newcontent', $tags);
        }
    }
}
