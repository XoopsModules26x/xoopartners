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
 */

use Xoops\Core\Request;
use Xoops\Core\Database\Connection;
use Xoops\Core\Kernel\XoopsObject;
use Xoops\Core\Kernel\XoopsPersistableObjectHandler;

/**
 * Class XoopartnersCategories
 */
class XoopartnersCategories extends XoopsObject
{
    // constructor
    /**
     * XoopartnersCategories constructor.
     */
    public function __construct()
    {
        $this->initVar('xoopartners_category_id', XOBJ_DTYPE_INT, 0, true, 5);
        $this->initVar('xoopartners_category_parent_id', XOBJ_DTYPE_INT, 0, false, 5);
        $this->initVar('xoopartners_category_title', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('xoopartners_category_description', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('xoopartners_category_image', XOBJ_DTYPE_TXTBOX, 'blank.gif', false, 100);
        $this->initVar('xoopartners_category_order', XOBJ_DTYPE_INT, 0, false, 3);
        $this->initVar('xoopartners_category_online', XOBJ_DTYPE_INT, 1, false, 1);
        $this->initVar('xoopartners_category_partners', XOBJ_DTYPE_INT, 0, false, 10);

        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
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

        $ret                              = parent::getValues();
        $ret['xoopartners_category_link'] = \XoopsBaseConfig::get('url')  . '/modules/xoopartners/index.php?category_id=' . $ret['xoopartners_category_id'];
        if ($ret['xoopartners_category_image'] !== 'blank.gif') {
            $ret['xoopartners_category_image_link'] = $xoops_upload_url . '/xoopartners/categories/images/' . $ret['xoopartners_category_image'];
        } else {
            $ret['xoopartners_category_image_link'] = \XoopsBaseConfig::get('url')  . '/' . $xoops->theme()->resourcePath('/modules/xoopartners/assets/images/categories.png');
        }

        return $ret;
    }

    public function cleanVarsForDB()
    {
        $system = System::getInstance();
        foreach ($this->getValues() as $k => $v) {
            if ($k !== 'dohtml') {
                if ($this->vars[$k]['data_type'] == XOBJ_DTYPE_STIME || $this->vars[$k]['data_type'] == XOBJ_DTYPE_MTIME || $this->vars[$k]['data_type'] == XOBJ_DTYPE_LTIME) {
                    $value0 = $system->cleanVars($_POST[$k], 'date', date('Y-m-d'), 'date') + $system->cleanVars($_POST[$k], 'time', date('u'), 'int');
                    //TODO should we use here getString??
//                    $value = Request::getString('date', date('Y-m-d'), $_POST[$k]) + Request::getInt('time', date('u'), $_POST[$k]);
                    $value = Request::getArray('date', date('Y-m-d'), 'POST')[$k] + Request::getArray('time', date('u'), 'POST')[$k];
                    $this->setVar($k, isset($_POST[$k]) ? $value : $v);
                } elseif ($this->vars[$k]['data_type'] == XOBJ_DTYPE_INT) {
                    $value = Request::getInt($k, $v, 'POST'); //$system->cleanVars($_POST, $k, $v, 'int');
                    $this->setVar($k, $value);
                } elseif ($this->vars[$k]['data_type'] == XOBJ_DTYPE_ARRAY) {
                    $value =  Request::getArray($k, $v, 'POST'); // $system->cleanVars($_POST, $k, $v, 'array');
                    $this->setVar($k, $value);
                } else {
                    $value = Request::getString($k, $v, 'POST'); //$system->cleanVars($_POST, $k, $v, 'string');
                    $this->setVar($k, stripslashes($value));
                }
            }
        }
    }
}

/**
 * Class XoopartnersXoopartnersCategoriesHandler
 */
class XoopartnersCategoriesHandler extends XoopsPersistableObjectHandler
{
    private $published;

    /**
     * XoopartnersXoopartnersCategoriesHandler constructor.
     * @param Connection|null $db
     */
    public function __construct(Connection $db = null)
    {
        parent::__construct($db, 'xoopartners_categories', 'XoopartnersCategories', 'xoopartners_category_id', 'xoopartners_category_title');
    }

    /**
     * @param int $category_parent_id
     * @return array
     */
    public function renderAdminList($category_parent_id = 0)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('xoopartners_category_parent_id', $category_parent_id));
        $criteria->setSort('xoopartners_category_order');
        $criteria->setOrder('asc');

        $categories = $this->getObjects($criteria, true, false);
        foreach ($categories as $k => $category) {
            $categories[$k]['categories'] = $this->renderAdminList($category['xoopartners_category_id']);
        }

        return $categories;
    }

    /**
     * @param int        $category_parent_id
     * @param bool|true  $main
     * @param bool|true  $sub
     * @param bool|false $empty
     * @return array
     */
    public function getCategories($category_parent_id = 0, $main = true, $sub = true, $empty = false)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('xoopartners_category_parent_id', $category_parent_id));
        $criteria->add(new Criteria('xoopartners_category_online', 1));
        if ($empty) {
            $criteria->add(new Criteria('xoopartners_category_partners', 0, '!='));
        }
        $criteria->setSort('xoopartners_category_order');
        $criteria->setOrder('asc');

        $categories = $this->getObjects($criteria, true, false);
        if ($sub) {
            foreach ($categories as $k => $category) {
                $categories[$k]['categories'] = $this->getCategories($category['xoopartners_category_id'], false, $sub, $empty);
            }
        }

        if ($main) {
            $categories[0]['xoopartners_category_id']          = 0;
            $categories[0]['xoopartners_category_parent_id']   = 0;
            $categories[0]['xoopartners_category_title']       = _XOO_PARTNERS_CATEGORY_NONE;
            $categories[0]['xoopartners_category_description'] = _XOO_PARTNERS_CATEGORY_NONE;
            $categories[0]['xoopartners_category_image']       = 'blank.gif';
            $categories[0]['xoopartners_category_order']       = 0;
            $categories[0]['xoopartners_category_online']      = 1;
            $categories[0]['xoopartners_category_link']        = 'index.php';
            $categories[0]['xoopartners_category_image_link']  = \XoopsBaseConfig::get('url')  . '/modules/xoopartners/assets/images/default.png';
            ksort($categories);
        }

        return $categories;
    }

    /**
     * @param $category_id
     * @return array
     */
    public function getParents($category_id)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('xoopartners_category_id', $category_id));
        $ret = $this->getObjects($criteria, false, false);
        if (count($ret)) {
            if ($ret[0]['xoopartners_category_parent_id'] != 0) {
                $tmp   = $this->getParents($ret[0]['xoopartners_category_parent_id']);
                $ret[] = $tmp[0];
            }
        }

        return $ret;
    }

    /**
     * @param $category_id
     * @return bool
     */
    public function setOnline($category_id)
    {
        if ($category_id != 0) {
            $category = $this->get($category_id);
            if ($category->getVar('xoopartners_category_online') == 1) {
                $category->setVar('xoopartners_category_online', 0);
            } else {
                $category->setVar('xoopartners_category_online', 1);
            }
            $this->insert($category);

            return true;
        }

        return false;
    }

    /**
     * @param $category_id
     * @return bool
     */
    public function addPartner($category_id)
    {
        if ($category_id != 0) {
            $category = $this->get($category_id);
            $partner  = $category->getVar('xoopartners_category_partners') + 1;
            $category->setVar('xoopartners_category_partners', $partner);
            $this->insert($category);

            return true;
        }
        return null;
    }

    /**
     * @param $category_id
     * @return bool
     */
    public function delPartner($category_id)
    {
        if ($category_id != 0) {
            $category = $this->get($category_id);
            $partner  = $category->getVar('xoopartners_category_partners') - 1;
            $category->setVar('xoopartners_category_partners', $partner);
            $this->insert($category);

            return true;
        }
        return null;
    }

    /**
     * @param $image_name
     * @return array
     */
    public function uploadImages($image_name)
    {
        $xoops              = Xoops::getInstance();
        $autoload           = XoopsLoad::loadConfig('xoopartners');
        $xoopartnersModule = Xoopartners::getInstance();
        $partnersConfig    = $xoopartnersModule->loadConfig();

        $uploader = new XoopsMediaUploader(
            \XoopsBaseConfig::get('uploads-path') . '/xoopartners/categories/images', $autoload['mimetypes'],
            $partnersConfig['xoopartners_category']['image_size'],
            $partnersConfig['xoopartners_category']['image_width'],
            $partnersConfig['xoopartners_category']['image_height']
        );

        $ret = array();
        $utilities = new XooPartnersUtilities();
        foreach (Request::getArray('xoops_upload_file', array(), 'POST') as $k => $input_image) {
            if (Request::getArray($input_image, array(), 'FILES')['tmp_name'] !== '' || is_readable(Request::getArray($input_image, array(), 'FILES'['tmp_name']))) {
                $path_parts = pathinfo(Request::getArray($input_image, array(), 'FILES')['name']);
                $uploader->setTargetFileName($utilities->cleanImage(strtolower($image_name . '.' . $path_parts['extension'])));
                if ($uploader->fetchMedia(Request::getArray('xoops_upload_file', array(), 'POST')[$k])) {
                    if ($uploader->upload()) {
                        $ret[$input_image] = array('filename' => $uploader->getSavedFileName(), 'error' => false, 'message' => '');
                    } else {
                        $ret[$input_image] = array('filename' => Request::getArray($input_image, array(), 'FILES')['name'], 'error' => true, 'message' => $uploader->getErrors());
                    }
                } else {
                    $ret[$input_image] = array('filename' => Request::getArray($input_image, array(), 'FILES')['name'], 'error' => true, 'message' => $uploader->getErrors());
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
    /**
     * @param           $name
     * @param           $id
     * @param bool|true $none
     * @param string    $onchange
     */
    public function makeSelectBox($name, $id, $none = true, $onchange = '')
    {
        echo "<select name='" . $name . "'";
        if ($onchange != '') {
            echo " onchange='" . $onchange . "';";
        }
        echo ">\n";
        if ($none) {
            echo "<option value='0'>" . _XOO_PARTNERS_CATEGORY_NONE . "</option>\n";
        }
        $this->makeSelectOptions($this->renderAdminList(), $id);

        echo '</select>';
    }

    /**
     * @param $datas
     * @param $id
     */
    public function makeSelectOptions($datas, $id)
    {
        static $level;
        if (count($datas) != 0) {
            foreach ($datas as $key => $data) {
                $level    = isset($level) ? $level + 1 : 0;
                $style    = 5 * $level;
                $selected = '';
                if ($data['xoopartners_category_id'] == $id) {
                    $selected = " selected='selected'";
                }
                echo "<option value='" . $data['xoopartners_category_id'] . "' " . $selected . '>' . str_repeat('-', $style) . ' ' . $data['xoopartners_category_title'] . "</option>\n";

                if (count($data['categories']) != 0) {
                    $this->makeSelectOptions($data['categories'], $id);
                }
                --$level;
            }
        }
    }
}
