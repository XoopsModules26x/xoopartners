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
    public function __construct($config)
    {        extract( $config );
        $xoops = Xoops::getinstance();
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

        /**
         * Categories
         */
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

        $tabtray->addElement( $tab2 );

        /**
         * Partners
         */
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

        $tabtray->addElement( $tab3 );

        /**
         * Rate / Like - Dislike
         */
        $rld = new XoopsFormTab(_XOO_CONFIG_RLD, 'tabid-rld');

        // Rate / Like / Dislike Mode
        $rld_mode = new XoopsFormSelect(_XOO_CONFIG_RLD_MODE, 'xoopartners_rld[rld_mode]', $xoopartners_rld['rld_mode']);
        $rld_mode->addOption('none',        _XOO_CONFIG_RLD_NONE);
        $rld_mode->addOption('rate',        _XOO_CONFIG_RLD_RATE);
        $rld_mode->addOption('likedislike', _XOO_CONFIG_RLD_LIKEDISLIKE);
        $rld->addElement( $rld_mode );

        $rate_scale = new XoopsFormSelect(_XOO_CONFIG_RATE_SCALE, 'xoopartners_rld[rate_scale]', $xoopartners_rld['rate_scale']);
        for ($i=4; $i <= 10; $i++) {
            $rate_scale->addOption($i, $i);
        }

        $rld->addElement( $rate_scale );

        $tabtray->addElement( $rld );

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
}
?>