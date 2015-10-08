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

include __DIR__ .  '/header.php';

$xoops->disableErrorReporting();

$ret['error'] = 1;

if ($xoops->security()->check()) {
    $partner_id = $system->cleanVars($_REQUEST, 'partner_id', 0, 'int');
    $option     = $system->cleanVars($_REQUEST, 'option', 2, 'int');

    $time = time();
    if (!isset($_SESSION['xoopartners_rates' . $partner_id]) || $_SESSION['xoopartners_rates' . $partner_id] < $time) {
        $_SESSION['xoopartners_rates' . $partner_id] = $time + 3600;

        $xoopartners_module = Xoopartners::getInstance();
        $partners_handler   = $xoopartners_module->PartnersHandler();

        $ret = $partners_handler->SetRate($partner_id, $option);
        if (is_array($ret) && count($ret) > 1) {
            $ret['error'] = 0;
        } else {
            $ret['error'] = 1;
        }
    }
}
echo json_encode($ret)
