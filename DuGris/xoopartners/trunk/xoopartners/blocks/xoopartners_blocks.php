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

function xoopartners_show($options)
{    $xoops = Xoops::getInstance();
    $xoops->theme->addStylesheet('modules/xoopartners/css/module.css');
    $xoops->theme->addStylesheet('modules/xoopartners/css/blocks.css');

    XoopsLoad::load('xoopreferences', 'xoopartners');
    $Partners_config = XooPartnersPreferences::getInstance()->loadConfig();
    $xooparnters_handler = $xoops->getModuleHandler('xoopartners', 'xoopartners');

    $block['template'] = $options[0];
    $options[1] = isset( $options[1] ) ? $options[1] : -1;
    $block['partners'] = $xooparnters_handler->GetPartners( $options[1] );
    $block['xoopartners_partner'] = $Partners_config['xoopartners_partner'];

    $xoops->tpl->assign('xoopartners_category', $Partners_config['xoopartners_category'] );
    $xoops->tpl->assign('xoopartners_partner', $Partners_config['xoopartners_partner'] );
	return $block;
}

function xoopartners_edit($options)
{    $xoops = Xoops::getInstance();    $block_form = new XoopsFormElementTray('&nbsp;', '<br />');

    $tmp = new XoopsFormSelect(_MB_XOO_PARTNERS_CONFIG_PARTNER_MODE . ' : ', 'options[0]', $options[0]);
    $tmp->addOption('list', _MB_XOO_PARTNERS_CONFIG_MODE_LIST);
    $tmp->addOption('table', _MB_XOO_PARTNERS_CONFIG_MODE_TABLE);
    $tmp->addOption('news', _MB_XOO_PARTNERS_CONFIG_MODE_NEWS);
    $tmp->addOption('images', _MB_XOO_PARTNERS_CONFIG_MODE_IMAGES);
    $block_form->addElement($tmp);

    XoopsLoad::load('xoopreferences', 'xoopartners');
    $Partners_config = XooPartnersPreferences::getInstance()->loadConfig();
    if ($Partners_config['xoopartners_category']['use_categories']) {            $categories_handler = $xoops->getModuleHandler('xoopartners_categories', 'xoopartners');
            ob_start();
            $categories_handler->makeSelectBox('options[1]', $options[1] );
            $block_form->addElement(new XoopsFormLabel(_MB_XOO_PARTNERS_CATEGORY_TITLE, ob_get_contents()));
            ob_end_clean();
    }

	return $block_form->render();
}
?>