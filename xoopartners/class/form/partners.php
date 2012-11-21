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
    {        $this->xoopsObject = $obj;
    }

    /**
     * Maintenance Form
     * @return void
     */
    public function PartnerForm( $category_id = 0 )
    {        global $partners_handler, $categories_handler;
        $xoops = Xoops::getInstance();
        $Partners_config = XooPartnersPreferences::getInstance()->loadConfig();

        if ($this->xoopsObject->isNew() ) {            $script = ($xoops->isAdminSide) ? 'partners.php' : 'joinpartners.php';
            $title  = ($xoops->isAdminSide) ? _AM_XOO_PARTNERS_ADD : _XOO_PARTNERS_JOIN;
            parent::__construct($title, 'form_partner', $script, 'post', true);
        } else {            parent::__construct(_AM_XOO_PARTNERS_EDIT . ' : ' . $this->xoopsObject->getVar('xoopartners_title'), 'form_partner', 'partners.php', 'post', true);
        }
        $this->setExtra('enctype="multipart/form-data"');

        // Partner Title
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_TITLE, 'xoopartners_title', 100, 255, $this->xoopsObject->getVar('xoopartners_title')) , true );

        // Partner Url
        $this->addElement( new XoopsFormUrl(_XOO_PARTNERS_URL, 'xoopartners_url', 100, 255, $this->xoopsObject->getVar('xoopartners_url')) , true );

        // Category
        if ($Partners_config['xoopartners_category']['use_categories']) {
            ob_start();
            $categories_handler->makeSelectBox('xoopartners_category', $this->xoopsObject->getVar('xoopartners_category') );
            $this->addElement(new XoopsFormLabel(_XOO_PARTNERS_CATEGORY_TITLE, ob_get_contents()));
            ob_end_clean();
        } else {            $this->addElement( new XoopsFormHidden('xoopartners_category', 0) );
        }

        // Partner Description
        if ($xoops->isAdminSide) {
            $this->addElement( new XoopsFormTextArea(_XOO_PARTNERS_DESCRIPTION, 'xoopartners_description', $this->xoopsObject->getVar('xoopartners_description'), 7, 50));
        } else {            $this->addElement( new XoopsFormTextArea(_XOO_PARTNERS_DESCRIPTION, 'xoopartners_description', $this->xoopsObject->getVar('xoopartners_description'), 7, 50), true);
        }

        // image
        $upload_msg[] = _XOO_PARTNERS_IMAGE_SIZE . ' : ' . $Partners_config['xoopartners_partner']['image_size'];
        $upload_msg[] = _XOO_PARTNERS_IMAGE_WIDTH . ' : ' . $Partners_config['xoopartners_partner']['image_width'];
        $upload_msg[] = _XOO_PARTNERS_IMAGE_HEIGHT . ' : ' . $Partners_config['xoopartners_partner']['image_height'];

        $image_tray = new XoopsFormElementTray(_XOO_PARTNERS_IMAGE, '' );
        $image_tray->setDescription( $this->message($upload_msg) );
        $image_box = new XoopsFormFile('', 'xoopartners_image', 5000000);
        $image_box->setExtra( "size ='70%'") ;
        $image_tray->addElement( $image_box );

        $image_array = XoopsLists :: getImgListAsArray( $xoops->path('uploads') . '/xoopartners/partners/images' );
        $image_select = new XoopsFormSelect( '<br />', 'image_list', $this->xoopsObject->getVar('xoopartners_image') );
        $image_select->addOptionArray( $image_array );
        $image_select->setExtra( "onchange='showImgSelected(\"select_image\", \"image_list\", \"" . '/xoopartners/partners/images/' . "\", \"\", \"" . $xoops->url('uploads') . "\")'" );
        $image_tray->addElement( $image_select );
        $image_tray->addElement( new XoopsFormLabel( '', "<br /><img src='" . $xoops->url('uploads') . '/xoopartners/partners/images/' . $this->xoopsObject->getVar('xoopartners_image') . "' name='select_image' id='select_image' alt='' />" ) );
        $this->addElement( $image_tray );

        // order
        if ($xoops->isAdminSide) {
            $this->addElement( new XoopsFormText(_XOO_PARTNERS_ORDER, 'xoopartners_order', 1, 3, $this->xoopsObject->getVar('xoopartners_order')) );
        } else {            $this->addElement( new XoopsFormHidden('xoopartners_order', $this->xoopsObject->getVar('xoopartners_order')) );
        }

        // display
        if ($xoops->isAdminSide) {
            $this->addElement( new XoopsFormRadioYN(_XOO_PARTNERS_DISPLAY, 'xoopartners_online',  $this->xoopsObject->getVar('xoopartners_online')) );
        } else {
            $this->addElement( new XoopsFormHidden('xoopartners_online', $this->xoopsObject->getVar('xoopartners_online')) );
        }

        // accepted
        if ($xoops->isAdminSide) {
            $this->addElement( new XoopsFormRadioYN(_XOO_PARTNERS_ACCEPTED_YES, 'xoopartners_accepted',  $this->xoopsObject->getVar('xoopartners_accepted')) );
        } else {
            $this->addElement( new XoopsFormHidden('xoopartners_accepted', $this->xoopsObject->getVar('xoopartners_accepted')) );
        }

        // hidden
        $this->addElement( new XoopsFormHidden('xoopartners_id', $this->xoopsObject->getVar('xoopartners_id')) );
        $this->addElement( new XoopsFormHidden('category_id', $category_id) );
        $this->addElement( new XoopsFormHidden('xoopartners_rates', $this->xoopsObject->getVar('xoopartners_rates')) );
        $this->addElement( new XoopsFormHidden('xoopartners_like', $this->xoopsObject->getVar('xoopartners_like')) );
        $this->addElement( new XoopsFormHidden('xoopartners_dislike', $this->xoopsObject->getVar('xoopartners_dislike')) );

        // button
        $button_tray = new XoopsFormElementTray('', '');
        $button_tray->addElement(new XoopsFormHidden('op', 'save'));
        $button_tray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $button_tray->addElement(new XoopsFormButton('', 'reset', _RESET, 'reset'));
        $cancel_send = new XoopsFormButton('', 'cancel', _CANCEL, 'button');
        $cancel_send->setExtra("onclick='javascript:history.go(-1);'");
        $button_tray->addElement($cancel_send);
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