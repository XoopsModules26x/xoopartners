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

class XoopartnersPreferencesForm extends XoopsThemeForm
{
    private $_colors = array(
        'Aqua'    => '#00FFFF',
        'Black'   => '#000000',
        'Blue'    => '#0000FF',
        'Fuchsia' => '#FF00FF',
        'Gray'    => '#808080',
        'Green'   => '#008000',
        'Lime'    => '#00FF00',
        'Maroon'  => '#800000',
        'Navy'    => '#000080',
        'Olive'   => '#808000',
        'Purple'  => '#800080',
        'Red'     => '#FF0000',
        'Silver'  => '#C0C0C0',
        'Teal'    => '#008080',
        'White'   => '#FFFFFF',
        'Yellow'  => '#FFFF00',
    );

    private $_config = array();
    /**
     * @param null $obj
     */
    public function __construct()
    {        $this->_config = XooPartnersPreferences::getInstance()->loadConfig();
    }

    /**
     * Maintenance Form
     * @return void
     */
    public function PreferencesForm()
    {        $xoops = Xoops::getinstance();
        extract( $this->_config );
        parent::__construct('', 'form_preferences', 'preferences.php', 'post', true);
        $this->setExtra('enctype="multipart/form-data"');

        $tabtray = new XoopsFormTabTray('', 'uniqueid');

        /**
         * Main page
         */
        //welcome
        $tab1 = new XoopsFormTab(_XOO_CONFIG_MAINPAGE, 'tabid-1');
        $tab1->addElement( new XoopsFormTextArea(_XOO_CONFIG_WELCOME, 'xoopartners_welcome', $xoopartners_welcome, 12, 12) );

        $tabtray->addElement($tab1);
        $tabtray->addElement( $this->CategoryForm() );
        $tabtray->addElement( $this->PartnerForm() );
        $tabtray->addElement( $this->rldForm() );
        if ( $xoops->isActiveModule('qrcode') ) {
            $tabtray->addElement( $this->QRcodeForm() );
        } else {
            $this->addElement( new XoopsFormHidden('xoopartners_qrcode[use_qrcode]', 0) );
        }

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

    /**
     * Categories
     */
    private function CategoryForm()
    {
        extract( $this->_config );
        $tab2 = new XoopsFormTab(_XOO_CONFIG_CATEGORY, 'tabid-2');

        // use category
        $tab2->addElement( new XoopsFormRadioYN(_XOO_CONFIG_USE_CATEGORIES, 'xoopartners_category[use_categories]', $xoopartners_category['use_categories']) );

        // main menu
        $tab2->addElement( new XoopsFormRadioYN(_XOO_CONFIG_MAIN_MENU, 'xoopartners_category[main_menu]', $xoopartners_category['main_menu']) );

        // Category mode
        $category_mode = new XoopsFormSelect(_XOO_CONFIG_CATEGORY_MODE, 'xoopartners_category[display_mode]', $xoopartners_category['display_mode']);
        $category_mode->addOption('list',   _XOO_CONFIG_MODE_LIST);
        $category_mode->addOption('table',  _XOO_CONFIG_MODE_TABLE);
        $category_mode->addOption('select', _XOO_CONFIG_MODE_SELECT);
        $category_mode->addOption('images', _XOO_CONFIG_MODE_IMAGES);
        $tab2->addElement( $category_mode );

        // image_size
        $tab2->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_SIZE, 'xoopartners_category[image_size]', 1, 10, $xoopartners_category['image_size']) );
        // image_width
        $tab2->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_WIDTH, 'xoopartners_category[image_width]', 1, 10, $xoopartners_category['image_width']) );
        // image_height
        $tab2->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_HEIGHT, 'xoopartners_category[image_height]', 1, 10, $xoopartners_category['image_height']) );

        return $tab2;
    }

    /**
     * Partners
     */
    private function PartnerForm()
    {        extract( $this->_config );
        $tab3 = new XoopsFormTab(_XOO_CONFIG_PARTNER, 'tabid-3');

        // Partner mode
        $partner_mode = new XoopsFormSelect(_XOO_CONFIG_PARTNER_MODE, 'xoopartners_partner[display_mode]', $xoopartners_partner['display_mode']);
        $partner_mode->addOption('blog',   _XOO_CONFIG_MODE_BLOG);
        $partner_mode->addOption('images', _XOO_CONFIG_MODE_IMAGES);
        $partner_mode->addOption('list',   _XOO_CONFIG_MODE_LIST);
        $partner_mode->addOption('table',  _XOO_CONFIG_MODE_TABLE);
        $tab3->addElement( $partner_mode );

        // limit per page
        $tab3->addElement( new XoopsFormText(_XOO_CONFIG_LIMIT_MAIN, 'xoopartners_partner[limit_main]', 1, 10, $xoopartners_partner['limit_main']) );

        // image_size
        $tab3->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_SIZE, 'xoopartners_partner[image_size]', 1, 10, $xoopartners_partner['image_size']) );
        // image_width
        $tab3->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_WIDTH, 'xoopartners_partner[image_width]', 1, 10, $xoopartners_partner['image_width']) );
        // image_height
        $tab3->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_HEIGHT, 'xoopartners_partner[image_height]', 1, 10, $xoopartners_partner['image_height']) );

        return $tab3;
    }

    /**
     * Rate / Like - Dislike
     */
    private function rldForm()
    {
        $tab4 = new XoopsFormTab(_XOO_CONFIG_RLD, 'tabid-4');
        extract( $this->_config );

        // Rate / Like / Dislike Mode
        $rld_mode = new XoopsFormSelect(_XOO_CONFIG_RLD_MODE, 'xoopartners_rld[rld_mode]', $xoopartners_rld['rld_mode']);
        $rld_mode->addOption('none',        _XOO_CONFIG_RLD_NONE);
        $rld_mode->addOption('rate',        _XOO_CONFIG_RLD_RATE);
        $rld_mode->addOption('likedislike', _XOO_CONFIG_RLD_LIKEDISLIKE);
        $tab4->addElement( $rld_mode );

        $rate_scale = new XoopsFormSelect(_XOO_CONFIG_RATE_SCALE, 'xoopartners_rld[rate_scale]', $xoopartners_rld['rate_scale']);
        for ($i=4; $i <= 10; $i++) {
            $rate_scale->addOption($i, $i);
        }
        $tab4->addElement( $rate_scale );
        return $tab4;
    }

    /**
     * QR Code
     */
    private function QRcodeForm()
    {
        $tab5 = new XoopsFormTab(_XOO_CONFIG_QRCODE, 'tabid-5');
        $xoops = Xoops::getinstance();
        $xoops->theme()->addScript('modules/xoopartners/include/qrcode.js');
        extract( $this->_config );

        // use QR code
        $tab5->addElement( new XoopsFormRadioYN(_XOO_CONFIG_QRCODE_USE, 'xoopartners_qrcode[use_qrcode]', $xoopartners_qrcode['use_qrcode']) );

        // Error Correction Level
        $ecl_mode = new XoopsFormSelect(_XOO_CONFIG_QRCODE_ECL, 'xoopartners_qrcode[CorrectionLevel]', $xoopartners_qrcode['CorrectionLevel']);
        $ecl_mode->setExtra( "onchange='showImgQRcode(\"image_qrcode\", \"" . 'xoopartners' . "\", \"url=http://dugris.xoofoo.org\", \"" . $xoops->url('modules') . "\")'" );
        $ecl_mode->addOption(0,   _XOO_CONFIG_QRCODE_ECL_L);
        $ecl_mode->addOption(1,   _XOO_CONFIG_QRCODE_ECL_M);
        $ecl_mode->addOption(2,   _XOO_CONFIG_QRCODE_ECL_Q);
        $ecl_mode->addOption(3,   _XOO_CONFIG_QRCODE_ECL_H);
        $tab5->addElement( $ecl_mode );

        // Matrix Point Size
        $matrix_mode = new XoopsFormSelect(_XOO_CONFIG_QRCODE_MATRIX, 'xoopartners_qrcode[matrixPointSize]', $xoopartners_qrcode['matrixPointSize']);
        $matrix_mode->setExtra( "onchange='showImgQRcode(\"image_qrcode\", \"" . 'xoopartners' . "\", \"url=http://dugris.xoofoo.org\", \"" . $xoops->url('modules') . "\")'" );
        for ($i = 1; $i <= 5; $i++) {
            $matrix_mode->addOption($i, $i);
        }
        $tab5->addElement( $matrix_mode );

        // Margin
        $margin_mode = new XoopsFormSelect(_XOO_CONFIG_QRCODE_MARGIN, 'xoopartners_qrcode[whiteMargin]', $xoopartners_qrcode['whiteMargin']);
        $margin_mode->setExtra( "onchange='showImgQRcode(\"image_qrcode\", \"" . 'xoopartners' . "\", \"url=http://dugris.xoofoo.org\", \"" . $xoops->url('modules') . "\")'" );
        for ($i = 0; $i <= 20; $i++) {
            $margin_mode->addOption($i,   $i);
        }
        $tab5->addElement( $margin_mode );

        // Background & Foreground Color
        $colors_tray = new XoopsFormElementTray(_XOO_CONFIG_QRCODE_COLORS, '' );

        $colors_bg = new XoopsFormSelect(_XOO_CONFIG_QRCODE_COLORS_BG . ': ', 'xoopartners_qrcode[backgroundColor]', $xoopartners_qrcode['backgroundColor'], 1);
        $colors_bg->setExtra( "onchange='showImgQRcode(\"image_qrcode\", \"" . 'xoopartners' . "\", \"url=http://dugris.xoofoo.org\", \"" . $xoops->url('modules') . "\")'" );

        $colors_fg = new XoopsFormSelect(_XOO_CONFIG_QRCODE_COLORS_FG . ': ', 'xoopartners_qrcode[foregroundColor]', $xoopartners_qrcode['foregroundColor'], 1);
        $colors_fg->setExtra( "onchange='showImgQRcode(\"image_qrcode\", \"" . 'xoopartners' . "\", \"url=http://dugris.xoofoo.org\", \"" . $xoops->url('modules') . "\")'" );

        foreach ( $this->_colors as $k => $color ) {
            $colors_bg->addOption( $k );
            $colors_fg->addOption( $k );
        }
        $colors_tray->addElement( new XoopsFormLabel( '', "<div class='floatright'><img src='" . $xoops->url('/modules/xoopartners/') . "qrcode.php?url=http://dugris.xoofoo.org' name='image_qrcode' id='image_qrcode' alt='" . _XOO_CONFIG_QRCODE . "' /></div>" ) );
        $colors_tray->addElement( $colors_bg );
        $colors_tray->addElement( new XoopsFormLabel( '', '<br />') );
        $colors_tray->addElement( $colors_fg );

        $tab5->addElement( $colors_tray );
        return $tab5;
    }
}
?>