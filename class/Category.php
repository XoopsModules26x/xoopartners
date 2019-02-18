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
class Category extends \XoopsObject
{
    // constructor
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
        $xoops = \Xoops::getInstance();
        $myts = \MyTextSanitizer::getInstance();

        $ret = parent::getValues();
        $ret['xoopartners_category_link'] = XOOPS_URL . '/modules/xoopartners/index.php?category_id=' . $ret['xoopartners_category_id'];
        if ('blank.gif' != $ret['xoopartners_category_image']) {
            $ret['xoopartners_category_image_link'] = XOOPS_UPLOAD_URL . '/xoopartners/categories/images/' . $ret['xoopartners_category_image'];
        } else {
            $ret['xoopartners_category_image_link'] = XOOPS_URL . '/' . $xoops->theme()->resourcePath('/modules/xoopartners/assets/images/categories.png');
        }

        return $ret;
    }

    public function CleanVarsForDB()
    {
        $system = \System::getInstance();
        foreach ($this->getValues() as $k => $v) {
            if ('dohtml' != $k) {
                if (XOBJ_DTYPE_STIME == $this->vars[$k]['data_type'] || XOBJ_DTYPE_MTIME == $this->vars[$k]['data_type'] || XOBJ_DTYPE_LTIME == $this->vars[$k]['data_type']) {
                    $value = $system->cleanVars($_POST[$k], 'date', date('Y-m-d'), 'date') + $system->cleanVars($_POST[$k], 'time', date('u'), 'int');
                    $this->setVar($k, isset($_POST[$k]) ? $value : $v);
                } elseif (XOBJ_DTYPE_INT == $this->vars[$k]['data_type']) {
                    $value = $system->cleanVars($_POST, $k, $v, 'int');
                    $this->setVar($k, $value);
                } elseif (XOBJ_DTYPE_ARRAY == $this->vars[$k]['data_type']) {
                    $value = $system->cleanVars($_POST, $k, $v, 'array');
                    $this->setVar($k, $value);
                } else {
                    $value = $system->cleanVars($_POST, $k, $v, 'string');
                    $this->setVar($k, stripslashes($value));
                }
            }
        }
    }
}
