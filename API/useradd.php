<?php

$result = array();
$email = $_POST['email'];
$pass = $_POST['pass'];

if(!trim($email) || !trim($pass)){
    $result['result'] = 1;
    $result['message'] = "파라미터 오류";
}else{
    $text = exec('sudo useradd -m '.$email.' -s /sbin/nologin -p '.$pass.' 2>&1');

    // $result['text'] = $text;
    if(!$text){
        $result['result'] = 2;
        $result['message'] = "성공";
    }else{
        $result['result'] = 3;
        $result['message'] = "이미 존재하는 아이디 입니다.";
    }
}
echo json_encode($result,JSON_UNESCAPED_UNICODE);
exit;
?>
