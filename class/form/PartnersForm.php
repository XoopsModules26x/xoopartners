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
use Xoops\Core\Request;
use XoopsModules\Xoopartners\Partners;

/**
 * Class PartnersForm
 */
class PartnersForm extends \Xoops\Form\ThemeForm
{
    /**
     * @param Partners|\XoopsObject|null $obj
     */
    public function __construct(Partners $obj = null)
    {
        $system = \System::getInstance();
        $category_id = Request::getInt('category_id', 0); //$system->cleanVars($_REQUEST, 'category_id', 0, 'int');

        $this->xoopsObject = $obj;

        global $partnersHandler, $categoriesHandler;
        $xoops = \Xoops::getInstance();

        $helper = \XoopsModules\Xoopartners\Helper::getInstance();
        $partnersConfig = $helper->loadConfig();

        if ($this->xoopsObject->isNew()) {
            $script = ($xoops->isAdminSide) ? 'partners.php' : 'joinpartners.php';
            $title = ($xoops->isAdminSide) ? _AM_XOO_PARTNERS_ADD : _XOO_PARTNERS_JOIN;
            parent::__construct($title, 'form_partner', $script, 'post', true);
        } else {
            parent::__construct(_AM_XOO_PARTNERS_EDIT . ' : ' . $this->xoopsObject->getVar('xoopartners_title'), 'form_partner', 'partners.php', 'post', true);
        }
        $this->setExtra('enctype="multipart/form-data"');

        $tabTray = new \Xoops\Form\TabTray('', 'uniqueid');

        /**
         * Main
         */
        $tab1 = new \Xoops\Form\Tab(_XOO_TABFORM_MAIN, 'tabid-1');

        // Partner Title
        $tab1->addElement(new \Xoops\Form\Text(_XOO_PARTNERS_TITLE, 'xoopartners_title', 12, 255, $this->xoopsObject->getVar('xoopartners_title')), true);

        // Partner Url
        $tab1->addElement(new \Xoops\Form\Url(_XOO_PARTNERS_URL, 'xoopartners_url', 12, 255, $this->xoopsObject->getVar('xoopartners_url')), true);

        // Category
        if ($partnersConfig['xoopartners_category']['use_categories']) {
            ob_start();
            $categoriesHandler->makeSelectBox('xoopartners_category', $this->xoopsObject->getVar('xoopartners_category'));
            $tab1->addElement(new \Xoops\Form\Label(_XOO_PARTNERS_CATEGORY_TITLE, ob_get_contents()));
            ob_end_clean();
        } else {
            $this->addElement(new \Xoops\Form\Hidden('xoopartners_category', 0));
        }

        // submitter
        if ($xoops->isAdminSide) {
            $xoopartners_uid = $this->xoopsObject->isNew() ? $xoops->user->getVar('uid') : $this->xoopsObject->getVar('xoopartners_uid');
            $tab1->addElement(new \Xoops\Form\SelectUser(_XOO_PARTNERS_AUTHOR, 'xoopartners_uid', true, $xoopartners_uid, 1, false));
        } else {
            $xoopartners_uid = $xoops->isUser() ? $xoops->user->getVar('uid') : 0;
            $this->addElement(new \Xoops\Form\Hidden('xoopartners_uid', $xoopartners_uid));
        }

        // Partner Description
        $editorConfigs = [];
        $editorConfigs['name'] = 'xoopartners_description';
        $editorConfigs['value'] = $this->xoopsObject->getVar('xoopartners_description');
        $editorConfigs['rows'] = 20;
        $editorConfigs['cols'] = 12;
        $editorConfigs['width'] = '100%';
        $editorConfigs['height'] = '400px';
        if ($xoops->isAdminSide) {
            $editorConfigs['editor'] = 'tinymce';
            $tab1->addElement(new \Xoops\Form\Editor(_XOO_PARTNERS_DESCRIPTION, 'xoopartners_description', $editorConfigs), true);
        } else {
            $editorConfigs['editor'] = 'dhtmltextarea';
            $tab1->addElement(new \Xoops\Form\Editor(_XOO_PARTNERS_DESCRIPTION, 'xoopartners_description', $editorConfigs), true);
        }

        // image
        $upload_msg[] = _XOO_PARTNERS_IMAGE_SIZE . ' : ' . $partnersConfig['xoopartners_partner']['image_size'];
        $upload_msg[] = _XOO_PARTNERS_IMAGE_WIDTH . ' : ' . $partnersConfig['xoopartners_partner']['image_width'];
        $upload_msg[] = _XOO_PARTNERS_IMAGE_HEIGHT . ' : ' . $partnersConfig['xoopartners_partner']['image_height'];

        $warning_tray = new \Xoops\Form\ElementTray($this->message($upload_msg, ''));
        $image_tray = new \Xoops\Form\ElementTray(_XOO_PARTNERS_IMAGE, '');

        $image_box = new \Xoops\Form\File('', 'xoopartners_image', 5000000);
        $image_box->setExtra("size ='70%'");
        $image_tray->addElement($image_box);
        $image_tray->addElement($warning_tray);

        $image_array = \XoopsLists:: getImgListAsArray(\XoopsBaseConfig::get('uploads-path') . '/xoopartners/partners/images');
        $image_select = new \Xoops\Form\Select('<br>', 'image_list', $this->xoopsObject->getVar('xoopartners_image'));
        $image_select->addOptionArray($image_array);
        $image_select->setExtra("onchange='showImgSelected(\"select_image\", \"image_list\", \"" . '/xoopartners/partners/images/' . '", "", "' . $xoops->url('uploads') . "\")'");
        $image_tray->addElement($image_select);
        $image_tray->addElement(
            new \Xoops\Form\Label(
                '',
                "<br><img src='" . $xoops->url('uploads') . '/xoopartners/partners/images/' . $this->xoopsObject->getVar('xoopartners_image') . "' name='select_image' id='select_image' alt=''>"
            )
        );
        $tab1->addElement($image_tray);

        $tabTray->addElement($tab1);

        /**
         * Options
         */
        if ($xoops->isAdminSide) {
            $tab3 = new \Xoops\Form\Tab(_XOO_TABFORM_OPTIONS, 'tabid-3');
            // order
            $tab3->addElement(new \Xoops\Form\Text(_XOO_PARTNERS_ORDER, 'xoopartners_order', 1, 3, $this->xoopsObject->getVar('xoopartners_order')));
            // display
            $tab3->addElement(new \Xoops\Form\RadioYesNo(_XOO_PARTNERS_DISPLAY, 'xoopartners_online', $this->xoopsObject->getVar('xoopartners_online')));
            // accepted
            $tab3->addElement(new \Xoops\Form\RadioYesNo(_XOO_PARTNERS_ACCEPTED_YES, 'xoopartners_accepted', $this->xoopsObject->getVar('xoopartners_accepted')));
            // Published date
            $published = (0 == $this->xoopsObject->getVar('xoopartners_published')) ? time() : $this->xoopsObject->getVar('xoopartners_published');
            $tab3->addElement(new \Xoops\Form\DateTimeSelect(_XOO_PARTNERS_PUBLISHED, 'xoopartners_published', 2, $published, false));
            $tabTray->addElement($tab3);
        } else {
            $this->addElement(new \Xoops\Form\Hidden('xoopartners_order', $this->xoopsObject->getVar('xoopartners_order')));
            $this->addElement(new \Xoops\Form\Hidden('xoopartners_online', $this->xoopsObject->getVar('xoopartners_online')));
            $this->addElement(new \Xoops\Form\Hidden('xoopartners_accepted', $this->xoopsObject->getVar('xoopartners_accepted')));
            $this->addElement(new \Xoops\Form\Hidden('xoopartners_published', time()));
        }

        /**
         * Tags
         */
        if ($xoops->registry()->offsetExists('XOOTAGS') && $xoops->registry()->get('XOOTAGS')) {
            if ($xoops->isAdminSide) {
                $tagsTray = new \Xoops\Form\Tab(_XOO_TABFORM_TAGS, 'tabid-tags');
//                $tagFormHandler = $xoops->getModuleForm(0, 'tags', 'xootags');
//                $tagform = $tagFormHandler->tagForm('tags', $this->xoopsObject->getVar('xoopartners_id'));


                $tagsForm = new \XoopsModules\Xootags\Form\TagsForm();
                $tagform = $tagsForm->tagForm('tags', $this->xoopsObject->getVar('xoopartners_id'));

                $tagsTray->addElement($tagform);
                $tabTray->addElement($tagsTray);
            } else {
                $this->addElement(new \Xoops\Form\Hidden('tags', ''));
            }
        }

        // hidden
        $this->addElement(new \Xoops\Form\Hidden('xoopartners_id', $this->xoopsObject->getVar('xoopartners_id')));
        $this->addElement(new \Xoops\Form\Hidden('category_id', $category_id));
        $this->addElement(new \Xoops\Form\Hidden('xoopartners_rates', $this->xoopsObject->getVar('xoopartners_rates')));
        $this->addElement(new \Xoops\Form\Hidden('xoopartners_like', $this->xoopsObject->getVar('xoopartners_like')));
        $this->addElement(new \Xoops\Form\Hidden('xoopartners_dislike', $this->xoopsObject->getVar('xoopartners_dislike')));
        $this->addElement(new \Xoops\Form\Hidden('xoopartners_comments', $this->xoopsObject->getVar('xoopartners_comments')));

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
