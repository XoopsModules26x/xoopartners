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
 */

/**
 * Class XoopartnersPreferencesForm
 */
class XoopartnersPreferencesForm extends Xoops\Form\ThemeForm
{
    private $colors
        = array(
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
            'Yellow'  => '#FFFF00'
        );

    private $config = array();

    /**
     * @param string $config
     * @internal param null $obj
     */
    public function __construct($config)
    {
        extract($config);
        $xoops = Xoops::getInstance();
        parent::__construct('', 'form_preferences', 'preferences.php', 'post', true);
        $this->setExtra('enctype="multipart/form-data"');

        $tabTray = new Xoops\Form\TabTray('', 'uniqueid');

        /**
         * Main page
         */
        //welcome
        $tab1 = new Xoops\Form\Tab(_XOO_CONFIG_MAINPAGE, 'tabid-1');
        $tab1->addElement(new Xoops\Form\TextArea(_XOO_CONFIG_WELCOME, 'xoopartners_welcome', $xoopartners_welcome, 12, 12));

        $tabTray->addElement($tab1);

        /**
         * Categories
         */
        $tab2 = new Xoops\Form\Tab(_XOO_CONFIG_CATEGORY, 'tabid-2');

        // use category
        $tab2->addElement(new Xoops\Form\RadioYesNo(_XOO_CONFIG_USE_CATEGORIES, 'xoopartners_category[use_categories]', $xoopartners_category['use_categories']));

        // main menu
        $tab2->addElement(new Xoops\Form\RadioYesNo(_XOO_CONFIG_MAIN_MENU, 'xoopartners_category[main_menu]', $xoopartners_category['main_menu']));

        // Category mode
        $category_mode = new Xoops\Form\Select(_XOO_CONFIG_CATEGORY_MODE, 'xoopartners_category[display_mode]', $xoopartners_category['display_mode']);
        $category_mode->addOption('list', _XOO_CONFIG_MODE_LIST);
        $category_mode->addOption('table', _XOO_CONFIG_MODE_TABLE);
        $category_mode->addOption('select', _XOO_CONFIG_MODE_SELECT);
        $category_mode->addOption('images', _XOO_CONFIG_MODE_IMAGES);
        $tab2->addElement($category_mode);

        // image_size
        $tab2->addElement(new Xoops\Form\Text(_XOO_PARTNERS_IMAGE_SIZE, 'xoopartners_category[image_size]', 1, 10, $xoopartners_category['image_size']));
        // image_width
        $tab2->addElement(new Xoops\Form\Text(_XOO_PARTNERS_IMAGE_WIDTH, 'xoopartners_category[image_width]', 1, 10, $xoopartners_category['image_width']));
        // image_height
        $tab2->addElement(new Xoops\Form\Text(_XOO_PARTNERS_IMAGE_HEIGHT, 'xoopartners_category[image_height]', 1, 10, $xoopartners_category['image_height']));

        $tabTray->addElement($tab2);

        /**
         * Partners
         */
        $tab3 = new Xoops\Form\Tab(_XOO_CONFIG_PARTNER, 'tabid-3');

        // Partner mode
        $partner_mode = new Xoops\Form\Select(_XOO_CONFIG_PARTNER_MODE, 'xoopartners_partner[display_mode]', $xoopartners_partner['display_mode']);
        $partner_mode->addOption('blog', _XOO_CONFIG_MODE_BLOG);
        $partner_mode->addOption('images', _XOO_CONFIG_MODE_IMAGES);
        $partner_mode->addOption('list', _XOO_CONFIG_MODE_LIST);
        $partner_mode->addOption('table', _XOO_CONFIG_MODE_TABLE);
        $tab3->addElement($partner_mode);

        // limit per page
        $tab3->addElement(new Xoops\Form\Text(_XOO_CONFIG_LIMIT_MAIN, 'xoopartners_partner[limit_main]', 1, 10, $xoopartners_partner['limit_main']));

        // image_size
        $tab3->addElement(new Xoops\Form\Text(_XOO_PARTNERS_IMAGE_SIZE, 'xoopartners_partner[image_size]', 1, 10, $xoopartners_partner['image_size']));
        // image_width
        $tab3->addElement(new Xoops\Form\Text(_XOO_PARTNERS_IMAGE_WIDTH, 'xoopartners_partner[image_width]', 1, 10, $xoopartners_partner['image_width']));
        // image_height
        $tab3->addElement(new Xoops\Form\Text(_XOO_PARTNERS_IMAGE_HEIGHT, 'xoopartners_partner[image_height]', 1, 10, $xoopartners_partner['image_height']));

        $tabTray->addElement($tab3);

        /**
         * Rate / Like - Dislike
         */
        $rld = new Xoops\Form\Tab(_XOO_CONFIG_RLD, 'tabid-rld');

        // Rate / Like / Dislike Mode
        $rld_mode = new Xoops\Form\Select(_XOO_CONFIG_RLD_MODE, 'xoopartners_rld[rld_mode]', $xoopartners_rld['rld_mode']);
        $rld_mode->addOption('none', _XOO_CONFIG_RLD_NONE);
        $rld_mode->addOption('rate', _XOO_CONFIG_RLD_RATE);
        $rld_mode->addOption('likedislike', _XOO_CONFIG_RLD_LIKEDISLIKE);
        $rld->addElement($rld_mode);

        $rate_scale = new Xoops\Form\Select(_XOO_CONFIG_RATE_SCALE, 'xoopartners_rld[rate_scale]', $xoopartners_rld['rate_scale']);
        for ($i = 4; $i <= 10; ++$i) {
            $rate_scale->addOption($i, $i);
        }

        $rld->addElement($rate_scale);

        $tabTray->addElement($rld);

        $this->addElement($tabTray);

        /**
         * Buttons
         */
        $buttonTray = new Xoops\Form\ElementTray('', '');
        $buttonTray->addElement(new Xoops\Form\Hidden('op', 'save'));

        $buttonSubmit = new Xoops\Form\Button('', 'submit', XoopsLocale::A_SUBMIT, 'submit');
        $buttonSubmit->setClass('btn btn-success');
        $buttonTray->addElement($buttonSubmit);

        $buttonReset = new Xoops\Form\Button('', 'reset', XoopsLocale::A_RESET, 'reset');
        $buttonReset->setClass('btn btn-warning');
        $buttonTray->addElement($buttonReset);

        $buttonCancel = new Xoops\Form\Button('', 'cancel', XoopsLocale::A_CANCEL, 'button');
        $buttonCancel->setExtra("onclick='javascript:history.go(-1);'");
        $buttonCancel->setClass('btn btn-danger');
        $buttonTray->addElement($buttonCancel);

        $this->addElement($buttonTray);
    }
}
