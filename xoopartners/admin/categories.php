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

include dirname(__FILE__) . '/header.php';

switch ($op) {    case 'save':
    if ( !$xoops->security()->check() ) {
        $xoops->redirect('categories.php', 5, implode(',', $xoops->security()->getErrors()));
    }

    $xoopartners_category_id = $system->CleanVars($_POST, 'xoopartners_category_id', 0, 'int');
    if( isset($xoopartners_category_id) && $xoopartners_category_id > 0 ){
        $category = $categories_handler->get($xoopartners_category_id);
    } else {
        $category = $categories_handler->create();
    }

    $category->CleanVarsForDB();

    // uploads images
    $myts = MyTextSanitizer::getInstance();
    $upload_images = $categories_handler->upload_images( $category->getVar('xoopartners_category_title') );

    if ( is_array( $upload_images ) && count( $upload_images) != 0 ) {
        foreach ($upload_images as $k => $reponse ) {
            if ( $reponse['error'] == true ) {
                $errors[] = $reponse['message'];
            } else {
                $category->setVar( $k, $reponse['filename'] );
            }
        }
    } else {
        $category->setVar('xoopartners_category_image', $myts->htmlspecialchars( $_POST['image_list'] ) );
    }


    if ($categories_handler->insert($category)) {
        $msg = _AM_XOO_PARTNERS_CATEGORY_SAVED;
        if ( isset($errors) && count($errors) != 0) {
            $msg .= '<br />' . implode('<br />', $errors);;
        }
        $xoops->redirect('categories.php', 5, $msg);
    }
    break;

    case 'add':
    $category = $categories_handler->create();
    $form = $xoops->getModuleForm($category, 'categories', 'xoopartners');
    $form->CategoryForm();
    $form->display();
    break;
    case 'edit':
    $xoopartners_category_id = $system->CleanVars($_REQUEST, 'xoopartners_category_id', 0, 'int');
    $category = $categories_handler->get($xoopartners_category_id);
    $form = $xoops->getModuleForm($category, 'categories', 'xoopartners');
    $form->CategoryForm();
    $form->display();
    break;

    case 'view':
    case 'hide';
    $xoopartners_category_id = $system->CleanVars($_REQUEST, 'xoopartners_category_id', 0, 'int');
    $categories_handler->SetOnline($xoopartners_category_id);
    $xoops->redirect('categories.php', 5, _AM_XOO_PARTNERS_CATEGORY_SAVED);
    break;

    default:
    $admin_page->addItemButton(_AM_XOO_PARTNERS_CATEGORY_ADD, 'categories.php?op=add', 'add');
    $admin_page->renderButton();

    $xoops->tpl()->assign('categories', $categories_handler->renderAdminList() );
    break;}

include dirname(__FILE__) . '/footer.php';
?>