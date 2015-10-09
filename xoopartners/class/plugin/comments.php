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

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * Class XoopartnersCommentsPlugin
 */
class XoopartnersCommentsPlugin extends Xoops\Module\Plugin\PluginAbstract implements CommentsPluginInterface
{
    /**
     * @return string
     */
    public function itemName()
    {
        return 'partner_id';
    }

    /**
     * @return string
     */
    public function pageName()
    {
        return 'partner.php';
    }

    /**
     * @return array
     */
    public function extraParams()
    {
        return array();
    }

    /**
     * This method will be executed upon successful post of an approved comment.
     * This includes comment posts by administrators, and change of comment status from 'pending' to 'active' state.
     * An CommentsComment object that has been approved will be passed as the first and only parameter.
     * This should be useful for example notifying the item submitter of a comment post.
     *
     * @param CommentsComment $comment
     *
     * @return void
     */
    public function approve(CommentsComment $comment)
    {
        //Where are you looking at?
    }

    /**
     * This method will be executed whenever the total number of 'active' comments for an item is changed.
     *
     * @param int $xoopartners_id
     * @param int $total_num The total number of active comments
     *
     * @internal param int $item_id The unique ID of an item
     */
    public function update($xoopartners_id, $total_num)
    {
        $db  = Xoops::getInstance()->db();
        $sql = 'UPDATE ' . $db->prefix('xoopartners') . ' SET xoopartners_comments = ' . (int)($total_num) . ' WHERE xoopartners_id = ' . (int)($xoopartners_id);
        $db->query($sql);
    }

    /**
     * This method will be executed whenever a new comment form is displayed.
     * You can set a default title for the comment and a header to be displayed on top of the form
     * ex: return array(
     *      'title' => 'My Article Title',
     *      'text' => 'Content of the article');
     *      'timestamp' => time(); //Date of the article in unix format
     *      'uid' => Id of the article author
     *
     * @param int $xoopartners_id
     * @return array
     * @internal param int $item_id The unique ID of an item
     *
     */
    public function itemInfo($xoopartners_id)
    {
        $ret = array();

        $partners_module  = Xoopartners::getInstance();
        $partnersHandler = $partners_module->partnersHandler();
        $page             = $page = $partnersHandler->get($xoopartners_id);

        $ret['text']      = $page->getVar('xoopartners_description');
        $ret['title']     = $page->getVar('xoopartners_title');
        $ret['uid']       = $page->getVar('xoopartners_uid');
        $ret['timestamp'] = $page->getVar('xoopartners_published');

        return $ret;
    }
}
