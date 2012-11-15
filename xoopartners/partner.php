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

$partner_id = $system->CleanVars($_REQUEST, 'partner_id', 0, 'int');

$partner = $partners_handler->get($partner_id);
$xoops->tpl->assign('partner', $partner->toArray() );

$time = time();
if ( !isset($_SESSION['xoopartner_view' . $partner_id]) || $_SESSION['xoopartner_view' . $partner_id] < $time ) {    $partner->setDisplay();
    $partners_handler->insert( $partner );
    $_SESSION['xoopartner_view' . $partner_id] = $time + 3600;
}

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footer.php';
?>