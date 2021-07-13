<?php
include_once('./_common.php');

?>

<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link rel="stylesheet" href="<?php echo _ADMIN_URL;?>/admin.css?t=<?php echo time()?>">
        <title>ADMIN</title>
    </head>
    <body>
        <div class="formWrap">
            <h1>메일서버 유저생성</h1>
            <form name="swapfrom" id="swapfrom" method="post" autocomplete="off">
                <div class="input">
                    <input type="text" name="email" placeholder="아이디" required />
                </div>
                <div class="input">
                    <input type="password" name="pass" placeholder="비밀번호" required />
                </div>
                <div><button type="submit">유저생성</button></div>
            </form>
        </div>

        <script src="<?php echo _URL;?>/js/jquery-3.3.1.min.js"></script>
        <script>
        $(document).ready(function(){
            $('#swapfrom').submit(function(e){
                e.preventDefault();
                var f = document.swapfrom;
                if(!f.email.value){
                    alert('아이디를 입력하세요.');
                    f.email.focus();
                    return false;
                }
                if(!f.pass.value){
                    alert('비밀번호를 입력하세요.');
                    f.pass.focus();
                    return false;
                }
                $.ajax({
                    type: "post",
                    url: "proc_userAdd.php",
                    data: {
                        email: f.email.value,
                        pass: f.pass.value
                    },
                    dataType: "json",
                    async: false,
                    success: function(data){
                        console.log(data);
                        switch(data.num){
                            case 1: alert('아이디 또는 비밀번호가 공백이면 안됩니다.'); break;
                            case 2: alert('유저가 생성되었습니다.'); location.reload(); break;
                            case 3: alert('이미 존재하는 아이디 입니다.'); break;
                        }
                        f.reset();
                        return false;
                    }
                });
            });
        });
        </script>
    </body>
</html>
