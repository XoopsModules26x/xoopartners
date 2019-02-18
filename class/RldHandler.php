<?php

namespace XoopsModules\Xoopartners;

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
use Xoops\Core\Database\Connection;

/**
 * Class XoopartnersXooPartnersRldHandler
 */
class RldHandler extends \XoopsPersistableObjectHandler
{
    /**
     * XoopartnersXooPartnersRldHandler constructor.
     * @param Connection|null $db
     */
    public function __construct(Connection $db = null)
    {
        parent::__construct($db, 'xoopartners_rld', Rld::class, 'xoopartners_rld_id', 'xoopartners_rld_partner');
    }

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $class = __CLASS__;
            $instance = new $class($db);
        }

        return $instance;
    }

    /**
     * @param $partner_id
     * @return int
     */
    public function getVotes($partner_id)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('xoopartners_rld_partner', $partner_id));
        $criteria->add(new \Criteria('xoopartners_rld_rates', 0, '!='));

        return parent::getCount($criteria);
    }

    /**
     * @param $partner_id
     * @return int
     */
    public function getbyUser($partner_id)
    {
        $xoops = \Xoops::getInstance();
        $uid = $xoops->isUser() ? $xoops->user->getVar('uid') : 0;
        $ip = $xoops->getEnv('REMOTE_ADDR');

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('xoopartners_rld_partner', $partner_id));

        $criteria2 = new \CriteriaCompo();
        $criteria2->add(new \Criteria('xoopartners_rld_uid', $uid), 'OR');
        $criteria2->add(new \Criteria('xoopartners_rld_ip', $ip), 'OR');
        $criteria->add($criteria2, 'AND');
        $tmp = $this->getObjects($criteria, false, false);
        if (0 != count($tmp)) {
            return $tmp[0]['xoopartners_rld_rates'];
        }

        return 0;
    }

    /**
     * @param $partner_id
     * @param $like_dislike
     * @return bool
     */
    public function setLikeDislike($partner_id, $like_dislike)
    {
        $xoops = \Xoops::getInstance();
        $uid = $xoops->isUser() ? $xoops->user->getVar('uid') : 0;
        $ip = $xoops->getEnv('REMOTE_ADDR');
        $like = (1 == $like_dislike) ? 1 : 0;
        $dislike = (0 == $like_dislike) ? 1 : 0;

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('xoopartners_rld_partner', $partner_id));

        $criteria2 = new \CriteriaCompo();
        $criteria2->add(new \Criteria('xoopartners_rld_uid', $uid), 'OR');
        $criteria2->add(new \Criteria('xoopartners_rld_ip', $ip), 'OR');
        $criteria->add($criteria2, 'AND');
        $tmp = $this->getObjects($criteria, false, false);
        if (0 == count($tmp)) {
            $rldObject = $this->create();
            $rldObject->setVar('xoopartners_rld_partner', $partner_id);
            $rldObject->setVar('xoopartners_rld_uid', $uid);
            $rldObject->setVar('xoopartners_rld_time', time());
            $rldObject->setVar('xoopartners_rld_ip', $ip);
            $rldObject->setVar('xoopartners_rld_rates', 0);
            $rldObject->setVar('xoopartners_rld_like', $like);
            $rldObject->setVar('xoopartners_rld_dislike', $dislike);
            if ($this->insert($rldObject)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $partner_id
     * @param $vote
     * @return array|bool
     */
    public function setRate($partner_id, $vote)
    {
        $xoops = \Xoops::getInstance();
        $uid = $xoops->isUser() ? $xoops->user->getVar('uid') : 0;
        $ip = $xoops->getEnv('REMOTE_ADDR');

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('xoopartners_rld_partner', $partner_id));

        $criteria2 = new \CriteriaCompo();
        $criteria2->add(new \Criteria('xoopartners_rld_uid', $uid), 'OR');
        $criteria2->add(new \Criteria('xoopartners_rld_ip', $ip), 'OR');
        $criteria->add($criteria2, 'AND');
        $tmp = $this->getObjects($criteria, false, false);
        if (0 == count($tmp)) {
            $rldObject = $this->create();
            $rldObject->setVar('xoopartners_rld_partner', $partner_id);
            $rldObject->setVar('xoopartners_rld_uid', $uid);
            $rldObject->setVar('xoopartners_rld_time', time());
            $rldObject->setVar('xoopartners_rld_ip', $ip);
            $rldObject->setVar('xoopartners_rld_rates', $vote);
            $rldObject->setVar('xoopartners_rld_like', 0);
            $rldObject->setVar('xoopartners_rld_dislike', 0);
            if ($tmp = $this->insert($rldObject)) {
                return $this->getAverage($partner_id, $vote);
            }
        }

        return false;
    }

    /**
     * @param $partner_id
     * @param $vote
     * @return array
     */
    private function getAverage($partner_id, $vote)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('xoopartners_rld_partner', $partner_id));
        $criteria->add(new \Criteria('xoopartners_rld_rates', 0, '!='));

        $res = $this->getObjects($criteria, false, false);
        $rates = 0;
        $voters = 0;
        foreach ($res as $k => $v) {
            $rates += $v['xoopartners_rld_rates'];
            ++$voters;
        }

        return ['voters' => $voters, 'average' => ($rates / $voters), 'vote' => $vote];
    }
}
