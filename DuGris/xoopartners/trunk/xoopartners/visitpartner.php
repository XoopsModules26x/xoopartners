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

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

error_reporting(0);
$xoopsLogger->activated = false;

XoopsLoad::load('system', 'system');
$system = System::getInstance();
$partner_id = $system->CleanVars($_REQUEST, 'partner_id', 0, 'int');

if ( $partner_id != 0) {    $partners_handler = $xoops->getModuleHandler('xoopartners', 'xoopartners');
    $partner = $partners_handler->get($partner_id);

    $time = time();
    if ( !isset($_SESSION['xoopartner_visit' . $partner_id]) || $_SESSION['xoopartner_visit' . $partner_id] < $time ) {
        $partner->setVisit();
        $partners_handler->insert( $partner );
        $_SESSION['xoopartner_visit' . $partner_id] = $time + 3600;
    }

    echo "<html><head><meta http-equiv='Refresh' content='0; URL=" . $partner->getVar("xoopartners_url")."'></head><body></body></html>";
    exit();
}
include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footer.php';
?>