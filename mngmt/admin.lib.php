<?php
if (!defined('_MAIL_')) exit;

// 불법접근을 막도록 토큰을 생성하면서 토큰값을 리턴
function get_admin_token(){
    $token = md5(uniqid(rand(), true));
    set_session('ss_admin_token', $token);
    return $token;
}

// POST로 넘어온 토큰과 세션에 저장된 토큰 비교
function check_admin_token(){
    $token = get_session('ss_admin_token');
    set_session('ss_admin_token', '');
    if(!$token || !$_REQUEST['token'] || $token != $_REQUEST['token'])
        alert('올바른 방법으로 이용해 주십시오.', G5_URL);

    return true;
}

// 관리자 페이지 referer 체크
function admin_referer_check($return=false){
    $referer = trim($_SERVER['HTTP_REFERER']);
    if(!$referer){
        $msg = '정보가 올바르지 않습니다.';

        if($return)
            return $msg;
        else
            alert($msg, _URL);
    }

    $p = @parse_url($referer);

    $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
    $msg = '';

    if($host != $p['host']){
        $msg = '올바른 방법으로 이용해 주십시오.';
    }

    if($p['path'] && ! preg_match( '/\/'.preg_quote(_ADMIN_DIR).'\//i', $p['path'])){
        $msg = '올바른 방법으로 이용해 주십시오';
    }

    if( $msg ){
        if($return){
            return $msg;
        } else {
            alert($msg, _URL);
        }
    }
}

function admin_check_xss_params($params){
    if(!$params) return;
    foreach( $params as $key=>$value ){
        if(empty($value)) continue;
        if(is_array($value)){
            admin_check_xss_params($value);
        }else if(preg_match('/<\s?[^\>]*\/?\s?>/i', $value) && (preg_match('/script.*?\/script/ius', $value) || preg_match('/onload=.*/ius', $value))){
            alert('요청 쿼리에 잘못된 스크립트문장이 있습니다.\\nXSS 공격일수도 있습니다.');
            die();
        }
    }
    return;
}

function get_admin($email){
    $sql = " SELECT * FROM `Admin` WHERE `adminId` = '".TRIM($email)."' ";
    return sql_fetch($sql);
}

if(isset($_REQUEST) && $_REQUEST){
    if(admin_referer_check(true)){
        admin_check_xss_params($_REQUEST);
    }
}
?>
