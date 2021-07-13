<?php
if (!defined('_MAIL_')) exit;
//include_once('./_common.php');
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
?>

<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="subject" content="" />
        <meta name="title" content="" />
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="author" content="" />
        <meta name="publisher" content="" />
        <meta name="other agent" content="" />
        <meta name="classification" content="<?=$protocol?><?=$_SERVER['HTTP_HOST']?><?=$_SERVER['REQUEST_URI']?>" />
        <meta name="generator" content="Webflow" />
        <meta name="Author" content="" />
        <meta name="location" content="" />
        <meta name="Copyright" content="" />
        <meta name="robots" content="ALL" />
        <meta property="og:title" content="" />
        <meta property="og:url" content="" />
        <meta property="og:image" content="" />
        <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo _URL?>/images/favicon/android-icon-192x192.png" />
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo _URL?>/images/favicon/favicon-32x32.png" />
        <link rel="icon" type="image/png" sizes="96x96" href="<?php echo _URL?>/images/favicon/favicon-96x96.png" />
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo _URL?>/images/favicon/favicon-16x16.png" />
        <link rel="manifest" href="<?php echo _URL?>/images/favicon/manifest.json" />
        <meta name="msapplication-TileColor" content="#ffffff" />
        <meta name="msapplication-TileImage" content="<?php echo _URL?>/images/favicon/ms-icon-144x144.png" />
        <meta name="theme-color" content="#ffffff" />
        <link rel="stylesheet" href="<?php echo _URL?>/css/dashboard.css?t=<?php echo time()?>">
        <title><?php echo _TITLE_?></title>
        <script>
        var _URL = "<?php echo _URL?>";
        </script>
        <script src="<?php echo _URL?>/js/jquery-3.3.1.min.js"></script>
    </head>
    <body>
        <div id="overlayer"></div>
        <div class="loader">
            <i class="xi-spinner-3 xi-3x"></i>
        </div>

        <div class="siteWrap">
            <header class="site-navbar">
                <div class="site-section">
                    <div class="siteLogo"><a href="<?php echo _URL?>/"><?php echo _TITLE_?></a></div>
                    <ul class="upmenu">
                        <?php if($user['userId']){?>
                        <li><a href="<?php echo _URL?>/proc/logout" class="member logout"><span class="icon-sign-out"></span> LOG OUT</a></li>
                        <li><a href="<?php echo _URL?>/dashboard" class="member"><span class="icon-user"></span> MY PAGE</a></li>
                        <?php }else{?>
                        <li><a href="<?php echo _URL?>/login"><i class="xi-log-in"></i> LOGIN</a></li>
                        <li><a href="<?php echo _URL?>/register"><i class="xi-user-plus-o"></i> SIGN UP</a></li>
                        <?php }?>
                    </ul>
                    <div class="clear"></div>
                </div>
            </header>
