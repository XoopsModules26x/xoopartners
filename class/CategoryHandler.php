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

/**
 * Class CategoryHandler
 * @package XoopsModules\Xoopartners
 */
class CategoryHandler extends \XoopsPersistableObjectHandler
{
    private $_published;

    /**
     * CategoryHandler constructor.
     * @param \Xoops\Core\Database\Connection|null $db
     */
    public function __construct(Connection $db = null)
    {
        parent::__construct($db, 'xoopartners_categories', Category::class, 'xoopartners_category_id', 'xoopartners_category_title');
    }

    /**
     * @param int $category_parent_id
     * @return array
     */
    public function renderAdminList($category_parent_id = 0)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('xoopartners_category_parent_id', $category_parent_id));
        $criteria->setSort('xoopartners_category_order');
        $criteria->setOrder('asc');

        $categories = $this->getObjects($criteria, true, false);
        foreach ($categories as $k => $category) {
            $categories[$k]['categories'] = $this->renderAdminList($category['xoopartners_category_id']);
        }

        return $categories;
    }

    /**
     * @param int  $category_parent_id
     * @param bool $main
     * @param bool $sub
     * @param bool $empty
     * @return array
     */
    public function GetCategories($category_parent_id = 0, $main = true, $sub = true, $empty = false)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('xoopartners_category_parent_id', $category_parent_id));
        $criteria->add(new \Criteria('xoopartners_category_online', 1));
        if ($empty) {
            $criteria->add(new \Criteria('xoopartners_category_partners', 0, '!='));
        }
        $criteria->setSort('xoopartners_category_order');
        $criteria->setOrder('asc');

        $categories = $this->getObjects($criteria, true, false);
        if ($sub) {
            foreach ($categories as $k => $category) {
                $categories[$k]['categories'] = $this->GetCategories($category['xoopartners_category_id'], false, $sub, $empty);
            }
        }

        if ($main) {
            $categories[0]['xoopartners_category_id'] = 0;
            $categories[0]['xoopartners_category_parent_id'] = 0;
            $categories[0]['xoopartners_category_title'] = _XOO_PARTNERS_CATEGORY_NONE;
            $categories[0]['xoopartners_category_description'] = _XOO_PARTNERS_CATEGORY_NONE;
            $categories[0]['xoopartners_category_image'] = 'blank.gif';
            $categories[0]['xoopartners_category_order'] = 0;
            $categories[0]['xoopartners_category_online'] = 1;
            $categories[0]['xoopartners_category_link'] = 'index.php';
            $categories[0]['xoopartners_category_image_link'] = XOOPS_URL . '/modules/xoopartners/assets/images/default.png';
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
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('xoopartners_category_id', $category_id));
        $ret = $this->getObjects($criteria, false, false);
        if (count($ret)) {
            if (0 != $ret[0]['xoopartners_category_parent_id']) {
                $tmp = $this->getParents($ret[0]['xoopartners_category_parent_id']);
                $ret[] = $tmp[0];
            }
        }

        return $ret;
    }

    /**
     * @param $category_id
     * @return bool
     */
    public function SetOnline($category_id)
    {
        if (0 != $category_id) {
            $category = $this->get($category_id);
            if (1 == $category->getVar('xoopartners_category_online')) {
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
    public function Addpartner($category_id)
    {
        if (0 != $category_id) {
            $category = $this->get($category_id);
            $partner = $category->getVar('xoopartners_category_partners') + 1;
            $category->setVar('xoopartners_category_partners', $partner);
            $this->insert($category);

            return true;
        }
    }

    /**
     * @param $category_id
     * @return bool
     */
    public function Delpartner($category_id)
    {
        if (0 != $category_id) {
            $category = $this->get($category_id);
            $partner = $category->getVar('xoopartners_category_partners') - 1;
            $category->setVar('xoopartners_category_partners', $partner);
            $this->insert($category);

            return true;
        }
    }

    /**
     * @param $image_name
     * @return array
     */
    public function upload_images($image_name)
    {
        $xoops = \Xoops::getInstance();
        $autoload = \XoopsLoad::loadConfig('xoopartners');
        $xoopartners_module = \XoopsModules\Xoopartners\Helper::getInstance();
        $partners_config = $xoopartners_module->LoadConfig();

        $uploader = new \XoopsMediaUploader(
            $xoops->path('uploads') . '/xoopartners/categories/images',
            $autoload['mimetypes'],
            $partners_config['xoopartners_category']['image_size'],
            $partners_config['xoopartners_category']['image_width'],
            $partners_config['xoopartners_category']['image_height']
        );

        $ret = [];
        foreach ($_POST['xoops_upload_file'] as $k => $input_image) {
            if ('' != $_FILES[$input_image]['tmp_name'] || is_readable($_FILES[$input_image]['tmp_name'])) {
                $path_parts = pathinfo($_FILES[$input_image]['name']);
                $uploader->setTargetFileName($this->CleanImage(mb_strtolower($image_name . '.' . $path_parts['extension'])));
                if ($uploader->fetchMedia($_POST['xoops_upload_file'][$k])) {
                    if ($uploader->upload()) {
                        $ret[$input_image] = ['filename' => $uploader->getSavedFileName(), 'error' => false, 'message' => ''];
                    } else {
                        $ret[$input_image] = ['filename' => $_FILES[$input_image]['name'], 'error' => true, 'message' => $uploader->getErrors()];
                    }
                } else {
                    $ret[$input_image] = ['filename' => $_FILES[$input_image]['name'], 'error' => true, 'message' => $uploader->getErrors()];
                }
            }
        }

        return $ret;
    }

    /**
     * @param $filename
     * @return string
     */
    public function CleanImage($filename)
    {
        $path_parts = pathinfo($filename);
        $string = $path_parts['filename'];

        $string = str_replace('_', md5('xoopartners'), $string);
        $string = str_replace('-', md5('xoopartners'), $string);
        $string = str_replace(' ', md5('xoopartners'), $string);

        $string = preg_replace('~\p{P}~', '', $string);
        $string = htmlentities($string, ENT_NOQUOTES, _CHARSET);
        $string = preg_replace("~\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;~", '$1', $string);
        $string = preg_replace("~\&([A-za-z]{2})(?:lig)\;~", '$1', $string); // pour les ligatures e.g. "&oelig;"
        $string = preg_replace("~\&[^;]+\;~", '', $string); // supprime les autres caract�res

        $string = str_replace(md5('xoopartners'), '_', $string);

        return $string . '.' . $path_parts['extension'];
    }

    /**
     * @param        $name
     * @param        $id
     * @param bool   $none
     * @param string $onchange
     */
    public function makeSelectBox($name, $id, $none = true, $onchange = '')
    {
        echo "<select name='" . $name . "'";
        if ('' != $onchange) {
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
        if (0 != count($datas)) {
            foreach ($datas as $key => $data) {
                $level = isset($level) ? $level + 1 : 0;
                $style = 5 * $level;
                $selected = '';
                if ($data['xoopartners_category_id'] == $id) {
                    $selected = " selected='selected'";
                }
                echo "<option value='" . $data['xoopartners_category_id'] . "' " . $selected . '>' . str_repeat('-', $style) . ' ' . $data['xoopartners_category_title'] . "</option>\n";

                if (0 != count($data['categories'])) {
                    $this->makeSelectOptions($data['categories'], $id);
                }
                --$level;
            }
        }
    }
}
