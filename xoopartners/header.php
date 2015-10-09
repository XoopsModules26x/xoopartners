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

include dirname(dirname(__DIR__)) . '/mainfile.php';
include __DIR__ . '/include/functions.php';

$xoopartners_module = Xoopartners::getInstance();
$partners_config    = $xoopartners_module->loadConfig();
$categoriesHandler = $xoopartners_module->categoriesHandler();
$partnersHandler   = $xoopartners_module->partnersHandler();

XoopsLoad::load('system', 'system');
$system = System::getInstance();

$xoops = Xoops::getInstance();

$script_name = basename($_SERVER['SCRIPT_NAME'], '.php');
$xoops->header('xoopartners_' . $script_name . '.tpl');

$xoops->theme()->addStylesheet('modules/xoopartners/assets/css/module.css');

$xoops->tpl()->assign('moduletitle', $xoops->module->name());

$xoops->tpl()->assign('template', $partners_config['xoopartners_main_mode']);
$xoops->tpl()->assign('welcome', $partners_config['xoopartners_welcome']);
$xoops->tpl()->assign('xoopartners_category', $partners_config['xoopartners_category']);
$xoops->tpl()->assign('xoopartners_partner', $partners_config['xoopartners_partner']);
$xoops->tpl()->assign('xoopartners_rld', $partners_config['xoopartners_rld']);

$xoops->tpl()->assign('qrcode', $xoops->isActiveModule('qrcode'));

if ($xoops->isActiveModule('notifications')) {
    if ($plugin = \Xoops\Module\Plugin::getPlugin('xoopartners', 'notifications') && $xoops->isUser()) {
        $xoops->tpl()->assign('xoopartners_not', true);
    }
}
