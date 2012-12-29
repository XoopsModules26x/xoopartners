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

$module = Xoops::getInstance()->getHandlerModule()->getBydirname('xoopartners');

$xoopartners_module = Xoopartners::getInstance();
$partners_config = $xoopartners_module->LoadConfig();

$i = 0;
$adminmenu[$i]['title']   = _MI_XOO_PARTNERS_INDEX;
$adminmenu[$i]['link']    = 'admin/index.php';
$adminmenu[$i]['icon']    = 'home.png';

if (Xoops::getInstance()->isadmin()) {
    $i++;
    $adminmenu[$i]['title'] = _MI_XOO_PARTNERS_PREFERENCES;
    $adminmenu[$i]['link']  = 'admin/preferences.php';
    $adminmenu[$i]['icon']  = 'administration.png';

    $i++;
    $adminmenu[$i]['title'] = _MI_XOO_PARTNERS_MODCONFIG;
    $adminmenu[$i]['link']  = '../system/admin.php?fct=preferences&op=showmod&mod=' . $module->getVar('mid');
    $adminmenu[$i]['icon']  = 'configs.png';
}

if ($partners_config['xoopartners_category']['use_categories']) {    $i++;
    $adminmenu[$i]['title'] = _MI_XOO_PARTNERS_CATEGORIES;
    $adminmenu[$i]['link']  = 'admin/categories.php';
    $adminmenu[$i]['icon']  = 'category.png';
}

$i++;
$adminmenu[$i]['title'] = _MI_XOO_PARTNERS_PARTNERS;
$adminmenu[$i]['link']  = 'admin/partners.php';
$adminmenu[$i]['icon']  = 'partners.png';

if (Xoops::getInstance()->isActiveModule('comments')) {
    $i++;
    $adminmenu[$i]['title'] = _MI_COMMENTS_NAME;
    $adminmenu[$i]['link']  = '../comments/admin/main.php?module=' . $module->getVar('mid');
    $adminmenu[$i]['icon']  = 'comments.png';
}

$i++;
$adminmenu[$i]['title']   = _MI_XOO_PARTNERS_ABOUT;
$adminmenu[$i]['link']    = 'admin/about.php';
$adminmenu[$i]['icon']    = 'about.png';
?>