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

include __DIR__ . '/header.php';

$category_id = $system->cleanVars($_REQUEST, 'category_id', 0, 'int');

switch ($op) {
    case 'save':
        if (!$xoops->security()->check()) {
            $xoops->redirect('partners.php?category_id=' . $category_id, 5, implode(',', $xoops->security()->getErrors()));
        }

        $xoopartners_id = $system->cleanVars($_POST, 'xoopartners_id', 0, 'int');
        if (isset($xoopartners_id) && $xoopartners_id > 0) {
            $partner     = $partnersHandler->get($xoopartners_id);
            $category_id = $partner->getVar('xoopartners_category');
            $isnew       = false;
        } else {
            $partner     = $partnersHandler->create();
            $category_id = 0;
            $isnew       = true;
        }

        $partner->cleanVarsForDB();

        // uploads images
        $myts          = MyTextSanitizer::getInstance();
        $upload_images = $partnersHandler->uploadImages($partner->getVar('xoopartners_title'));

        if (is_array($upload_images) && count($upload_images) != 0) {
            foreach ($upload_images as $k => $reponse) {
                if ($reponse['error'] == true) {
                    $errors[] = $reponse['message'];
                } else {
                    $partner->setVar($k, $reponse['filename']);
                }
            }
        } else {
            $partner->setVar('xoopartners_image', $myts->htmlSpecialChars($_POST['image_list']));
        }

        if ($xoopartners_id = $partnersHandler->insert($partner)) {
            $msg = _AM_XOO_PARTNERS_SAVED;
            if (isset($errors) && count($errors) != 0) {
                $msg .= '<br />' . implode('<br />', $errors);
            }

            // tags
            if ($xoops->registry()->offsetExists('XOOTAGS') && $xoops->registry()->get('XOOTAGS')) {
                $xootagsHandler = $xoops->getModuleHandler('xootags_tags', 'xootags');
                $msg .= '<br />' . $xootagsHandler->updateByItem('tags', $xoopartners_id);
            }

            if ($partner->getVar('xoopartners_accepted')) {
                if ($category_id != $partner->getVar('xoopartners_category')) {
                    $categoriesHandler->delPartner($category_id);
                    $categoriesHandler->addPartner($partner->getVar('xoopartners_category'));
                }
            }

            if ($isnew) {
                $partner->setPost(true);

                //notifications
                $partner->sendNotifications();
            }
            $xoops->redirect('partners.php?category_id=' . $partner->getVar('xoopartners_category'), 5, $msg);
        }
        break;

    case 'add':
        $partner = $partnersHandler->create();
        $form    = $xoopartners_module->getForm($partner, 'partners');
        $form->display();
        break;

    case 'edit':
        $xoopartners_id = $system->cleanVars($_REQUEST, 'xoopartners_id', 0, 'int');
        $partner        = $partnersHandler->get($xoopartners_id);
        $form           = $xoopartners_module->getForm($partner, 'partners');
        $form->display();
        break;

    case 'del':
        $xoopartners_id = $system->cleanVars($_REQUEST, 'xoopartners_id', 0, 'int');
        if (isset($xoopartners_id) && $xoopartners_id > 0) {
            if ($partner = $partnersHandler->get($xoopartners_id)) {
                $delete = $system->cleanVars($_POST, 'ok', 0, 'int');
                if ($delete == 1) {
                    if (!$xoops->security()->check()) {
                        $xoops->redirect('partners.php?category_id=' . $category_id, 5, implode(',', $xoops->security()->getErrors()));
                    }
                    // tags
                    if ($xoops->registry()->offsetExists('XOOTAGS') && $xoops->registry()->get('XOOTAGS')) {
                        $xootagsHandler = $xoops->getModuleHandler('xootags_tags', 'xootags');
                        $xootagsHandler->deleteByItem($partner->getVar('xoopartners_id'));
                    }
                    $partner->setPost(false);
                    $categoriesHandler->delPartner($partner->getVar('xoopartners_category'));
                    $partnersHandler->delete($partner);
                    $xoops->redirect('partners.php?category_id=' . $category_id, 5, _AM_XOO_PARTNERS_DELETED);
                } else {
                    $xoops->confirm(array('ok'             => 1,
                                          'xoopartners_id' => $xoopartners_id,
                                          'category_id'    => $category_id,
                                          'op'             => 'del'), $_SERVER['REQUEST_URI'], sprintf(_AM_XOO_PARTNERS_DELETE_CFM . "<br /><b><span style='color : Red'> %s </span></b><br /><br />", $partner->getVar('xoopartners_title')));
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
        $xoopartners_id = $system->cleanVars($_REQUEST, 'xoopartners_id', 0, 'int');
        $partnersHandler->setOnline($xoopartners_id);
        $xoops->redirect('partners.php?category_id=' . $category_id, 5, _AM_XOO_PARTNERS_SAVED);
        break;

    case 'accept':
    case 'naccept':
        $xoopartners_id = $system->cleanVars($_REQUEST, 'xoopartners_id', 0, 'int');
        $partnersHandler->setAccept($xoopartners_id);
        $xoops->redirect('partners.php?category_id=' . $category_id, 5, _AM_XOO_PARTNERS_SAVED);
        break;

    default:
        $online = $system->cleanVars($_REQUEST, 'online', -1, 'int');
        if ($partners_config['xoopartners_category']['use_categories']) {
            ob_start();
            $categoriesHandler->makeSelectBox('category_id', $category_id, true, 'window.location.href="partners.php?category_id="+this.options[this.selectedIndex].value');
            $xoops->tpl()->assign('categories', ob_get_contents());
            ob_end_clean();
        }

        $admin_page->addItemButton(_AM_XOO_PARTNERS_ADD, 'partners.php?op=add', 'add');
        $admin_page->renderButton();

        $xoops->tpl()->assign('partners', $partnersHandler->renderAdminList($category_id, $online));
        $xoops->tpl()->assign('category_id', $category_id);

        break;
}
include __DIR__ . '/footer.php';
