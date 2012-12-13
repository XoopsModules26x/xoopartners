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

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

XoopsLoad::load('xoopaginate', 'xoopartners');
$start = $system->CleanVars($_REQUEST, 'start', 0, 'int');

$criteria = new CriteriaCompo();
$criteria->add( new Criteria('xoopartners_online', 1) ) ;
$criteria->add( new Criteria('xoopartners_published', time(), '<=') ) ;

if ($Partners_config['xoopartners_category']['use_categories']) {    $category_id = $system->CleanVars($_REQUEST, 'category_id', 0, 'int');
    $categories = $categories_handler->GetCategories();
    $partners = $partners_handler->GetPartners( $category_id, 'order', 'asc', $start, $Partners_config['xoopartners_partner']['limit_main'] );
    $xoops->tpl()->assign('category_id', $category_id);
    $xoops->tpl()->assign('categories', $categories);
    $xoops->tpl()->assign('partners', $partners);

    $criteria->add( new Criteria('xoopartners_category', $category_id) ) ;
    $partners_count = $partners_handler->getCount($criteria);
    $extra = 'category_id=' . $category_id;

    if ( $Partners_config['xoopartners_category']['display_mode'] == 'select' ) {        $xoops->tpl()->assign('category_header', '<div class="txtcenter"><select name="category_id" onchange=\'window.location.href="index.php?category_id="+this.options[this.selectedIndex].value\'>' );
        $xoops->tpl()->assign('category_footer', '</select></div>' );
    } elseif ( $Partners_config['xoopartners_category']['display_mode'] == 'table' ) {        $xoops->tpl()->assign('category_header', '<table class="outer">' );
        $xoops->tpl()->assign('category_footer', '</table>' );
    }
} else {    $partners = $partners_handler->GetPartners(0, 'order', 'asc', $start, $Partners_config['xoopartners_partner']['limit_main']);
    $xoops->tpl()->assign('partners', $partners);
    $partners_count = $partners_handler->getCount($criteria);
    $extra = '';
}

$description = '';
if ( isset($categories) ) {    foreach ($categories as $k => $category ) {
        $description .= $category['xoopartners_category_title'];
    }
}
$i=0;
foreach ($partners as $k => $partner ) {
    $description .= $partner['xoopartners_title'];
    $i++;
    if ( $i < count($partners) ) {
        $description .= ', ';
    }
}
$xoops->theme()->addMeta($type = 'meta', 'description', getMetaDescription( $description ) );
$xoops->theme()->addMeta($type = 'meta', 'keywords', getMetaKeywords( $description ) );

// Page navigation
$paginate = new Xoopaginate($partners_count, $Partners_config['xoopartners_partner']['limit_main'], $start, 'start', $extra);

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footer.php';
?>