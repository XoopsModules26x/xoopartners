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
 * Class XoopartnersSearchPlugin
 */
class SearchPlugin extends \Xoops\Module\Plugin\PluginAbstract implements \SearchPluginInterface
{
    /**
     * @param string[] $queries
     * @param string   $andor
     * @param int      $limit
     * @param int      $start
     * @param type     $uid
     * @return array
     */
    public function search($queries, $andor, $limit, $start, $uid)
    {
        $searchstring = '';
        $ret = [];

        $criteria = new \CriteriaCompo();

        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $criteria->setSort('xoopartners_order');
        $criteria->setOrder('ASC');

        $criteria->add(new \Criteria('xoopartners_online', 1));
        $criteria->add(new \Criteria('xoopartners_accepted', 1));
        $criteria->add(new \Criteria('xoopartners_published', 0, '>'));
        $criteria->add(new \Criteria('xoopartners_published', time(), '<='));

        if (is_array($queries) && $count = count($queries)) {
            foreach ($queries as $k => $v) {
                $criteria_content = new \CriteriaCompo();
                $criteria_content->add(new \Criteria('xoopartners_title', '%' . $v . '%', 'LIKE'), 'OR');
                $criteria_content->add(new \Criteria('xoopartners_description', '%' . $v . '%', 'LIKE'), 'OR');
                $criteria->add($criteria_content, $andor);
            }
        }

        if (0 != $uid) {
            $criteria->add(new \Criteria('xoopartners_uid', $uid));
        }

        $helper = \XoopsModules\Xoopartners\Helper::getInstance();
        $partnersHandler = $helper->getHandler('Partners');

        $partners = $partnersHandler->getObjects($criteria, true, false);

        $k = 0;
        foreach ($partners as $partner) {
            $ret[$k]['image'] = 'assets/icons/logo_small.png';
            $ret[$k]['link'] = 'partner.php?partner_id=' . $partner['xoopartners_id'] . '' . $searchstring;
            $ret[$k]['title'] = $partner['xoopartners_title'];
            $ret[$k]['time'] = $partner['xoopartners_time'];
            $ret[$k]['uid'] = $partner['xoopartners_uid'];
            $ret[$k]['content'] = $partner['xoopartners_description'];
            ++$k;
        }

        return $ret;
    }
}
