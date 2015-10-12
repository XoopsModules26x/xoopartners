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

use Xoops\Core\Request;

include __DIR__ . '/header.php';

switch ($op) {
    case 'save':
        if (!$xoops->security()->check()) {
            $xoops->redirect('categories.php', 5, implode(',', $xoops->security()->getErrors()));
        }

        //        $xoopartners_category_id = $system->cleanVars($_POST, 'xoopartners_category_id', 0, 'int');
        $xoopartners_category_id = Request::getInt('xoopartners_category_id', 0, 'POST');
        if (isset($xoopartners_category_id) && $xoopartners_category_id > 0) {
            $category = $categoriesHandler->get($xoopartners_category_id);
        } else {
            $category = $categoriesHandler->create();
        }

        $category->cleanVarsForDB();

        // uploads images
        $myts          = MyTextSanitizer::getInstance();
        $upload_images = $categoriesHandler->uploadImages($category->getVar('xoopartners_category_title'));

        if (is_array($upload_images) && count($upload_images) != 0) {
            foreach ($upload_images as $k => $reponse) {
                if ($reponse['error'] == true) {
                    $errors[] = $reponse['message'];
                } else {
                    $category->setVar($k, $reponse['filename']);
                }
            }
        } else {
            //            $category->setVar('xoopartners_category_image', $myts->htmlSpecialChars($_POST['image_list']));
            $category->setVar('xoopartners_category_image', $myts->htmlSpecialChars(Request::getString('image_list', '', 'POST')));
        }

        if ($categoriesHandler->insert($category)) {
            $msg = _AM_XOO_PARTNERS_CATEGORY_SAVED;
            if (isset($errors) && count($errors) != 0) {
                $msg .= '<br />' . implode('<br />', $errors);
            }
            $xoops->redirect('categories.php', 5, $msg);
        }
        break;

    case 'add':
        $category = $categoriesHandler->create();
        $form     = $xoopartnersModule->getForm($category, 'categories');
        $form->display();
        break;

    case 'edit':
        $xoopartners_category_id = Request::getInt('xoopartners_category_id', 0); //$system->cleanVars($_REQUEST, 'xoopartners_category_id', 0, 'int');
        $category                = $categoriesHandler->get($xoopartners_category_id);
        $form                    = $xoopartnersModule->getForm($category, 'categories');
        $form->display();
        break;

    case 'view':
    case 'hide':
        $xoopartners_category_id = Request::getInt('xoopartners_category_id', 0); //$system->cleanVars($_REQUEST, 'xoopartners_category_id', 0, 'int');
        $categoriesHandler->setOnline($xoopartners_category_id);
        $xoops->redirect('categories.php', 5, _AM_XOO_PARTNERS_CATEGORY_SAVED);
        break;

    default:
        $admin_page->addItemButton(_AM_XOO_PARTNERS_CATEGORY_ADD, 'categories.php?op=add', 'add');
        $admin_page->renderButton();

        $xoops->tpl()->assign('categories', $categoriesHandler->renderAdminList());
        break;
}

include __DIR__ . '/footer.php';
