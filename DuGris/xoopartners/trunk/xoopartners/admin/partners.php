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

$category_id = $system->CleanVars($_REQUEST, 'category_id', 0, 'int');

switch ($op) {    case 'save':
    if ( !$xoops->security()->check() ) {
        $xoops->redirect('partners.php?category_id=' . $category_id, 5, implode(',', $xoops->security()->getErrors()));
    }

    $xoopartners_id = $system->CleanVars($_POST, 'xoopartners_id', 0, 'int');
    if( isset($xoopartners_id) && $xoopartners_id > 0 ){
        $partner = $partners_handler->get($xoopartners_id);
        $category_id = $partner->getVar('xoopartners_category');
        $isnew = false;
    } else {
        $partner = $partners_handler->create();
        $category_id = 0;
        $isnew = true;
    }

    $partner->CleanVarsForDB();

    // uploads images
    $myts = MyTextSanitizer::getInstance();
    $upload_images = $partners_handler->upload_images( $partner->getVar('xoopartners_title') );

    if ( is_array( $upload_images ) && count( $upload_images) != 0 ) {
        foreach ($upload_images as $k => $reponse ) {
            if ( $reponse['error'] == true ) {
                $errors[] = $reponse['message'];
            } else {
                $partner->setVar( $k, $reponse['filename'] );
            }
        }
    } else {
        $partner->setVar('xoopartners_image', $myts->htmlspecialchars( $_POST['image_list'] ) );
    }

    if ($partners_handler->insert($partner)) {        if ($isnew) {            $xoopartners_id = $xoops->db()->getInsertId();
        }        $msg = _AM_XOO_PARTNERS_SAVED;
        $partner = $partners_handler->get( $xoopartners_id );

        // tags
        if ( $xoops->registry()->offsetExists('XOOTAGS') && $xoops->registry()->get('XOOTAGS') ) {
            $xootags_handler = $xoops->getModuleHandler('xootags_tags', 'xootags');
            $msg .= '<br />' . $xootags_handler->updateByItem( 'tags', $xoopartners_id) ;
        }

        if ($partner->getVar('xoopartners_accepted')) {            if ($category_id != $partner->getVar('xoopartners_category')) {                $categories_handler->Delpartner( $category_id );
                $categories_handler->Addpartner( $partner->getVar('xoopartners_category') );
            }        }

        if ( isset($errors) && count($errors) != 0) {
            $msg .= '<br />' . implode('<br />', $errors);;
        }

        if ($isnew) {
            $partner->setPost(true);
        }
        $xoops->redirect('partners.php?category_id=' . $partner->getVar('xoopartners_category'), 5, $msg);
    }
    break;

    case 'add';
    $partner = $partners_handler->create();
    $form = $xoops->getModuleForm($partner, 'partners', 'xoopartners');
    $form->PartnerForm( $category_id );
    $form->display();
    break;

    case 'edit':
    $xoopartners_id = $system->CleanVars($_REQUEST, 'xoopartners_id', 0, 'int');
    $partner = $partners_handler->get( $xoopartners_id);
    $form = $xoops->getModuleForm($partner, 'partners', 'xoopartners');
    $form->PartnerForm( $category_id );
    $form->display();
    break;

    case 'del':
    $xoopartners_id = $system->CleanVars($_REQUEST, 'xoopartners_id', 0, 'int');
    if( isset($xoopartners_id) && $xoopartners_id > 0 ){
        if ($partner = $partners_handler->get($xoopartners_id) ) {
            $delete = $system->CleanVars( $_POST, 'ok', 0, 'int' );
            if ($delete == 1) {
                if ( !$xoops->security()->check() ) {
                    $xoops->redirect('partners.php?category_id=' . $category_id, 5, implode(',', $xoops->security()->getErrors()));
                }
                // tags
                if ( $xoops->registry()->offsetExists('XOOTAGS') && $xoops->registry()->get('XOOTAGS') ) {
                    $xootags_handler = $xoops->getModuleHandler('xootags_tags', 'xootags');
                    $xootags_handler->deleteByItem($partner->getVar('xoopartners_id')) ;
                }
                $partner->setPost(false);
                $categories_handler->Delpartner( $partner->getVar('xoopartners_category') );
                $partners_handler->delete($partner);
                $xoops->redirect('partners.php?category_id=' . $category_id, 5, _AM_XOO_PARTNERS_DELETED);
            } else {
                $xoops->confirm(array('ok' => 1, 'xoopartners_id' => $xoopartners_id, 'category_id' => $category_id, 'op' => 'del'), $_SERVER['REQUEST_URI'], sprintf(_AM_XOO_PARTNERS_DELETE_CFM . "<br /><b><span style='color : Red'> %s </span></b><br /><br />", $partner->getVar('xoopartners_title')));
            }
        } else {
            $xoops->redirect('partners.php?category_id=' . $category_id, 5);
        }
    } else {
        $xoops->redirect('partners.php?category_id=' . $category_id, 5);
    }
    break;

    case 'view':
    case 'hide':
    $xoopartners_id = $system->CleanVars($_REQUEST, 'xoopartners_id', 0, 'int');
    $partners_handler->SetOnline($xoopartners_id);
    $xoops->redirect('partners.php?category_id=' . $category_id, 5, _AM_XOO_PARTNERS_SAVED);
    break;

    case 'accept':
    case 'naccept':
    $xoopartners_id = $system->CleanVars($_REQUEST, 'xoopartners_id', 0, 'int');
    $partners_handler->SetAccept($xoopartners_id);
    $xoops->redirect('partners.php?category_id=' . $category_id, 5, _AM_XOO_PARTNERS_SAVED);
    break;

    default:
    if ($Partners_config['xoopartners_category']['use_categories']) {        ob_start();
        $categories_handler->makeSelectBox('category_id', $category_id, true, 'window.location.href="partners.php?category_id="+this.options[this.selectedIndex].value');
        $xoops->tpl()->assign('categories', ob_get_contents() );
        ob_end_clean();
    }

    $admin_page->addItemButton(_AM_XOO_PARTNERS_ADD, 'partners.php?op=add', 'add');
    $admin_page->renderButton();

    $xoops->tpl()->assign('partners', $partners_handler->renderAdminList( $category_id ) );
    $xoops->tpl()->assign('category_id', $category_id );

    break;
}
include dirname(__FILE__) . '/footer.php';
?>