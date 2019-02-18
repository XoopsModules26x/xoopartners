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
use Xoops\Core\Request;

include __DIR__ .  '/header.php';

$start = Request::getInt('start', 0); //$system->cleanVars($_REQUEST, 'start', 0, 'int');

$criteria = new \CriteriaCompo();
$criteria->add(new \Criteria('xoopartners_online', 1));
$criteria->add(new \Criteria('xoopartners_published', time(), '<='));

if ($partnersConfig['xoopartners_category']['use_categories']) {
    $category_id = Request::getInt('category_id', 0); //$system->cleanVars($_REQUEST, 'category_id', 0, 'int');
    $categories = $categoriesHandler->getCategories();
    $partners = $partnersHandler->getPartners($category_id, 'order', 'asc', $start, $partnersConfig['xoopartners_partner']['limit_main']);
    $xoops->tpl()->assign('category_id', $category_id);
    $xoops->tpl()->assign('categories', $categories);
    $xoops->tpl()->assign('partners', $partners);

    $criteria->add(new \Criteria('xoopartners_category', $category_id));
    $partners_count = $partnersHandler->getCount($criteria);
    $extra = 'category_id=' . $category_id;

    if ('select' === $partnersConfig['xoopartners_category']['display_mode']) {
        $xoops->tpl()->assign('category_header', '<div class="txtcenter"><select name="category_id" onchange=\'window.location.href="index.php?category_id="+this.options[this.selectedIndex].value\'>');
        $xoops->tpl()->assign('category_footer', '</select></div>');
    } elseif ('table' === $partnersConfig['xoopartners_category']['display_mode']) {
        $xoops->tpl()->assign('category_header', '<table class="outer">');
        $xoops->tpl()->assign('category_footer', '</table>');
    }
} else {
    $partners = $partnersHandler->getPartners(0, 'order', 'asc', $start, $partnersConfig['xoopartners_partner']['limit_main']);
    $xoops->tpl()->assign('partners', $partners);
    $partners_count = $partnersHandler->getCount($criteria);
    $extra = '';
}

$description = '';
if (isset($categories)) {
    foreach ($categories as $k => $category) {
        $description .= $category['xoopartners_category_title'];
    }
}
$i = 0;
foreach ($partners as $k => $partner) {
    $description .= $partner['xoopartners_title'];
    ++$i;
    if ($i < count($partners)) {
        $description .= ', ';
    }
}
$utilities = new \XoopsModules\Xoopartners\Utility();
$xoops->theme()->addMeta($type = 'meta', 'description', $utilities->getMetaDescription($description));
$xoops->theme()->addMeta($type = 'meta', 'keywords', $utilities->getMetaKeywords($description));

// Page navigation
$paginate = new \XoopsModules\Xoopartners\XooPaginate($partners_count, $partnersConfig['xoopartners_partner']['limit_main'], $start, 'start', $extra);

include __DIR__ . '/footer.php';
