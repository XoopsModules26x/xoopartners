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

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

class XoopartnersMenusPlugin extends Xoops_Plugin_Abstract implements MenusPluginInterface
{
    /**
     * expects an array of array containing:
     * name,      Name of the submenu
     * url,       Url of the submenu relative to the module
     * ex: return array(0 => array(
     *      'name' => _MI_PUBLISHER_SUB_SMNAME3;
     *      'url' => "search.php";
     *    ));
     *
     * @return array
     */
    public function subMenus()
    {
        $ret = array();
        if (Xoops::getInstance()->isModule() && Xoops::getInstance()->module->getVar('dirname') == 'xoopartners') {
            $xoopartners_module = Xoopartners::getInstance();
            $partners_config = $xoopartners_module->LoadConfig();

            $i=0;
            if ( $partners_config['xoopartners_category']['use_categories'] && $partners_config['xoopartners_category']['main_menu']) {                $categories_handler = $xoopartners_module->CategoriesHandler();
                $categories = $categories_handler->GetCategories(0, false, false);
                foreach ($categories as $k => $category ) {
                    $ret[$i]['name']  = $category['xoopartners_category_title'];
                    $ret[$i]['url']   = 'index.php?category_id=' . $category['xoopartners_category_id'];
                    $i++;
                }
            }
            $ret[$i]['name']  = _XOO_PARTNERS_JOIN;
            $ret[$i]['url']   = 'joinpartners.php';

        }
        return $ret;
    }
}

