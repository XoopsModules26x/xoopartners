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

defined('XOOPS_ROOT_PATH') or die('Restricted access');

class XoopartnersPartnersForm extends XoopsThemeForm
{
    /**
     * @param null $obj
     */
    public function __construct($obj = null)
    {        $system = System::getInstance();
        $category_id = $system->CleanVars($_REQUEST, 'category_id', 0, 'int');

        $this->xoopsObject = $obj;

        global $partners_handler, $categories_handler;
        $xoops = Xoops::getInstance();

        $xoopartners_module = Xoopartners::getInstance();
        $partners_config = $xoopartners_module->LoadConfig();

        if ($this->xoopsObject->isNew() ) {            $script = ($xoops->isAdminSide) ? 'partners.php' : 'joinpartners.php';
            $title  = ($xoops->isAdminSide) ? _AM_XOO_PARTNERS_ADD : _XOO_PARTNERS_JOIN;
            parent::__construct($title, 'form_partner', $script, 'post', true);
        } else {            parent::__construct(_AM_XOO_PARTNERS_EDIT . ' : ' . $this->xoopsObject->getVar('xoopartners_title'), 'form_partner', 'partners.php', 'post', true);
        }
        $this->setExtra('enctype="multipart/form-data"');

        $tabtray = new XoopsFormTabTray('', 'uniqueid');

        /**
         * Main
         */
        $tab1 = new XoopsFormTab(_XOO_TABFORM_MAIN, 'tabid-1');

        // Partner Title
        $tab1->addElement( new XoopsFormText(_XOO_PARTNERS_TITLE, 'xoopartners_title', 100, 255, $this->xoopsObject->getVar('xoopartners_title')) , true );

        // Partner Url
        $tab1->addElement( new XoopsFormUrl(_XOO_PARTNERS_URL, 'xoopartners_url', 100, 255, $this->xoopsObject->getVar('xoopartners_url')) , true );

        // Category
        if ($partners_config['xoopartners_category']['use_categories']) {
            ob_start();
            $categories_handler->makeSelectBox('xoopartners_category', $this->xoopsObject->getVar('xoopartners_category') );
            $tab1->addElement(new XoopsFormLabel(_XOO_PARTNERS_CATEGORY_TITLE, ob_get_contents()));
            ob_end_clean();
        } else {            $this->addElement( new XoopsFormHidden('xoopartners_category', 0) );
        }

        // submitter
        if ($xoops->isAdminSide) {
            $xoopartners_uid = $this->xoopsObject->isNew() ? $xoops->user->getVar('uid') : $this->xoopsObject->getVar('xoopartners_uid');
            $tab1->addElement(new XoopsFormSelectUser(_XOO_PARTNERS_AUTHOR, 'xoopartners_uid', true, $xoopartners_uid, 1, false));
        } else {
            $xoopartners_uid = $xoops->isUser() ? $xoops->user->getVar('uid') : 0;
            $this->addElement( new XoopsFormHidden('xoopartners_uid', $xoopartners_uid) );
        }

        // Partner Description
        $editor_configs=array();
        $editor_configs['name'] ='xoopartners_description';
        $editor_configs['value'] = $this->xoopsObject->getVar('xoopartners_description');
        $editor_configs['rows'] = 20;
        $editor_configs['cols'] = 6;
        $editor_configs['width'] = '100%';
        $editor_configs['height'] = '400px';
        if ($xoops->isAdminSide) {
            $editor_configs['editor'] = 'tinymce';
            $tab1->addElement( new XoopsFormEditor(_XOO_PARTNERS_DESCRIPTION, 'xoopartners_description', $editor_configs), true );
        } else {            $editor_configs['editor'] = 'dhtmltextarea';
            $tab1->addElement( new XoopsFormEditor(_XOO_PARTNERS_DESCRIPTION, 'xoopartners_description', $editor_configs), true );
        }

        // image
        $upload_msg[] = _XOO_PARTNERS_IMAGE_SIZE . ' : ' . $partners_config['xoopartners_partner']['image_size'];
        $upload_msg[] = _XOO_PARTNERS_IMAGE_WIDTH . ' : ' . $partners_config['xoopartners_partner']['image_width'];
        $upload_msg[] = _XOO_PARTNERS_IMAGE_HEIGHT . ' : ' . $partners_config['xoopartners_partner']['image_height'];

        $warning_tray = new XoopsFormElementTray($this->message($upload_msg, '') );
        $image_tray = new XoopsFormElementTray(_XOO_PARTNERS_IMAGE, '' );

        $image_box = new XoopsFormFile('', 'xoopartners_image', 5000000);
        $image_box->setExtra( "size ='70%'") ;
        $image_tray->addElement( $image_box );
        $image_tray->addElement( $warning_tray );

        $image_array = XoopsLists :: getImgListAsArray( $xoops->path('uploads') . '/xoopartners/partners/images' );
        $image_select = new XoopsFormSelect( '<br />', 'image_list', $this->xoopsObject->getVar('xoopartners_image') );
        $image_select->addOptionArray( $image_array );
        $image_select->setExtra( "onchange='showImgSelected(\"select_image\", \"image_list\", \"" . '/xoopartners/partners/images/' . "\", \"\", \"" . $xoops->url('uploads') . "\")'" );
        $image_tray->addElement( $image_select );
        $image_tray->addElement( new XoopsFormLabel( '', "<br /><img src='" . $xoops->url('uploads') . '/xoopartners/partners/images/' . $this->xoopsObject->getVar('xoopartners_image') . "' name='select_image' id='select_image' alt='' />" ) );
        $tab1->addElement( $image_tray );

        $tabtray->addElement($tab1);

        /**
         * Options
         */
        if ($xoops->isAdminSide) {
            $tab3 = new XoopsFormTab(_XOO_TABFORM_OPTIONS, 'tabid-3');
            // order
            $tab3->addElement( new XoopsFormText(_XOO_PARTNERS_ORDER, 'xoopartners_order', 1, 3, $this->xoopsObject->getVar('xoopartners_order')) );
            // display
            $tab3->addElement( new XoopsFormRadioYN(_XOO_PARTNERS_DISPLAY, 'xoopartners_online',  $this->xoopsObject->getVar('xoopartners_online')) );
            // accepted
            $tab3->addElement( new XoopsFormRadioYN(_XOO_PARTNERS_ACCEPTED_YES, 'xoopartners_accepted',  $this->xoopsObject->getVar('xoopartners_accepted')) );
            // Published date
            $published = ($this->xoopsObject->getVar('xoopartners_published') == 0) ? time() : $this->xoopsObject->getVar('xoopartners_published');
            $tab3->addElement( new XoopsFormDateTime(_XOO_PARTNERS_PUBLISHED, 'xoopartners_published', 2, $published, false) );
            $tabtray->addElement($tab3);
        } else {            $this->addElement( new XoopsFormHidden('xoopartners_order', $this->xoopsObject->getVar('xoopartners_order')) );
            $this->addElement( new XoopsFormHidden('xoopartners_online', $this->xoopsObject->getVar('xoopartners_online')) );
            $this->addElement( new XoopsFormHidden('xoopartners_accepted', $this->xoopsObject->getVar('xoopartners_accepted')) );
            $this->addElement( new XoopsFormHidden('xoopartners_published', time() ) );
        }

        /**
         * Tags
         */
        if ( $xoops->registry()->offsetExists('XOOTAGS') && $xoops->registry()->get('XOOTAGS') ) {
            if ($xoops->isAdminSide) {
                $tab4 = new XoopsFormTab(_XOO_TABFORM_TAGS, 'tabid-4');
                $TagForm_handler = $xoops->getModuleForm(0, 'tags', 'xootags');
                $tagform = $TagForm_handler->TagsForm( 'tags', $this->xoopsObject->getVar('xoopartners_id'));
                $tab4->addElement( $tagform );
                $tabtray->addElement($tab4);
            } else {
                $this->addElement( new XoopsFormHidden('tags', '') );
            }
        }

        // hidden
        $this->addElement( new XoopsFormHidden('xoopartners_id', $this->xoopsObject->getVar('xoopartners_id')) );
        $this->addElement( new XoopsFormHidden('category_id', $category_id) );
        $this->addElement( new XoopsFormHidden('xoopartners_rates', $this->xoopsObject->getVar('xoopartners_rates')) );
        $this->addElement( new XoopsFormHidden('xoopartners_like', $this->xoopsObject->getVar('xoopartners_like')) );
        $this->addElement( new XoopsFormHidden('xoopartners_dislike', $this->xoopsObject->getVar('xoopartners_dislike')) );

        $this->addElement($tabtray);

        /**
         * Buttons
         */
        $button_tray = new XoopsFormElementTray('', '');
        $button_tray->addElement(new XoopsFormHidden('op', 'save'));

        $button = new XoopsFormButton('', 'submit', _SUBMIT, 'submit');
        $button->setClass('btn btn-success');
        $button_tray->addElement($button);

        $button_2 = new XoopsFormButton('', 'reset', _RESET, 'reset');
        $button_2->setClass('btn btn-warning');
        $button_tray->addElement($button_2);

        $button_3 = new XoopsFormButton('', 'cancel', _CANCEL, 'button');
        $button_3->setExtra("onclick='javascript:history.go(-1);'");
        $button_3->setClass('btn btn-danger');
        $button_tray->addElement($button_3);

        $this->addElement($button_tray);
    }

    public function message($msg, $title = '', $class='errorMsg' )
    {
        $ret = "<div class='" . $class . "'>";
        if ( $title != '' ) {
            $ret .= "<strong>" . $title . "</strong>";
        }
        if ( is_array( $msg ) || is_object( $msg ) ) {
            $ret .= implode('<br />', $msg);
        } else {
            $ret .= $msg;
        }
        $ret .= "</div>";
        return $ret;
    }
}
?>