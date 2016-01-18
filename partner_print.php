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
 */

use Xoops\Core\Request;

include dirname(dirname(__DIR__)) . '/mainfile.php';
include __DIR__ . '/include/functions.php';

$xoopartnersModule = Xoopartners::getInstance();
$partnersConfig    = $xoopartnersModule->loadConfig();
$categoriesHandler = $xoopartnersModule->getCategoriesHandler();
$partnersHandler   = $xoopartnersModule->getPartnersHandler();

XoopsLoad::load('system', 'system');
$system = System::getInstance();

$xoops = Xoops::getInstance();
$xoops->disableErrorReporting();

$partner_id = Request::getInt('partner_id', 0); //$system->cleanVars($_REQUEST, 'partner_id', 0, 'int');
$partner    = $partnersHandler->get($partner_id);

$output = Request::getString('output', 'print'); //$system->cleanVars($_REQUEST, 'output', 'print', 'string');

if (is_object($partner) && count($partner) != 0 && $partner->getVar('xoopartners_online') && $partner->getVar('xoopartners_accepted')) {
    $tpl = new XoopsTpl();

    $tpl->assign('xoopartners_category', $partnersConfig['xoopartners_category']);
    $tpl->assign('xoopartners_partner', $partnersConfig['xoopartners_partner']);
    $tpl->assign('xoopartners_qrcode', $partnersConfig['xoopartners_qrcode']);
    $tpl->assign('xoopartners_rld', $partnersConfig['xoopartners_rld']);

    $tpl->assign('moduletitle', $xoops->module->name());

    $tpl->assign('partner', $partner->getValues());
    $tpl->assign('print', true);
    $tpl->assign('output', true);
    $tpl->assign('xoops_sitename', $xoops->getConfig('sitename'));
    $tpl->assign('xoops_pagetitle', $partner->getVar('xoopartners_title') . ' - ' . $xoops->module->getVar('name'));
    $tpl->assign('xoops_slogan', htmlspecialchars($xoops->getConfig('slogan'), ENT_QUOTES));

    if ('pdf' === $output && $xoops->isActiveModule('pdf')) {
        /*
                $content = $tpl->fetch('module:xoopartners/xoopartners_partner_pdf.html');
                $pdf = new Pdf('P', 'A4', _LANGCODE, true, _CHARSET, array(10, 10, 10, 10));
                $pdf->setDefaultFont('Helvetica');
                $pdf->writeHtml($content, false);
                $pdf->Output();
        */
    } else {
        $tpl->display('module:xoopartners/xoopartners_partner_print.tpl');
    }
} else {
    $tpl = new XoopsTpl();
    $tpl->assign('xoops_sitename', $xoops->getConfig('sitename'));
    $tpl->assign('xoops_slogan', htmlspecialchars($xoops->getConfig('slogan'), ENT_QUOTES));
    $tpl->assign('not_found', true);
    $tpl->display('module:xoopartners/xoopartners_partner_print.tpl');
}
