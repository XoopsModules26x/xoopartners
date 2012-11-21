<?php
defined('XOOPS_ROOT_PATH') or die('Restricted access');

return $config = array(
    'xoopartners_welcome'   => '',
    'xoopartners_main_mode' => 'list',

    'xoopartners_category'  => array(
        'use_categories'    => false,
        'display_mode'      => 'list',
        'main_menu'         => 1,
        'image_size'        => 100000,
        'image_width'       => 100,
        'image_height'      => 100,
    ),

    'xoopartners_partner'   => array(
        'display_mode'      => 'list',
        'image_size'        => 100000,
        'image_width'       => 100,
        'image_height'      => 100,
    ),

    'xoopartners_qrcode'    => array(
        'use_qrcode'        => 0,
        'CorrectionLevel'   => 'L',
        'matrixPointSize'   => 2,
        'whiteMargin'       => 0,
/*
        'backgroundColor'   => 'FFFFFF',
        'foregroundColor'   => '000000',
*/
    ),
);
?>