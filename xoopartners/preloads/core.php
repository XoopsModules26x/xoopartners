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

defined('XOOPS_ROOT_PATH') or die('Restricted access');

class XoopartnersCorePreload extends XoopsPreloadItem
{
    static function eventCoreIncludeCommonEnd($args)
    {
        $path = dirname(dirname(__FILE__));
        XoopsLoad::addMap(array(
            'xoopartners' => $path . '/class/xoopartners.php',
        ));
    }

    static function eventOnModuleInstallConfigs($args)
    {
        /* @var $module XoopsModule */
        $module = $args[0];
        $configs =& $args[1];
        $helper = Comments::getInstance(); //init helper to load defines na language

        if ($plugin = Xoops_Plugin::getPlugin($module->getVar('dirname'), 'comments', true)) {

            array_push($configs, array(
                'name'        => 'com_rule',
                'title'       => '_MD_COMMENTS_COMRULES',
                'description' => '',
                'formtype'    => 'select',
                'valuetype'   => 'int',
                'default'     => 1,
                'options'     => array(
                    '_MD_COMMENTS_COMNOCOM'        => COMMENTS_APPROVENONE,
                    '_MD_COMMENTS_COMAPPROVEALL'   => COMMENTS_APPROVEALL,
                    '_MD_COMMENTS_COMAPPROVEUSER'  => COMMENTS_APPROVEUSER,
                    '_MD_COMMENTS_COMAPPROVEADMIN' => COMMENTS_APPROVEADMIN
                )
            ));
            array_push($configs, array(
                'name'        => 'com_anonpost',
                'title'       => '_MD_COMMENTS_COMANONPOST',
                'description' => '',
                'formtype'    => 'yesno',
                'valuetype'   => 'int',
                'default'     => 0
            ));
        }
    }

    static function eventOnModuleUnistall($args)
    {
        /* @var $module XoopsModule */
        $module = $args[0];
        $class =& $args[1];
        // Delete notifications if any
        if ($plugin = Xoops_Plugin::getPlugin($module->getVar('dirname'), 'comments')) {
            $xoops = Xoops::getInstance();
            $helper = Comments::getInstance();

            // Delete comments if any
            $class->trace[] = _MD_COMMENTS_DELETE;
            if (false === $helper->getHandlerComment()->deleteByModule($module->getVar('mid'))) {
                $class->trace[]['sub'] = '<span class="red">' . _MD_COMMENTS_DELETE_ERROR . '</span>';
            } else {
                $class->trace[]['sub'] = _MD_COMMENTS_DELETED;
            }

            // delete module config options if any
            $config_handler = $xoops->getHandlerConfig();
            $configs = $config_handler->getConfigs(new Criteria('conf_modid', $module->getVar('mid')));
            $confcount = count($configs);
            if ($confcount > 0) {
                $class->trace[] = _AM_SYSTEM_MODULES_MODULE_DATA_DELETE;
                for ($i = 0; $i < $confcount; $i++) {
                    if (false === $config_handler->deleteConfig($configs[$i])) {
                        $class->trace[]['sub'] = '<span class="red">' . _AM_SYSTEM_MODULES_CONFIG_DATA_DELETE_ERROR . sprintf(_AM_SYSTEM_MODULES_GONFIG_ID, "<strong>" . $configs[$i]->getvar('conf_id') . "</strong>") . '</span>';
                    } else {
                        $class->trace[]['sub'] = _AM_SYSTEM_MODULES_GONFIG_DATA_DELETE . sprintf(_AM_SYSTEM_MODULES_GONFIG_ID, "<strong>" . $configs[$i]->getvar('conf_id') . "</strong>");
                    }
                }
            }
        }
    }

    static function eventOnSystemPreferencesForm($args)
    {
        /* @var $module XoopsModule */
        $module = $args[0];
        if ($plugin = Xoops_Plugin::getPlugin($module->getVar('dirname'), 'comments')) {
            Comments::getInstance()->loadLanguage('main');
        }
    }
}
?>