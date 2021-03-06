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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         Xoopartners
 * @since           2.6.0
 * @author          Laurent JEN (Aka DuGris)

 * @param mixed $options
 */

/**
 * @param $options
 * @return mixed
 */
function xoopartners_show($options)
{
    $xoops = \Xoops::getInstance();
    $xoops->theme()->addStylesheet('modules/xoopartners/assets/css/module.css');
    $xoops->theme()->addStylesheet('modules/xoopartners/assets/css/blocks.css');

    $helper = \XoopsModules\Xoopartners\Helper::getInstance();
    $partnersConfig = $helper->loadConfig();
    $partnersHandler = $helper->getHandler('Partners');

    $block['template'] = $options[0];
    $options[3] = isset($options[3]) ? $options[3] : -1;
    $block['partners'] = $partnersHandler->getPartners($options[3], $options[1], $options[2], 0, $options[3]);
    $block['xoopartners_partner'] = $partnersConfig['xoopartners_partner'];

    $xoops->tpl()->assign('xoopartners_category', $partnersConfig['xoopartners_category']);
    $xoops->tpl()->assign('xoopartners_partner', $partnersConfig['xoopartners_partner']);

    return $block;
}

/**
 * @param $options
 * @return string
 */
function xoopartners_edit($options)
{
    $block_form = new \XoopsBlockForm();

    $display_mode = new \Xoops\Form\Select(_MB_XOO_PARTNERS_MODE . ' : ', 'options[0]', $options[0]);
    $display_mode->addOption('list', _MB_XOO_PARTNERS_MODE_LIST);
    $display_mode->addOption('table', _MB_XOO_PARTNERS_MODE_TABLE);
    $display_mode->addOption('news', _MB_XOO_PARTNERS_MODE_NEWS);
    $display_mode->addOption('images', _MB_XOO_PARTNERS_MODE_IMAGES);
    $block_form->addElement($display_mode);

    $helper = \XoopsModules\Xoopartners\Helper::getInstance();
    $partnersConfig = $helper->loadConfig();
    $categoriesHandler = $helper->getHandler('Categories');

    $sort_mode = new \Xoops\Form\Select(_MB_XOO_PARTNERS_SORT . ' : ', 'options[1]', $options[1]);
    $sort_mode->addOption('id', _MB_XOO_PARTNERS_SORT_ID);
    $sort_mode->addOption('order', _MB_XOO_PARTNERS_SORT_ORDER);
    $sort_mode->addOption('published', _MB_XOO_PARTNERS_SORT_RECENTS);
    $sort_mode->addOption('hits', _MB_XOO_PARTNERS_SORT_HITS);

    if ('none' !== $partnersConfig['xoopartners_rld']['rld_mode']) {
        if ('rate' === $partnersConfig['xoopartners_rld']['rld_mode']) {
            $sort_mode->addOption('rates', _MB_XOO_PARTNERS_SORT_RATES);
        } else {
            $sort_mode->addOption('like', _MB_XOO_PARTNERS_SORT_LIKE);
            $sort_mode->addOption('dislike', _MB_XOO_PARTNERS_SORT_DISLIKE);
        }
    }
    $sort_mode->addOption('random', _MB_XOO_PARTNERS_SORT_RANDOM);
    $block_form->addElement($sort_mode);

    $order_mode = new \Xoops\Form\Select(_MB_XOO_PARTNERS_ORDER . ' : ', 'options[2]', $options[2]);
    $order_mode->addOption('asc', _MB_XOO_PARTNERS_ORDER_ASC);
    $order_mode->addOption('desc', _MB_XOO_PARTNERS_ORDER_DESC);
    $block_form->addElement($order_mode);

    if ($partnersConfig['xoopartners_category']['use_categories']) {
        ob_start();
        $categoriesHandler->makeSelectBox('options[3]', $options[3]);
        $block_form->addElement(new \Xoops\Form\Label(_MB_XOO_PARTNERS_CATEGORY_TITLE, ob_get_contents()));
        ob_end_clean();
    }

    $block_form->addElement(new \Xoops\Form\Text(_MB_XOO_PARTNERS_LIMIT, 'options[3]', 1, 2, $options[3]));

    return $block_form->render();
}
