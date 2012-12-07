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
    {        extract( $this->_config );
        parent::__construct('', "form_preferences", "preferences.php", 'post', true);
        $this->setExtra('enctype="multipart/form-data"');
        $this->insertBreak(_XOO_CONFIG_MAINPAGE,'preferenceTitle');

        //welcome
        $this->addElement( new XoopsFormTextArea(_XOO_CONFIG_WELCOME, 'xoopartners_welcome', $xoopartners_welcome, 12, 12) );

        $this->CategoryForm();
        $this->PartnerForm();
        $this->rldForm();
        $this->QRcodeForm();

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

    private function CategoryForm()
    {
        extract( $this->_config );
        // Category
        $this->insertBreak(_XOO_CONFIG_CATEGORY,'preferenceTitle');

        // use category
        $this->addElement( new XoopsFormRadioYN(_XOO_CONFIG_USE_CATEGORIES, 'xoopartners_category[use_categories]', $xoopartners_category['use_categories']) );

        // main menu
        $this->addElement( new XoopsFormRadioYN(_XOO_CONFIG_MAIN_MENU, 'xoopartners_category[main_menu]', $xoopartners_category['main_menu']) );

        // Category mode
        $category_mode = new XoopsFormSelect(_XOO_CONFIG_CATEGORY_MODE, 'xoopartners_category[display_mode]', $xoopartners_category['display_mode']);
        $category_mode->addOption('list',   _XOO_CONFIG_MODE_LIST);
        $category_mode->addOption('table',  _XOO_CONFIG_MODE_TABLE);
        $category_mode->addOption('select', _XOO_CONFIG_MODE_SELECT);
        $category_mode->addOption('images', _XOO_CONFIG_MODE_IMAGES);
        $this->addElement( $category_mode );

        // image_size
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_SIZE, 'xoopartners_category[image_size]', 1, 10, $xoopartners_category['image_size']) );
        // image_width
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_WIDTH, 'xoopartners_category[image_width]', 1, 10, $xoopartners_category['image_width']) );
        // image_height
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_HEIGHT, 'xoopartners_category[image_height]', 1, 10, $xoopartners_category['image_height']) );
    }

    private function PartnerForm()
    {        extract( $this->_config );
        // Partner
        $this->insertBreak(_XOO_CONFIG_PARTNER,'preferenceTitle');

        // Partner mode
        $partner_mode = new XoopsFormSelect(_XOO_CONFIG_PARTNER_MODE, 'xoopartners_partner[display_mode]', $xoopartners_partner['display_mode']);
        $partner_mode->addOption('blog',   _XOO_CONFIG_MODE_BLOG);
        $partner_mode->addOption('images', _XOO_CONFIG_MODE_IMAGES);
        $partner_mode->addOption('list',   _XOO_CONFIG_MODE_LIST);
        $partner_mode->addOption('table',  _XOO_CONFIG_MODE_TABLE);
        $this->addElement( $partner_mode );

        // image_size
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_SIZE, 'xoopartners_partner[image_size]', 1, 10, $xoopartners_partner['image_size']) );
        // image_width
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_WIDTH, 'xoopartners_partner[image_width]', 1, 10, $xoopartners_partner['image_width']) );
        // image_height
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_HEIGHT, 'xoopartners_partner[image_height]', 1, 10, $xoopartners_partner['image_height']) );
    }

    private function rldForm()
    {
        extract( $this->_config );
        $this->insertBreak(_XOO_CONFIG_RLD,'preferenceTitle');
        // Rate / Like / Dislike Mode
        $rld_mode = new XoopsFormSelect(_XOO_CONFIG_RLD_MODE, 'xoopartners_rld[rld_mode]', $xoopartners_rld['rld_mode']);
        $rld_mode->addOption('none',        _XOO_CONFIG_RLD_NONE);
        $rld_mode->addOption('rate',        _XOO_CONFIG_RLD_RATE);
        $rld_mode->addOption('likedislike', _XOO_CONFIG_RLD_LIKEDISLIKE);
        $this->addElement( $rld_mode );

        $rate_scale = new XoopsFormSelect(_XOO_CONFIG_RATE_SCALE, 'xoopartners_rld[rate_scale]', $xoopartners_rld['rate_scale']);
        for ($i=4; $i <= 10; $i++) {
            $rate_scale->addOption($i, $i);
        }
        $this->addElement( $rate_scale );
    }

    private function QRcodeForm()
    {
        if ( file_exists(XOOPS_PATH . '/phpqrcode/qrlib.php') ) {
            $xoops = xoops::getinstance();
            $xoops->theme()->addScript('modules/xoopartners/include/qrcode.js');
            extract( $this->_config );
            $this->insertBreak(_XOO_CONFIG_QRCODE,'preferenceTitle');

            // use QR code
            $this->addElement( new XoopsFormRadioYN(_XOO_CONFIG_QRCODE_USE, 'xoopartners_qrcode[use_qrcode]', $xoopartners_qrcode['use_qrcode']) );

            // Error Correction Level
            $ecl_mode = new XoopsFormSelect(_XOO_CONFIG_QRCODE_ECL, 'xoopartners_qrcode[CorrectionLevel]', $xoopartners_qrcode['CorrectionLevel']);
            $ecl_mode->setExtra( "onchange='showImgQRcode(\"image_qrcode\", \"" . 'xoopartners' . "\", \"url=http://dugris.xoofoo.org\", \"" . $xoops->url('modules') . "\")'" );
            $ecl_mode->addOption(0,   _XOO_CONFIG_QRCODE_ECL_L);
            $ecl_mode->addOption(1,   _XOO_CONFIG_QRCODE_ECL_M);
            $ecl_mode->addOption(2,   _XOO_CONFIG_QRCODE_ECL_Q);
            $ecl_mode->addOption(3,   _XOO_CONFIG_QRCODE_ECL_H);
            $this->addElement( $ecl_mode );

            // Matrix Point Size
            $matrix_mode = new XoopsFormSelect(_XOO_CONFIG_QRCODE_MATRIX, 'xoopartners_qrcode[matrixPointSize]', $xoopartners_qrcode['matrixPointSize']);
            $matrix_mode->setExtra( "onchange='showImgQRcode(\"image_qrcode\", \"" . 'xoopartners' . "\", \"url=http://dugris.xoofoo.org\", \"" . $xoops->url('modules') . "\")'" );
            for ($i = 1; $i <= 5; $i++) {
                $matrix_mode->addOption($i, $i);
            }
            $this->addElement( $matrix_mode );

            // Margin
            $margin_mode = new XoopsFormSelect(_XOO_CONFIG_QRCODE_MARGIN, 'xoopartners_qrcode[whiteMargin]', $xoopartners_qrcode['whiteMargin']);
            $margin_mode->setExtra( "onchange='showImgQRcode(\"image_qrcode\", \"" . 'xoopartners' . "\", \"url=http://dugris.xoofoo.org\", \"" . $xoops->url('modules') . "\")'" );
            for ($i = 0; $i <= 20; $i++) {
                $margin_mode->addOption($i,   $i);
            }
            $this->addElement( $margin_mode );

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

            $this->addElement( $colors_tray );
        } else {
            $this->addElement( new XoopsFormHidden('xoopartners_qrcode[use_qrcode]', 0) );
        }
    }
}
?>