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

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

$op = '';
if ( isset( $_POST ) ){
    foreach ( $_POST as $k => $v )  {
        ${$k} = $v;
    }
}
if ( isset( $_GET ) ){
    foreach ( $_GET as $k => $v )  {
        ${$k} = $v;
    }
}

switch ($op) {
    case 'save':
    if ( !$xoops->security->check() ) {
        $xoops->redirect('index.php', 5, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $xoopartners_id = $system->CleanVars($_POST, 'xoopartners_id', 0, 'int');
    if( isset($xoopartners_id) && $xoopartners_id > 0 ){
        $partner = $partners_handler->get($xoopartners_id);
    } else {
        $partner = $partners_handler->create();
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

    if ($partner_id = $partners_handler->insert($partner)) {
        $msg = _XOO_PARTNERS_SAVED;
        if ( isset($errors) && count($errors) != 0) {
            $msg .= '<br />' . implode('<br />', $errors);;
        }
        $xoops->redirect('index.php', 5, $msg);
    }
    break;

    default:
    $partner = $partners_handler->create();
    $form = $xoops->getModuleForm($partner, 'partners', 'xoopartners');
    $form->PartnerForm();
    $form->render();
    break;
}

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footer.php';
?>