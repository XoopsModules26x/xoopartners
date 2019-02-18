<?php

namespace XoopsModules\Xoocontact;

/**
 * Xoocontact module
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
 * @package         xoocontact
 * @since           2.6.0
 * @author          Laurent JEN (Aka DuGris)
 */
use Xoops\Core\Database\Connection;
//use Xoops\Core\Kernel\XoopsObject;
//use Xoops\Core\Kernel\XoopsPersistableObjectHandler;
//use XoopsLoad;
use XoopsModules\Xoocontact;

/**
 * class XoocontactContactHandler
 */
class ContactHandler extends \Xoops\Core\Kernel\XoopsPersistableObjectHandler
{
    /**
     * ContactHandler constructor.
     * @param null|Connection $db database connection
     */
    public function __construct($db = null)
    {
        parent::__construct($db, 'xoocontact_fields', Contact::class, 'xoocontact_id', 'xoocontact_description');
    }

    /**
     * @return array
     */
    public function renderAdminList()
    {
        $criteria = new \CriteriaCompo();
        $criteria->setSort('xoocontact_order');
        $criteria->setOrder('asc');

        return $this->getObjects($criteria, true, false);
    }

    /**
     * @param bool $asObject
     *
     * @return array
     */
    public function getDisplay($asObject = true)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('xoocontact_display', 1));
        $criteria->setSort('xoocontact_order');
        $criteria->setOrder('asc');

        return $this->getObjects($criteria, true, $asObject);
    }
}
