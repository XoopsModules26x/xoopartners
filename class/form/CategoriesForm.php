<?php

namespace XoopsModules\Xoopartners\Form;

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

/**
 * Class CategoriesForm
 */
class CategoriesForm extends \Xoops\Form\ThemeForm
{
    /**
     * @param \XoopsModules\Xoopartners\Categories|\XoopsObject|null $obj
     */
    public function __construct(\XoopsModules\Xoopartners\Categories $obj = null)
    {
        $this->xoopsObject = $obj;
        $xoops = \Xoops::getInstance();

        $helper = \XoopsModules\Xoopartners\Helper::getInstance();
        $partnersConfig = $helper->loadConfig();
        $categoriesHandler = $helper->getHandler('Categories');

        if ($this->xoopsObject->isNew()) {
            parent::__construct(_AM_XOO_PARTNERS_CATEGORY_ADD, 'form_category', 'categories.php', 'post', true);
        } else {
            parent::__construct(_AM_XOO_PARTNERS_CATEGORY_EDIT . ' : ' . $this->xoopsObject->getVar('xoopartners_title'), 'form_category', 'categories.php', 'post', true);
        }
        $this->setExtra('enctype="multipart/form-data"');

        $tabTray = new \Xoops\Form\TabTray('', 'uniqueid');

        /**
         * Main
         */
        $tab1 = new \Xoops\Form\Tab(_XOO_TABFORM_MAIN, 'tabid-1');
        // Category Title
        $tab1->addElement(new \Xoops\Form\Text(_XOO_PARTNERS_TITLE, 'xoopartners_category_title', 12, 255, $this->xoopsObject->getVar('xoopartners_category_title')), true);

        // Category parent_id
        ob_start();
        $categoriesHandler->makeSelectBox('xoopartners_category_parent_id', $this->xoopsObject->getVar('xoopartners_category_parent_id'));
        $tab1->addElement(new \Xoops\Form\Label(_XOO_PARTNERS_CATEGORY_PARENT_ID, ob_get_contents()));
        ob_end_clean();

        // Category Description
        $tab1->addElement(new \Xoops\Form\TextArea(_XOO_PARTNERS_DESCRIPTION, 'xoopartners_category_description', $this->xoopsObject->getVar('xoopartners_category_description'), 7, 12));

        // image
        $upload_msg[] = _XOO_PARTNERS_IMAGE_SIZE . ' : ' . $partnersConfig['xoopartners_category']['image_size'];
        $upload_msg[] = _XOO_PARTNERS_IMAGE_WIDTH . ' : ' . $partnersConfig['xoopartners_category']['image_width'];
        $upload_msg[] = _XOO_PARTNERS_IMAGE_HEIGHT . ' : ' . $partnersConfig['xoopartners_category']['image_height'];

        $warning_tray = new \Xoops\Form\ElementTray($this->message($upload_msg, ''));
        $image_tray = new \Xoops\Form\ElementTray(_XOO_PARTNERS_IMAGE, '');

        $image_box = new \Xoops\Form\File('', 'xoopartners_category_image', 5000000);
        $image_box->setExtra("size ='70%'");
        $image_tray->addElement($image_box);
        $image_tray->addElement($warning_tray);

        $image_array = \XoopsLists:: getImgListAsArray(\XoopsBaseConfig::get('uploads-path') . '/xoopartners/categories/images');
        $image_select = new \Xoops\Form\Select('<br>', 'image_list', $this->xoopsObject->getVar('xoopartners_category_image'));
        $image_select->addOptionArray($image_array);
        $image_select->setExtra("onchange='showImgSelected(\"select_image\", \"image_list\", \"" . '/xoopartners/categories/images/' . '", "", "' . $xoops->url('uploads') . "\")'");
        $image_tray->addElement($image_select);
        $image_tray->addElement(
            new \Xoops\Form\Label(
                '',
                "<br><img src='" . $xoops->url('uploads') . '/xoopartners/categories/images/' . $this->xoopsObject->getVar('xoopartners_category_image')
                . "' name='select_image' id='select_image' alt=''>"
            )
        );
        $tab1->addElement($image_tray);

        // order
        $tab1->addElement(new \Xoops\Form\Text(_XOO_PARTNERS_ORDER, 'xoopartners_category_order', 1, 3, $this->xoopsObject->getVar('xoopartners_category_order')));

        // display
        $tab1->addElement(new \Xoops\Form\RadioYesNo(_XOO_PARTNERS_DISPLAY, 'xoopartners_category_online', $this->xoopsObject->getVar('xoopartners_category_online')));

        $tabTray->addElement($tab1);

        // hidden
        $this->addElement(new \Xoops\Form\Hidden('xoopartners_category_id', $this->xoopsObject->getVar('xoopartners_category_id')));
        $this->addElement(new \Xoops\Form\Hidden('xoopartners_category_partners', $this->xoopsObject->getVar('xoopartners_category_partners')));

        $this->addElement($tabTray);

        /**
         * Buttons
         */
        $buttonTray = new \Xoops\Form\ElementTray('', '');
        $buttonTray->addElement(new \Xoops\Form\Hidden('op', 'save'));

        $buttonSubmit = new \Xoops\Form\Button('', 'submit', \XoopsLocale::A_SUBMIT, 'submit');
        $buttonSubmit->setClass('btn btn-success');
        $buttonTray->addElement($buttonSubmit);

        $buttonReset = new \Xoops\Form\Button('', 'reset', \XoopsLocale::A_RESET, 'reset');
        $buttonReset->setClass('btn btn-warning');
        $buttonTray->addElement($buttonReset);

        $buttonCancel = new \Xoops\Form\Button('', 'cancel', \XoopsLocale::A_CANCEL, 'button');
        $buttonCancel->setExtra("onclick='javascript:history.go(-1);'");
        $buttonCancel->setClass('btn btn-danger');
        $buttonTray->addElement($buttonCancel);

        $this->addElement($buttonTray);
    }

    /**
     * @param        $msg
     * @param string $title
     * @param string $class
     * @return string
     */
    public function message($msg, $title = '', $class = 'errorMsg')
    {
        $ret = "<div class='" . $class . "'>";
        if ('' != $title) {
            $ret .= '<strong>' . $title . '</strong>';
        }
        if (is_array($msg) || is_object($msg)) {
            $ret .= implode('<br>', $msg);
        } else {
            $ret .= $msg;
        }
        $ret .= '</div>';

        return $ret;
    }
}
