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

class XoopartnersXootagsPlugin extends Xoops_Plugin_Abstract implements XootagsPluginInterface
{
    public function Xootags( $items )
    {
        $criteria = new CriteriaCompo();
        $criteria->setSort('xoopartners_order');
        $criteria->setOrder('ASC');

        $criteria->add( new Criteria('xoopartners_online', 1) ) ;
        $criteria->add( new Criteria('xoopartners_accepted', 1) ) ;
        $criteria->add( new Criteria('xoopartners_published', 0, '>') ) ;
        $criteria->add( new Criteria('xoopartners_published', time(), '<=') ) ;
        $criteria->add( new Criteria('xoopartners_id', '(' . implode(', ', $items) . ')', 'IN') ) ;

        $xoopartners_module = Xoopartners::getInstance();
        $partners_handler = $xoopartners_module->getHandler('xoopartners_partners');

        $partners = $partners_handler->getObjects($criteria, false, false);

        $ret = array();
        foreach ( $partners as $k =>  $partner ) {
            $k = $partner['xoopartners_time'];
            $ret[$k]['itemid']   = $partner['xoopartners_id'];
            $ret[$k]['title']    = $partner['xoopartners_title'];
            $ret[$k]['link']     = 'partner.php?partner_id=' . $partner['xoopartners_id'];
            $ret[$k]['time']     = $partner['xoopartners_published'];
            $ret[$k]['uid']      = $partner['xoopartners_uid'];
            $ret[$k]['content']  = $partner['xoopartners_description'];
        }
        return $ret;
    }
}