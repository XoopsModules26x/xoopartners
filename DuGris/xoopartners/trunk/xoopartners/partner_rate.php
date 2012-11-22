<?php
/**
 * Xoopartner module
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
 * @package         Xoopartner
 * @since           2.6.0
 * @author          Laurent JEN (Aka DuGris)
 * @version         $Id: index.php 1152 2012-11-15 14:31:41Z DuGris $
 */

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

error_reporting(0);
$xoopsLogger->activated = false;

$ret['error'] = 1;

if ( $xoops->security->check() ) {    $partner_id = $system->CleanVars($_REQUEST, 'partner_id', 0, 'int');
    $option = $system->CleanVars($_REQUEST, 'option', 2, 'int');

    $time = time();
    if ( !isset($_SESSION['xoopartners_rate' . $partner_id]) || $_SESSION['xoopartner_rates' . $partner_id] < $time ) {
        $_SESSION['xoopartners_rate' . $partner_id] = $time + 3600;
        $partners_handler = $xoops->getModuleHandler('xoopartners', 'xoopartners');
        $ret = $partners_handler->SetRate( $partner_id, $option );
        if ( is_array($ret) && count($ret) > 1) {            $ret['error'] = 0;
        } else {            $ret['error'] = 1;        }
    }
}
echo json_encode($ret)
?>