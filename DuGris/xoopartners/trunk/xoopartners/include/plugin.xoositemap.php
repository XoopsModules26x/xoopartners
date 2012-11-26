<?php
/**
 * Xooghost module
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
 * @package         Xooghost
 * @since           2.6.0
 * @author          Laurent JEN (Aka DuGris)
 * @version         $Id$
 */

function XooSitemap_xoopartners( $subcategories = true)
{
    $xoops = Xoops::getInstance();
    $xoops->loadLanguage('common', 'xoopartners');

    XoopsLoad::load('xoopreferences', 'xoopartners');
    $Partners_config = XooPartnersPreferences::getInstance()->getConfig();

    $categories_handler = $xoops->getModuleHandler('xoopartners_categories', 'xoopartners');
    $partners_handler = $xoops->getModuleHandler('xoopartners', 'xoopartners');

    $sitemap = array();
    if ( $subcategories && $Partners_config['xoopartners_category']['use_categories'] ) {        $categories = $categories_handler->GetCategories();
        foreach ($categories as $c => $category) {            $sitemap[$c]['id'] = $c;
            $sitemap[$c] = getCategory( $category );        }
    } elseif (!$subcategories || ($subcategories && !$Partners_config['xoopartners_category']['use_categories']) ) {        $partners = $partners_handler->GetPartners(0, 'published', 'desc');

        foreach ($partners as $p => $partner) {            $sitemap[$p]['id']    = $p;
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
function getCategory( $category )
{    $xoops = Xoops::getInstance();
    $partners_handler = $xoops->getModuleHandler('xoopartners', 'xoopartners');
    $ret = array();
    $ret['title'] = $category['xoopartners_category_title'];
    $ret['url']   = XOOPS_URL . '/modules/xoopartners/index.php?category_id=' . $category['xoopartners_category_id'];
    $ret['image'] = $category['xoopartners_category_image_link'];

    $partners = $partners_handler->GetPartners($category['xoopartners_category_id'], 'published', 'desc');
    foreach ($partners as $p => $partner) {        $ret['item'][$p]['id']    = $p;
        $ret['item'][$p]['title'] = $partner['xoopartners_title'];
        $ret['item'][$p]['url']   = XOOPS_URL . '/modules/xoopartners/partner.php?partner_id=' . $partner['xoopartners_id'];
        $ret['item'][$p]['uid']   = $partner['xoopartners_uid'];
        $ret['item'][$p]['uname'] = $partner['xoopartners_uid_name'];
        $ret['item'][$p]['image'] = $partner['xoopartners_image_link'];
        $ret['item'][$p]['date']  = $partner['xoopartners_published'];
    }

    if ( isset($category['categories']) ) {        foreach ($category['categories'] as $subcategory ) {
            $ret['categories'][] = getCategory( $subcategory );
        }
    }
    return $ret;}
?>