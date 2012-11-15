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

include dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'mainfile.php';

XoopsLoad::load('xoopreferences', 'xoopartners');
$Partners_config = XooPartnersPreferences::getInstance()->getConfig();

XoopsLoad::load('system', 'system');
$system = System::getInstance();

$xoops = Xoops::getInstance();
$xoops->loadLanguage('common', 'xoopartners');

$script_name = basename($_SERVER['SCRIPT_NAME'], '.php');
$xoops->header('xoopartners_' . $script_name . '.html');

$xoops->theme->addStylesheet('modules/xoopartners/css/module.css');

$xoops->tpl->assign('template', $Partners_config['xoopartners_main_mode'] );
$xoops->tpl->assign('welcome', $Partners_config['xoopartners_welcome'] );
$xoops->tpl->assign('xoopartners_category', $Partners_config['xoopartners_category'] );
$xoops->tpl->assign('xoopartners_partner', $Partners_config['xoopartners_partner'] );
$xoops->tpl->assign('moduletitle', $xoops->module->name() );

$categories_handler = $xoops->getModuleHandler('xoopartners_categories', 'xoopartners');
$partners_handler = $xoops->getModuleHandler('xoopartners', 'xoopartners');
?>