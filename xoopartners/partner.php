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
$_SESSION['xoopartners_stat'] = true;

$partner_id = $system->CleanVars($_REQUEST, 'partner_id', 0, 'int');
$partner = $partners_handler->get($partner_id);

if ( is_object($partner) && count($partner) != 0 && $partner->getVar('xoopartners_online') && $partner->getVar('xoopartners_accepted') ) {    $time = time();
    if ( !isset($_SESSION['xoopartner_view' . $partner_id]) || $_SESSION['xoopartner_view' . $partner_id] < $time ) {
        $_SESSION['xoopartner_view' . $partner_id] = $time + 3600;
        $partners_handler->SetRead( $partner );
    }

    $xoops->tpl->assign('security', $xoops->security->createToken() );

    $xoops->tpl->assign('partner', $partner->toArray() );
    $xoops->tpl->assign('xoops_pagetitle' , $partner->getVar('xoopartners_title') . ' - ' . $xoops->module->getVar('name') );
    $xoops->theme->addMeta($type = 'meta', 'description', $partner->getMetaDescription() );
    $xoops->theme->addMeta($type = 'meta', 'keywords', $partner->getMetaKeywords() );

} else {    $xoops->tpl->assign('not_found', true);
}
include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footer.php';
?>