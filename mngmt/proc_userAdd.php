<?php
include_once('./_common.php');
$result = array();
$email = $_POST['email'];
$pass = $_POST['pass'];

if(!trim($email) || !trim($pass)){
    $result['num'] = 1;
}else{
    $text = exec('sudo useradd -m '.$email.' -s /sbin/nologin -p '.$pass.' 2>&1');

    $result['text'] = $text;
    if(!$text){
        $result['num'] = 2;
    }else{
        $result['num'] = 3;
    }
}
echo json_encode($result);
exit;
?>
