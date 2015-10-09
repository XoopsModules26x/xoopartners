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
 * @version         $Id: update.php 1388 2012-12-29 00:23:08Z DuGris $
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once XOOPS_ROOT_PATH . '/modules/xoopartners/install/oninstall.php';

/**
 * @return bool
 */
function xoops_module_update_xoopartners()
{
    return xoops_module_install_xoopartners();
}
