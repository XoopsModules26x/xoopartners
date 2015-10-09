<!DOCTYPE html>
<html lang="<{$xoops_langcode}>">
<head>
    <!-- Title and meta -->
    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>"/>
    <title><{$xoops_pagetitle}> - <{$xoops_sitename}></title>
    <meta name="robots" content="noindex,nofollow"/>

    <meta name="generator" content="XOOPS"/>

    <!-- Xoops style sheet -->
    <link rel="stylesheet" type="text/css" media="screen,print" href="<{xoAppUrl 'xoops.css'}>"/>
    <link rel="stylesheet" type="text/css" media="screen,print" href="<{xoAppUrl 'media/xoops/css/icons.css'}>"/>
    <link rel="stylesheet" type="text/css" media="screen,print" href="<{xoImgUrl 'media/bootstrap/css/bootstrap.css'}>"/>
    <link rel="stylesheet" type="text/css" media="screen,print" href="<{xoImgUrl 'media/bootstrap/css/xoops.bootstrap.css'}>"/>

    <!-- Theme style sheet -->
    <link rel="stylesheet" type="text/css" media="screen,print" href="<{xoImgUrl 'css/style.css'}>"/>
    <!-- Module style sheet -->
    <link rel="stylesheet" type="text/css" media="screen,print" href="<{xoImgUrl 'modules/xoopartners/assets/css/module.css'}>"/>

    <!--[if lte IE 8]>
    <link rel="stylesheet" href="<{xoImgUrl 'styleIE8.css'}>" type="text/css"/>
    <![endif]-->

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>

<body class="<{$xoops_langcode}>">

<div class="xo-hero">
    <div class="container xo-hero-content">
        <div class="span7">
            <h2><{$xoops_sitename}></h2>
        </div>
        <div class="pull-right">
            <p class="btn btn-warning btn-large"><{$xoops_slogan}></p>
        </div>
    </div>
</div>

<div class="container">
    <{include file='module:xoopartners/xoopartners_partners_css.tpl'}>
    <{include file='module:xoopartners/xoopartners_partner.tpl'}>
</div>
</body>
</html>
