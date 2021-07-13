<?php
/********************
    상수 선언
********************/
// 이 상수가 정의되지 않으면 각각의 개별 페이지는 별도로 실행될 수 없음
define('_MAIL_', true);
define('_TITLE_', 'MAIL');
define('_MIAN_', false);

if(PHP_VERSION >= '5.1.0'){
    date_default_timezone_set("Asia/Seoul");
}

define('_USER_DIR', 'dashboard');
define('_NEW_USER_DIR', 'dashboard');
define('_ADMIN_DIR', 'mngmt');
define('_DATA_DIR', 'data');
define('_SESSION_DIR', 'session');

/********************
    경로 상수
********************/
define('_DOMAIN', 'http://222.106.198.35');
define('_COOKIE_DOMAIN',  '');

// URL 은 브라우저상에서의 경로 (도메인으로 부터의)
if(_DOMAIN){
    define('_URL', _DOMAIN);
}else{
    if(isset($_path['url']))
        define('_URL', $_path['url']);
    else
        define('_URL', '');
}

if(isset($_path['path'])){
    define('_PATH', $_path['path']);
}else{
    define('_PATH', '');
}

define('_USER_URL',      _URL.'/'._USER_DIR);
define('_NEW_USER_URL',      _URL.'/'._NEW_USER_DIR);
define('_ADMIN_URL',      _URL.'/'._ADMIN_DIR);
define('_DATA_URL',       _URL.'/'._DATA_DIR);

// PATH 는 서버상에서의 절대경로
define('_USER_PATH',      _PATH.'/'._USER_DIR);
define('_NEW_USER_PATH',      _PATH.'/'._NEW_USER_DIR);
define('_ADMIN_PATH',     _PATH.'/'._ADMIN_DIR);
define('_DATA_PATH',      _PATH.'/'._DATA_DIR);
define('_SESSION_PATH',   _DATA_PATH.'/'._SESSION_DIR);

/********************
    시간 상수
********************/
// 서버의 시간과 실제 사용하는 시간이 틀린 경우 수정하세요.
// 하루는 86400 초입니다. 1시간은 3600초
// 6시간이 빠른 경우 time() + (3600 * 6);
// 6시간이 느린 경우 time() - (3600 * 6);
define('_SERVER_TIME',    time());
define('_TIME_YMDHIS',    date('Y-m-d H:i:s', _SERVER_TIME));
define('_TIME_YMD',       substr(_TIME_YMDHIS, 0, 10));
define('_TIME_HIS',       substr(_TIME_YMDHIS, 11, 8));

define('_MOBILE_AGENT',   'phone|samsung|lgtel|mobile|[^A]skt|nokia|blackberry|BB10|android|sony');

// lib/mailer.lib.php 에서 사용
define('_SMTP',      '127.0.0.1');
define('_SMTP_PORT', '25');
?>
