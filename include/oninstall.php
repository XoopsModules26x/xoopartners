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
 * @version         $Id: install.php 1388 2012-12-29 00:23:08Z DuGris $
 */

/**
 * @return bool
 */
function xoops_module_install_xoopartners()
{
    $xoops = \Xoops::getInstance();
    $folders = [];
    $folders[] = \XoopsBaseConfig::get('uploads-path') . '/xoopartners/categories/images';
    $folders[] = \XoopsBaseConfig::get('uploads-path') . '/xoopartners/partners/images';
    $images = ['index.html', 'blank.gif'];

    foreach ($folders as $folder) {
        if (!xoopartners_mkdirs($folder)) {
            return false;
        }
        foreach ($images as $image) {
            if (!xoopartners_copyfile(\XoopsBaseConfig::get('uploads-path'), $image, $folder)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * @param              $pathname
 * @param mixed|string $pathout
 * @return bool
 */
function xoopartners_mkdirs($pathname, $pathout = XOOPS_ROOT_PATH)
{
    $xoops = \Xoops::getInstance();
    $pathname = mb_substr($pathname, mb_strlen(\XoopsBaseConfig::get('root-path')));
    $pathname = str_replace(DIRECTORY_SEPARATOR, '/', $pathname);

    $dest  = $pathout;
    $paths = explode('/', $pathname);

    foreach ($paths as $path) {
        if (!empty($path)) {
            $dest = $dest . '/' . $path;
            if (!is_dir($dest)) {
                if (!mkdir($dest, 0755) && !is_dir($dest)) {
                    return false;
                }
                xoopartners_copyfile(\XoopsBaseConfig::get('uploads-path'), 'index.html', $dest);
            }
        }
    }

    return true;
}

/**
 * @param $folder_in
 * @param $source_file
 * @param $folder_out
 * @return bool
 */
function xoopartners_copyfile($folder_in, $source_file, $folder_out)
{
    if (!is_dir($folder_out)) {
        if (!xoopartners_mkdirs($folder_out)) {
            return false;
        }
    }

    // Simple copy for a file
    if (is_file($folder_in . '/' . $source_file)) {
        return copy($folder_in . '/' . $source_file, $folder_out . '/' . basename($source_file));
    }

    return false;
}
