<?php

namespace XoopsModules\Xoopartners\Plugin;

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

/**
 * Class XoopartnersXoositemapPlugin
 */
class XoositemapPlugin extends \Xoops\Module\Plugin\PluginAbstract implements \XoositemapPluginInterface
{
    /**
     * @param $subcategories
     * @return array
     */
    public function xoositemap($subcategories)
    {
        $helper = \XoopsModules\Xoopartners\Helper::getInstance();
        $partnersConfig = $helper->loadConfig();
        $categoriesHandler = $helper->getHandler('Categories');
        $partnersHandler = $helper->getHandler('Partners');

        $sitemap = [];
        if ($subcategories && $partnersConfig['xoopartners_category']['use_categories']) {
            $categories = $categoriesHandler->getCategories();
            foreach ($categories as $c => $category) {
                $sitemap[$c]['id'] = $c;
                $sitemap[$c] = $this->xoopartners_getCategory($category);
            }
        } elseif (!$subcategories || ($subcategories && !$partnersConfig['xoopartners_category']['use_categories'])) {
            $partners = $partnersHandler->getPartners(0, 'published', 'desc');

            foreach ($partners as $p => $partner) {
                $sitemap[$p]['id'] = $p;
                $sitemap[$p]['title'] = $partner['xoopartners_title'];
                $sitemap[$p]['url'] = $partner['xoopartners_link'];
                $sitemap[$p]['uid'] = $partner['xoopartners_uid'];
                $sitemap[$p]['uname'] = $partner['xoopartners_uid_name'];
                $sitemap[$p]['image'] = $partner['xoopartners_image_link'];
                $sitemap[$p]['time'] = $partner['xoopartners_time'];
            }
        }

        return $sitemap;
    }

    /**
     * @param $category
     * @return array
     */
    public function xoopartners_getCategory($category)
    {
        $helper = \XoopsModules\Xoopartners\Helper::getInstance();
        $partnersHandler = $helper->getHandler('Partners');

        $ret = [];
        $partners = $partnersHandler->getPartners($category['xoopartners_category_id'], 'published', 'desc');
        if (count($partners) > 0) {
            $ret['title'] = $category['xoopartners_category_title'];
            $ret['url'] = $category['xoopartners_category_link'];
            $ret['image'] = $category['xoopartners_category_image_link'];
            $ret['category'] = true;

            foreach ($partners as $p => $partner) {
                $ret['item'][$p]['id'] = $p;
                $ret['item'][$p]['title'] = $partner['xoopartners_title'];
                $ret['item'][$p]['url'] = $partner['xoopartners_link'];
                $ret['item'][$p]['uid'] = $partner['xoopartners_uid'];
                $ret['item'][$p]['uname'] = $partner['xoopartners_uid_name'];
                $ret['item'][$p]['image'] = $partner['xoopartners_image_link'];
                $ret['item'][$p]['time'] = $partner['xoopartners_time'];
            }
        }

        if (isset($category['categories'])) {
            foreach ($category['categories'] as $subcategory) {
                $ret['categories'][] = $this->xoopartners_getCategory($subcategory);
            }
        }

        return $ret;
    }

    /**
     * @param $subcategories
     * @return array
     */
    public function xoositemap_xml($subcategories)
    {
        $helper = \XoopsModules\Xoopartners\Helper::getInstance();
        $partnersConfig = $helper->loadConfig();
        $categoriesHandler = $helper->getHandler('Categories');
        $partnersHandler = $helper->getHandler('Partners');

        $sitemap = [];
        $time = 0;

        $partners = $partnersHandler->getPartners(0, 'published', 'desc');
        foreach ($partners as $p => $partner) {
            $sitemap[$p]['url'] = $partner['xoopartners_link'];
            $sitemap[$p]['time'] = $partner['xoopartners_time'];
            if ($time < $partner['xoopartners_time']) {
                $time = $partner['xoopartners_time'];
            }
        }

        if ($subcategories && $partnersConfig['xoopartners_category']['use_categories']) {
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('xoopartners_category_online', 1));
            $criteria->setSort('xoopartners_category_order');
            $criteria->setOrder('asc');

            $categories = $categoriesHandler->getObjects($criteria, true, false);
            foreach ($categories as $category) {
                ++$p;
                $sitemap[$p]['url'] = $category['xoopartners_category_link'];
                $sitemap[$p]['time'] = $time;
            }
        }

        return ['dirname' => \XoopsModules\Xoopartners\Helper::getInstance()->getModule()->getVar('dirname'), 'time' => $time, 'items' => $sitemap];
    }
}
