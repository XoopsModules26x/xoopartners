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
use Xoops\Core\Request;

/**
 * Class XooPartnersRld
 */
class Rld extends \XoopsObject
{
    // constructor

    /**
     * XooPartnersRld constructor.
     */
    public function __construct()
    {
        $this->initVar('xoopartners_rld_id', XOBJ_DTYPE_INT, 0, true, 5);
        $this->initVar('xoopartners_rld_partner', XOBJ_DTYPE_INT, 1, false, 5);
        $this->initVar('xoopartners_rld_uid', XOBJ_DTYPE_INT, 0, true, 5);
        $this->initVar('xoopartners_rld_time', XOBJ_DTYPE_INT, time(), true, 10);
        $this->initVar('xoopartners_rld_ip', XOBJ_DTYPE_TXTBOX, '0.0.0.0', true, 15);
        $this->initVar('xoopartners_rld_rates', XOBJ_DTYPE_INT, 0, true, 5);
        $this->initVar('xoopartners_rld_like', XOBJ_DTYPE_INT, 0, true, 1);
        $this->initVar('xoopartners_rld_dislike', XOBJ_DTYPE_INT, 0, true, 1);
    }

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $class = __CLASS__;
            $instance = new $class();
        }

        return $instance;
    }

    public function cleanVarsForDB()
    {
        $system = \System::getInstance();
        foreach (parent::getValues() as $k => $v) {
            if ('dohtml' !== $k) {
                if (XOBJ_DTYPE_STIME == $this->vars[$k]['data_type'] || XOBJ_DTYPE_MTIME == $this->vars[$k]['data_type'] || XOBJ_DTYPE_LTIME == $this->vars[$k]['data_type']) {
//                    $value = $system->cleanVars($_POST[$k], 'date', date('Y-m-d'), 'date') + $system->cleanVars($_POST[$k], 'time', date('u'), 'int');
                    //TODO should we use here getString??
                    $value = Request::getArray('date', date('Y-m-d'), 'POST')[$k] + Request::getArray('time', date('u'), 'POST')[$k];
                    $this->setVar($k, isset($_POST[$k]) ? $value : $v);
                } elseif (XOBJ_DTYPE_INT == $this->vars[$k]['data_type']) {
                    $value = Request::getInt($k, $v, 'POST'); //$system->cleanVars($_POST, $k, $v, 'int');
                    $this->setVar($k, $value);
                } elseif (XOBJ_DTYPE_ARRAY == $this->vars[$k]['data_type']) {
                    $value = Request::getArray($k, $v, 'POST'); // $system->cleanVars($_POST, $k, $v, 'array');
                    $this->setVar($k, $value);
                } else {
                    $value = Request::getString($k, $v, 'POST'); //$system->cleanVars($_POST, $k, $v, 'string');
                    $this->setVar($k, $value);
                }
            }
        }
    }
}
