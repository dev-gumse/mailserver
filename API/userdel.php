<?php

$result = array();
$email = $_POST['email'];

if(!trim($email)){
    $result['result'] = 1;
    $result['message'] = "파라미터 오류";
}else{
    $text = exec('sudo userdel -r '.$email.' 2>&1');

    // $result['text'] = $text;
    if(strpos($text,'not found') !== false){
        $result['result'] = 2;
        $result['message'] = "성공";
    }else if(strpos($text,'not exist') !== false){
        $result['result'] = 3;
        $result['message'] = "존재하지 않는 아이디 입니다.";
    }
}
echo json_encode($result,JSON_UNESCAPED_UNICODE);
exit;
?>
