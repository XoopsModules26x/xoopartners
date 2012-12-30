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

class XoopartnersXoositemapPlugin extends Xoops_Module_Plugin_Abstract implements XoositemapPluginInterface
{
    public function Xoositemap($subcategories)
    {
        $xoopartners_module = Xoopartners::getInstance();
        $partners_config = $xoopartners_module->LoadConfig();
        $categories_handler = $xoopartners_module->CategoriesHandler();
        $partners_handler = $xoopartners_module->PartnersHandler();

        $sitemap = array();
        if ( $subcategories && $partners_config['xoopartners_category']['use_categories'] ) {
            $categories = $categories_handler->GetCategories();
            foreach ($categories as $c => $category) {
                $sitemap[$c]['id'] = $c;
                $sitemap[$c] = $this->xoopartners_getCategory( $category );
            }
        } elseif (!$subcategories || ($subcategories && !$partners_config['xoopartners_category']['use_categories']) ) {
            $partners = $partners_handler->GetPartners(0, 'published', 'desc');

            foreach ($partners as $p => $partner) {
                $sitemap[$p]['id']    = $p;
                $sitemap[$p]['title'] = $partner['xoopartners_title'];
                $sitemap[$p]['url']   = XOOPS_URL . '/modules/xoopartners/partner.php?partner_id=' . $partner['xoopartners_id'];
                $sitemap[$p]['uid']   = $partner['xoopartners_uid'];
                $sitemap[$p]['uname'] = $partner['xoopartners_uid_name'];
                $sitemap[$p]['image'] = $partner['xoopartners_image_link'];
                $sitemap[$p]['date']  = $partner['xoopartners_published'];
            }
        }
        return $sitemap;
    }

    function xoopartners_getCategory( $category )
    {
        $xoopartners_module = Xoopartners::getInstance();
        $partners_handler = $xoopartners_module->PartnersHandler();

        $ret = array();
        $partners = $partners_handler->GetPartners($category['xoopartners_category_id'], 'published', 'desc');
        if ( count($partners) > 0 ) {
            $ret['title'] = $category['xoopartners_category_title'];
            $ret['url']   = XOOPS_URL . '/modules/xoopartners/index.php?category_id=' . $category['xoopartners_category_id'];
            $ret['image'] = $category['xoopartners_category_image_link'];
            $ret['category'] = true;

            foreach ($partners as $p => $partner) {
                $ret['item'][$p]['id']    = $p;
                $ret['item'][$p]['title'] = $partner['xoopartners_title'];
                $ret['item'][$p]['url']   = XOOPS_URL . '/modules/xoopartners/partner.php?partner_id=' . $partner['xoopartners_id'];
                $ret['item'][$p]['uid']   = $partner['xoopartners_uid'];
                $ret['item'][$p]['uname'] = $partner['xoopartners_uid_name'];
                $ret['item'][$p]['image'] = $partner['xoopartners_image_link'];
                $ret['item'][$p]['date']  = $partner['xoopartners_published'];
            }
        }

        if ( isset($category['categories']) ) {
            foreach ($category['categories'] as $subcategory ) {
                $ret['categories'][] = $this->xoopartners_getCategory( $subcategory );
            }
        }
        return $ret;
    }
}