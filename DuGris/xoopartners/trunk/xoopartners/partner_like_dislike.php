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

$xoops->disableErrorReporting();

$ret['error'] = 1;

if ( $xoops->security()->check() ) {
    $option = $system->CleanVars($_REQUEST, 'option', 2, 'int');

    $time = time();
    if ( !isset($_SESSION['xoopartners_like' . $partner_id]) || $_SESSION['xoopartners_like' . $partner_id] < $time ) {
        $_SESSION['xoopartners_like' . $partner_id] = $time + 3600;
        $partners_handler = $xoops->getModuleHandler('xoopartners', 'xoopartners');
        $ret = $partners_handler->SetLike_Dislike( $partner_id, $option );
        if ( is_array($ret) && count($ret) > 1) {
        } else {
    }
}
echo json_encode($ret)
?>