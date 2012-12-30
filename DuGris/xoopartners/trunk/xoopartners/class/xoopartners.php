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

class Xoopartners extends Xoops_Module_Helper_Abstract
{
    /**
     * Init the module
     *
     * @return null|void
     */
    public function init()
    {
        $this->setDirname('xoopartners');
        @$this->loadLanguage('common');
        @$this->loadLanguage('preferences');

        XoopsLoad::load('xoopaginate', $this->_dirname);
    }

    public function LoadConfig()
    {
        XoopsLoad::load('xoopreferences', $this->_dirname);
        return XooPartnersPreferences::getInstance()->getConfig();
    }

    public function CategoriesHandler()
    {
        return $this->getHandler('xoopartners_categories');
    }

    public function PartnersHandler()
    {
        return $this->getHandler('xoopartners_partners');
    }

    public function RldHandler()
    {
        return $this->getHandler('xoopartners_rld');
    }
}
?>