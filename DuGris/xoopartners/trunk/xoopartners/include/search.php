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

function xoopartners_search($queryarray, $andor, $limit, $offset, $userid)
{    $xoops = Xoops::getInstance();
    $searchstring = '';
    $ret = array();

    $criteria = new CriteriaCompo();

	$criteria->setLimit($limit);
	$criteria->setStart($offset);
	$criteria->setSort('xoopartners_order');
	$criteria->setOrder('ASC');

    $criteria->add( new Criteria('xoopartners_online', 1) ) ;
    $criteria->add( new Criteria('xoopartners_accepted', 1) ) ;
    $criteria->add( new Criteria('xoopartners_published', 0, '>') ) ;
    $criteria->add( new Criteria('xoopartners_published', time(), '<=') ) ;

	if ( is_array($queryarray) && $count = count($queryarray) ) {
        foreach ($queryarray as $k => $v) {            $criteria_content = new CriteriaCompo();
            $criteria_content->add( new Criteria('xoopartners_title', '%' . $v . '%', 'LIKE'), 'OR' ) ;
            $criteria_content->add( new Criteria('xoopartners_description', '%' . $v . '%', 'LIKE'), 'OR' ) ;
            $criteria->add( $criteria_content, $andor);
        }
    }

	if ( $userid != 0 ) {
        $criteria->add( new Criteria('xoopartners_uid', $userid) ) ;
	}

    $partners_handler = $xoops->getModuleHandler('xoopartners', 'xoopartners');
    $partners = $partners_handler->getObjects($criteria, false, false);

    foreach ( $partners as $k => $partner ) {        $ret[$k]['image']    = 'icons/logo_small.png';
        $ret[$k]['link']     = 'partner.php?partner_id=' . $partner['xoopartners_id'] . '' . $searchstring;
        $ret[$k]['title']    = $partner['xoopartners_title'];
        $ret[$k]['time']     = $partner['xoopartners_time'];
        $ret[$k]['uid']      = $partner['xoopartners_uid'];
        $ret[$k]['content']  = $partner['xoopartners_description'];
    }
    return $ret;
}
?>