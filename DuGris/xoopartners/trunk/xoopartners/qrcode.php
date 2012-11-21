<?php
/**
 * XooBlog module
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
 * @package         XooBlog
 * @since           2.6.0
 * @author          Laurent JEN (Aka DuGris)
 * @version         $Id: index.php 1152 2012-11-15 14:31:41Z DuGris $
 */

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';
$url = $system->CleanVars($_REQUEST, 'url', '', 'string');
extract($Blog_config['xooblog_qrcode']);

if ( $url != '' ) {    include XOOPS_PATH . '/phpqrcode/qrlib.php';
    QRcode::png($url, false, $CorrectionLevel, $matrixPointSize, $whiteMargin );}
?>