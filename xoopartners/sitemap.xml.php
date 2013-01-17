<?php
/**
 * Xoositemap module
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
 * @package         Xoositemap
 * @since           2.6.0
 * @author          Laurent JEN (Aka DuGris)
 * @version         $Id$
 */

if (file_exists('mainfile.php')) {
    include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mainfile.php';
} else {
    include '../../mainfile.php';
}
$xoops = Xoops::getInstance();
$xoops->disableErrorReporting();

if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}
header('Content-Type:text/xml; charset=utf-8');

$dirname = $xoops->isModule() ? $xoops->module->getVar('dirname'): 'system';
$tpl = new XoopsTpl();
//$tpl->caching = 2;
//$tpl->cache_lifetime = 3600;
//if (!$tpl->is_cached('module:xoositemap|xoositemap_xml.html')) {
    if ($xoops->isModule()) {        $plugin = Xoops_Module_Plugin::getPlugin($dirname, 'xoositemap');
        $res = $plugin->Xoositemap_xml(true);
        if (is_array($res)) {            $time = isset($res['time']) ? $res['time'] : time();
            $mod_time[] = array('time' => $time);
            $modules[] = array('time' => $time,
                               'dirname' => $res['dirname'],
                               'date' => gmdate('Y-m-d\TH:i:s\Z', $time),
                               );
            if (count($res['items']) > 0) {                foreach ($res['items'] as $item) {                    $times[] = array('time' => $item['time']);
                    $items[] = array('time' => $item['time'],
                                     'date' => gmdate('Y-m-d\TH:i:s\Z', $item['time']),
                                     'link' => $item['url'],
                                     );
                }
            }
        }
    } else {        $plugins = Xoops_Module_Plugin::getPlugins('xoositemap');
        foreach ($plugins as $plugin) {            $res = $plugin->Xoositemap_xml(true);
            if (is_array($res)) {                $time = isset($res['time']) ? $res['time'] : time();
                $mod_time[] = array('time' => $time);
                $modules[] = array('time' => $time,
                                   'dirname' => $res['dirname'],
                                   'date' => gmdate('Y-m-d\TH:i:s\Z', $time),
                                  );
                if (count($res['items']) > 0) {                    foreach ($res['items'] as $item) {                        $times[] = array('time' => $item['time']);
                        $items[] = array('time' => $item['time'],
                                         'date' => gmdate('Y-m-d\TH:i:s\Z', $item['time']),
                                         'link' => $item['url'],
                                         );
                    }
                }
            }
        }
    }

    array_multisort($times, SORT_DESC, $items);
    array_multisort($mod_time, SORT_DESC, $modules);
    $tpl->assign('items', $items);
    $tpl->assign('modules', $modules);
    $tpl->assign('modification', gmdate( 'Y-m-d\TH:i:s\Z' ));
//}
$tpl->display('module:xoositemap|xoositemap_xml.html');
