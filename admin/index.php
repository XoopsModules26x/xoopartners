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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         Xoopartners
 * @since           2.6.0
 * @author          Laurent JEN (Aka DuGris)
 */
include __DIR__ . '/header.php';

// extension
$admin_page->addConfigBoxLine(['comments', 'warning'], 'extension');
$admin_page->addConfigBoxLine(['pdf', 'warning'], 'extension');
$admin_page->addConfigBoxLine(['qrcode', 'warning'], 'extension');
$admin_page->addConfigBoxLine(['xoosocialnetwork', 'warning'], 'extension');
$admin_page->addConfigBoxLine(['notifications', 'warning'], 'module');

include __DIR__ . '/footer.php';
