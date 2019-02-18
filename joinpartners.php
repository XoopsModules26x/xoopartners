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

 */
use Xoops\Core\Request;

include __DIR__ . '/header.php';

$op = '';
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}
if (isset($_GET)) {
    foreach ($_GET as $k => $v) {
        ${$k} = $v;
    }
}

switch ($op) {
    case 'save':
        if (!$xoops->security()->check()) {
            $xoops->redirect('index.php', 5, implode(',', $xoops->security()->getErrors()));
        }

        //        $xoopartners_id = $system->cleanVars($_POST, 'xoopartners_id', 0, 'int'); //mb
        $xoopartners_id = Request::getInt('xoopartners_id', 0, 'POST');
        if (isset($xoopartners_id) && $xoopartners_id > 0) {
            $partner = $partnersHandler->get($xoopartners_id);
        } else {
            $partner = $partnersHandler->create();
        }

        $partner->cleanVarsForDB();

        // uploads images
        $myts = \MyTextSanitizer::getInstance();
        $uploadImages = $partnersHandler->uploadImages($partner->getVar('xoopartners_title'));

        if (is_array($uploadImages) && 0 != count($uploadImages)) {
            foreach ($uploadImages as $k => $response) {
                if (true === $response['error']) {
                    $errors[] = $response['message'];
                } else {
                    $partner->setVar($k, $response['filename']);
                }
            }
        } else {
            //            $partner->setVar('xoopartners_image', $myts->htmlSpecialChars($_POST['image_list']));
            $partner->setVar('xoopartners_image', $myts->htmlSpecialChars(Request::getString('image_list', '', 'POST')));
        }

        if ($partner_id = $partnersHandler->insert($partner)) {
            $msg = _XOO_PARTNERS_SAVED;
            if (isset($errors) && 0 != count($errors)) {
                $msg .= '<br>' . implode('<br>', $errors);
            }
            $xoops->redirect('index.php', 5, $msg);
        }
        break;
    default:
        $partner = $partnersHandler->create();
        $form = $helper->getForm($partner, 'partners');
        $form->display();
        break;
}
include __DIR__ . '/footer.php';
