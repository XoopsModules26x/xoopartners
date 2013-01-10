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

class XoopartnersSystemPlugin extends Xoops_Module_Plugin_Abstract implements SystemPluginInterface
{
    public function userPosts($uid)
    {        $partners_module = Xoopartners::getInstance();
        $partners_handler = $partners_module->PartnersHandler();

        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria('xoopartners_online', 1) ) ;
        $criteria->add( new Criteria('xoopartners_published', time(), '<=') ) ;
        $criteria->add( new Criteria('xoopartners_uid', $uid) );

        return $partners_handler->getCount($criteria);
    }

    public function waiting()
    {
        $partners_module = Xoopartners::getInstance();
        $partners_handler = $partners_module->PartnersHandler();
        $criteria = new CriteriaCompo(new Criteria('xoopartners_online', 0));
        $criteria->add(new Criteria('xoopartners_accepted', 0), 'OR');
        if ($count = $partners_handler->getCount($criteria)) {            $ret['count'] = $count;
            $ret['name'] = Xoops::getInstance()->getHandlerModule()->getBydirname('xoopartners')->getVar('name');
            $ret['link'] = Xoops::getInstance()->url('modules/xoopartners/admin/partners.php?online=0');
            return $ret;
        }
        return false;
    }


    public function backend($limit=10)
    {
        $xoops = Xoops::getInstance();

        $partners_module = Xoopartners::getInstance();
        $partners_config = $partners_module->LoadConfig();
        $partners_handler = $partners_module->PartnersHandler();

        $ret = array();

        $partners = $partners_handler->GetPartners(0, 'order', 'asc', 0, $limit);
        foreach ($partners as $k => $partner) {
            $ret[$k]['title']   = $partner['xoopartners_title'];
            $ret[$k]['link']    = $xoops->url('modules/xoopartners/partner.php?partner_id=' . $partner['xoopartners_id']);
            $ret[$k]['content'] = $partner['xoopartners_description'];
            $ret[$k]['date']    = $partner['xoopartners_time'];
        }
        return $ret;
    }

    public function userMenus()
    {
        return array();
    }
}