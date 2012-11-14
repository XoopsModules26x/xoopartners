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
        $this->insertBreak(_MI_XOO_PARTNERS_CONFIG_MAINPAGE,'preferenceTitle');

        //welcome
        $this->addElement( new XoopsFormTextArea(_MI_XOO_PARTNERS_CONFIG_WELCOME, 'xoopartners_welcome', $xoopartners_welcome, 12, 12) );

        // Category
        $this->insertBreak(_MI_XOO_PARTNERS_CONFIG_CATEGORY,'preferenceTitle');

        // use category
        $this->addElement( new XoopsFormRadioYN(_MI_XOO_PARTNERS_CONFIG_USE_CATEGORIES, 'xoopartners_category[use_categories]', $xoopartners_category['use_categories']) );

        // main menu
        $this->addElement( new XoopsFormRadioYN(_MI_XOO_PARTNERS_CONFIG_MAIN_MENU, 'xoopartners_category[main_menu]', $xoopartners_category['main_menu']) );

        // Category mode
        $category_mode = new XoopsFormSelect(_MI_XOO_PARTNERS_CONFIG_CATEGORY_MODE, 'xoopartners_category[display_mode]', $xoopartners_category['display_mode']);
        $category_mode->addOption('list',   _MI_XOO_PARTNERS_CONFIG_MODE_LIST);
        $category_mode->addOption('table',  _MI_XOO_PARTNERS_CONFIG_MODE_TABLE);
        $category_mode->addOption('select', _MI_XOO_PARTNERS_CONFIG_MODE_SELECT);
        $category_mode->addOption('images', _MI_XOO_PARTNERS_CONFIG_MODE_IMAGES);
        $this->addElement( $category_mode );

        // image_size
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_SIZE, 'xoopartners_category[image_size]', 1, 10, $xoopartners_category['image_size']) );
        // image_width
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_WIDTH, 'xoopartners_category[image_width]', 1, 10, $xoopartners_category['image_width']) );
        // image_height
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_HEIGHT, 'xoopartners_category[image_height]', 1, 10, $xoopartners_category['image_height']) );

        // Partner
        $this->insertBreak(_MI_XOO_PARTNERS_CONFIG_PARTNER,'preferenceTitle');

        // Category mode
        $partner_mode = new XoopsFormSelect(_MI_XOO_PARTNERS_CONFIG_PARTNER_MODE, 'xoopartners_partner[display_mode]', $xoopartners_partner['display_mode']);
        $partner_mode->addOption('list',   _MI_XOO_PARTNERS_CONFIG_MODE_LIST);
        $partner_mode->addOption('table',  _MI_XOO_PARTNERS_CONFIG_MODE_TABLE);
        $partner_mode->addOption('news',   _MI_XOO_PARTNERS_CONFIG_MODE_NEWS);
        $partner_mode->addOption('images', _MI_XOO_PARTNERS_CONFIG_MODE_IMAGES);
        $this->addElement( $partner_mode );

        // image_size
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_SIZE, 'xoopartners_partner[image_size]', 1, 10, $xoopartners_partner['image_size']) );
        // image_width
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_WIDTH, 'xoopartners_partner[image_width]', 1, 10, $xoopartners_partner['image_width']) );
        // image_height
        $this->addElement( new XoopsFormText(_XOO_PARTNERS_IMAGE_HEIGHT, 'xoopartners_partner[image_height]', 1, 10, $xoopartners_partner['image_height']) );

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
}
?>