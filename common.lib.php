<?php
// DB 연결
function sql_connect($host, $user, $pass){
    if(function_exists('mysqli_connect')) {
        $link = mysqli_connect($host, $user, $pass);
        // 연결 오류 발생 시 스크립트 종료
        if (mysqli_connect_errno()) {
            die('Connect Error: '.mysqli_connect_error());
        }
    } else {
        $link = mysql_connect($host, $user, $pass);
    }
    return $link;
}

// DB 선택
function sql_select_db($db, $connect){
    if(function_exists('mysqli_select_db'))
        return @mysqli_select_db($connect, $db);
    else
        return @mysql_select_db($db, $connect);
}

function sql_query($sql){
    global $connect_db;

    // Blind SQL Injection 취약점 해결
    $sql = trim($sql);
    // union의 사용을 허락하지 않습니다.
    //$sql = preg_replace("#^select.*from.*union.*#i", "select 1", $sql);
    $sql = preg_replace("#^select.*from.*[\s\(]+union[\s\)]+.*#i ", "select 1", $sql);
    // `information_schema` DB로의 접근을 허락하지 않습니다.
    $sql = preg_replace("#^select.*from.*where.*`?information_schema`?.*#i", "select 1", $sql);

    if(function_exists('mysqli_query')){
        if($error){
            $result = @mysqli_query($connect_db, $sql) or die("<p>$sql<p>" . mysqli_errno($connect_db) . " : " .  mysqli_error($connect_db) . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
        } else {
            $result = @mysqli_query($connect_db, $sql);
        }
    } else {
        if ($error) {
            $result = @mysql_query($sql, $connect_db) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
        } else {
            $result = @mysql_query($sql, $connect_db);
        }
    }

    return $result;
}

// 쿼리를 실행한 후 결과값에서 한행을 얻는다.
function sql_fetch($sql, $link=null){
    global $connect_db;

    $result = sql_query($sql, $connect_db);
    //$row = @sql_fetch_array($result) or die("<p>$sql<p>" . mysqli_errno() . " : " .  mysqli_error() . "<p>error file : $_SERVER['SCRIPT_NAME']");
    $row = sql_fetch_array($result);
    return $row;
}

// 결과값에서 한행 연관배열(이름으로)로 얻는다.
function sql_fetch_array($result){
    if(function_exists('mysqli_fetch_assoc'))
        $row = @mysqli_fetch_assoc($result);
    else
        $row = @mysql_fetch_assoc($result);
    return $row;
}

function sql_insert_id($link=null)
{
    global $connect_db;

    if(!$link)
        $link = $connect_db;

    if(function_exists('mysqli_insert_id'))
        return mysqli_insert_id($link);
    else
        return mysql_insert_id($link);
}

// 세션변수 생성
function set_session($session_name, $value){
    if (PHP_VERSION < '5.3.0')
        session_register($session_name);
    // PHP 버전별 차이를 없애기 위한 방법
    $$session_name = $_SESSION["$session_name"] = $value;
}

// 세션변수값 얻음
function get_session($session_name){
    return $_SESSION[$session_name];
}

// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire){
    setcookie(md5($cookie_name), base64_encode($value), _SERVER_TIME + $expire, '/', _COOKIE_DOMAIN);
}

// 쿠키변수값 얻음
function get_cookie($cookie_name){
    $cookie = md5($cookie_name);
    if (array_key_exists($cookie, $_COOKIE))
        return base64_decode($_COOKIE[$cookie]);
    else
        return "";
}

function goto_url($url){
    $url = str_replace("&amp;", "&", $url);
    //echo "<script> location.replace('$url'); </script>";

    if (!headers_sent())
        header('Location: '.$url);
    else{
        echo '<script>';
        echo 'location.replace("'.$url.'");';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
    }
    exit;
}

// 경고메세지를 경고창으로
function alert($msg='', $url='', $error=true, $post=false){
    $msg = $msg ? strip_tags($msg, '<br>') : 'Please use it in the correct way.';
    echo "<script>alert('".$msg."');location.href='".$url."';</script>";
    exit;
}

// 회원 정보를 얻는다.
function get_user($userId, $fields='*'){
    //$sql = " SELECT * FROM `User` WHERE `userid` = '".$email."' ";
    $sql = " SELECT ".$fields." FROM `User` WHERE `userId` = '".TRIM($userId)."' ";
    return sql_fetch($sql);
}

function get_deposit($uid, $fields='*'){
    $sql = " SELECT * FROM `Deposit` WHERE `uid` = '".$uid."' ";
    return sql_fetch($sql);
}

function is_mobile(){
    return preg_match('/'._MOBILE_AGENT.'/i', $_SERVER['HTTP_USER_AGENT']);
}

// 이메일 주소 추출
function get_email_address($email){
    preg_match("/[0-9a-z._-]+@[a-z0-9._-]{4,}/i", $email, $matches);
    return $matches[0];
}

// XSS 관련 태그 제거
function clean_xss_tags($str){
    $str_len = strlen($str);
    $i = 0;
    while($i <= $str_len){
        $result = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);
        if((string)$result === (string)$str) break;
        $str = $result;
        $i++;
    }
    return $str;
}

// XSS 어트리뷰트 태그 제거
function clean_xss_attributes($str){
    $str = preg_replace('#(onabort|onactivate|onafterprint|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onblur|onbounce|oncellchange|onchange|onclick|oncontextmenu|oncontrolselect|oncopy|oncut|ondataavaible|ondatasetchanged|ondatasetcomplete|ondblclick|ondeactivate|ondrag|ondragdrop|ondragend|ondragenter|ondragleave|ondragover|ondragstart|ondrop|onerror|onerrorupdate|onfilterupdate|onfinish|onfocus|onfocusin|onfocusout|onhelp|onkeydown|onkeypress|onkeyup|onlayoutcomplete|onload|onlosecapture|onmousedown|onmouseenter|onmouseleave|onmousemove|onmoveout|onmouseover|onmouseup|onmousewheel|onmove|onmoveend|onmovestart|onpaste|onpropertychange|onreadystatechange|onreset|onresize|onresizeend|onresizestart|onrowexit|onrowsdelete|onrowsinserted|onscroll|onselect|onselectionchange|onselectstart|onstart|onstop|onsubmit|onunload)\\s*=\\s*\\\?".*?"#is', '', $str);
    return $str;
}

// unescape nl 얻기
function conv_unescape_nl($str){
    $search = array('\\r', '\r', '\\n', '\n');
    $replace = array('', '', "\n", "\n");
    return str_replace($search, $replace, $str);
}

function makeUniqCode(){
    $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
    shuffle($chars_array);
    $shuffle = implode('', $chars_array);
    $code = substr($shuffle, 0, 10);
    $sql = " SELECT COUNT(no) AS cnt FROM `User` WHERE `recom_code` = '".$code."' ";
    $row = sql_fetch($sql);
    if($row['cnt']){
        makeUniqCode();
    }else{
        return $code;
    }
}

// 동일한 host url 인지
function check_url_host($url, $msg='', $return_url=_URL, $is_redirect=false)
{
    if(!$msg)
        $msg = 'You can not specify another domain for url.';

    $p = @parse_url($url);
    $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
    $is_host_check = false;

    // url을 urlencode 를 2번이상하면 parse_url 에서 scheme와 host 값을 가져올수 없는 취약점이 존재함
    if ( $is_redirect && !isset($p['host']) && urldecode($url) != $url ){
        $i = 0;
        while($i <= 3){
            $url = urldecode($url);
            if( urldecode($url) == $url ) break;
            $i++;
        }

        if( urldecode($url) == $url ){
            $p = @parse_url($url);
        } else {
            $is_host_check = true;
        }
    }

    if(stripos($url, 'http:') !== false) {
        if(!isset($p['scheme']) || !$p['scheme'] || !isset($p['host']) || !$p['host'])
            alert('The url information is invalid.', $return_url);
    }

    //php 5.6.29 이하 버전에서는 parse_url 버그가 존재함
    //php 7.0.1 ~ 7.0.5 버전에서는 parse_url 버그가 존재함
    if ( $is_redirect && (isset($p['host']) && $p['host']) ) {
        $bool_ch = false;
        foreach( array('user','host') as $key) {
            if ( isset( $p[ $key ] ) && strpbrk( $p[ $key ], ':/?#@' ) ) {
                $bool_ch = true;
            }
        }
        if( $bool_ch ){
            $regex = '/https?\:\/\/'.$host.'/i';
            if( ! preg_match($regex, $url) ){
                $is_host_check = true;
            }
        }
    }

    if ((isset($p['scheme']) && $p['scheme']) || (isset($p['host']) && $p['host']) || $is_host_check) {
        //if ($p['host'].(isset($p['port']) ? ':'.$p['port'] : '') != $_SERVER['HTTP_HOST']) {
        if ( ($p['host'] != $host) || $is_host_check ) {
            echo '<script>'.PHP_EOL;
            echo 'alert("'.$msg.'");'.PHP_EOL;
            echo 'document.location.href = "'.$return_url.'";'.PHP_EOL;
            echo '</script>'.PHP_EOL;
            echo '<noscript>'.PHP_EOL;
            echo '<p>'.$msg.'</p>'.PHP_EOL;
            echo '<p><a href="'.$return_url.'">Go Back</a></p>'.PHP_EOL;
            echo '</noscript>'.PHP_EOL;
            exit;
        }
    }
}

function check_mail_bot($ip=''){
    //아이피를 체크하여 메일 크롤링을 방지합니다.
    $check_ips = array('211.249.40.');
    $bot_message = 'It stops judging by bot.';
    if($ip){
        foreach( $check_ips as $c_ip ){
            if( preg_match('/^'.preg_quote($c_ip).'/', $ip) ) {
                die($bot_message);
            }
        }
    }
    // user agent를 체크하여 메일 크롤링을 방지합니다.
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if ($user_agent === 'Carbon' || strpos($user_agent, 'BingPreview') !== false || strpos($user_agent, 'Slackbot') !== false) {
        die($bot_message);
    }
}

function validate($address){
    $decoded = decodeBase58($address);
    $d1 = hash("sha256", substr($decoded,0,21), true);
    $d2 = hash("sha256", $d1, true);

    if(substr_compare($decoded, $d2, 21, 4)){
        throw new \Exception(2);
    }
    return true;
}
function decodeBase58($input){
    $alphabet = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";

    $out = array_fill(0, 25, 0);
    for($i=0;$i<strlen($input);$i++){
        if(($p=strpos($alphabet, $input[$i]))===false){
            throw new \Exception(3);
        }
        $c = $p;
        for($j = 25; $j--;){
            $c += (int)(58 * $out[$j]);
            $out[$j] = (int)($c % 256);
            $c /= 256;
            $c = (int)$c;
        }
        if($c != 0){
            throw new \Exception(4);
        }
    }

    $result = "";
    foreach($out as $val){
        $result .= chr($val);
    }

    return $result;
}

function checkBitAddr($addr){
    $message = 1;
    try{
        validate($addr);
    }catch(\Exception $e){ $message = $e->getMessage(); }
    return $message;
}

function search_font($stx, $str){
    // 문자앞에 \ 를 붙입니다.
    $src = array('/', '|');
    $dst = array('\/', '\|');

    if (!trim($stx) && $stx !== '0') return $str;

    // 검색어 전체를 공란으로 나눈다
    $s = explode(' ', $stx);

    // "/(검색1|검색2)/i" 와 같은 패턴을 만듬
    $pattern = '';
    $bar = '';
    for ($m=0; $m<count($s); $m++) {
        if (trim($s[$m]) == '') continue;
        // 태그는 포함하지 않아야 하는데 잘 안되는군. ㅡㅡa
        //$pattern .= $bar . '([^<])(' . quotemeta($s[$m]) . ')';
        //$pattern .= $bar . quotemeta($s[$m]);
        //$pattern .= $bar . str_replace("/", "\/", quotemeta($s[$m]));
        $tmp_str = quotemeta($s[$m]);
        $tmp_str = str_replace($src, $dst, $tmp_str);
        $pattern .= $bar . $tmp_str . "(?![^<]*>)";
        $bar = "|";
    }

    // 지정된 검색 폰트의 색상, 배경색상으로 대체
    $replace = "<strong class=\"sch_word\">\\1</strong>";

    return preg_replace("/($pattern)/i", $replace, $str);
}

// 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL
function get_paging($write_pages, $cur_page, $total_page, $url, $add=""){
    $url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';
    $str = '';
    if($cur_page > 1){
        $str .= '<a href="'.$url.'1'.$add.'" class="pg_page pg_start">처음</a>'.PHP_EOL;
    }
    $start_page = (((int)(($cur_page - 1 ) / $write_pages)) * $write_pages) + 1;
    $end_page = $start_page + $write_pages - 1;
    if($end_page >= $total_page) $end_page = $total_page;
    if($start_page > 1) $str .= '<a href="'.$url.($start_page-1).$add.'" class="pg_page pg_prev">이전</a>'.PHP_EOL;
    if($total_page > 1){
        for($k=$start_page;$k<=$end_page;$k++){
            if($cur_page != $k)
                $str .= '<a href="'.$url.$k.$add.'" class="pg_page">'.$k.'<span class="sound_only">페이지</span></a>'.PHP_EOL;
            else
                $str .= '<span class="sound_only">열린</span><strong class="pg_current">'.$k.'</strong><span class="sound_only">페이지</span>'.PHP_EOL;
        }
    }
    if ($total_page > $end_page) $str .= '<a href="'.$url.($end_page+1).$add.'" class="pg_page pg_next">다음</a>'.PHP_EOL;
    if($cur_page < $total_page){
        $str .= '<a href="'.$url.$total_page.$add.'" class="pg_page pg_end">맨끝</a>'.PHP_EOL;
    }
    if($str)
        return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
    else
        return "";
}

function admPaging($write_pages, $cur_page, $total_page, $add=""){
    $str = '';
    if($cur_page > 1){
        $str .= '<a href="1'.$add.'" class="pg_page pg_start">처음</a>'.PHP_EOL;
    }
    $start_page = (((int)(($cur_page - 1 ) / $write_pages)) * $write_pages) + 1;
    $end_page = $start_page + $write_pages - 1;
    if($end_page >= $total_page) $end_page = $total_page;
    if($start_page > 1) $str .= '<a href="'.($start_page-1).$add.'" class="pg_page pg_prev">이전</a>'.PHP_EOL;
    if($total_page > 1){
        for($k=$start_page;$k<=$end_page;$k++){
            if($cur_page != $k)
                $str .= '<a href="'.$k.$add.'" class="pg_page">'.$k.'<span class="sound_only">페이지</span></a>'.PHP_EOL;
            else
                $str .= '<span class="sound_only">열린</span><strong class="pg_current">'.$k.'</strong><span class="sound_only">페이지</span>'.PHP_EOL;
        }
    }
    if ($total_page > $end_page) $str .= '<a href="'.($end_page+1).$add.'" class="pg_page pg_next">다음</a>'.PHP_EOL;
    if($cur_page < $total_page){
        $str .= '<a href="'.$total_page.$add.'" class="pg_page pg_end">맨끝</a>'.PHP_EOL;
    }
    if($str)
        return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
    else
        return "";
}

// 휴대폰번호의 숫자만 취한 후 중간에 하이픈(-)을 넣는다.
function hyphen_hp_number($hp){
    $hp = preg_replace("/[^0-9]/", "", $hp);
    return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $hp);
}

function valid_mb_email($reg_mb_email){
    if (!preg_match("/([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/", $reg_mb_email))
        return 1;
    else
        return "";
}

//문자열 사이 배열로 추출
function splitBetweenStr($str, $startWord, $endWord)
{
	for ($i=0, $len=strlen($str); $i<$len; $i++)
	{
		$target = substr($str,$i);
		$prevStartIdx = strpos($target, $startWord);
		$startIdx = $prevStartIdx + strlen($startWord);
		$endIdx = strpos(substr($target, $startIdx), $endWord);
		if($prevStartIdx===false || $endIdx===false)
		{
			break;
		}
		else
		{
			$betweenStrings[] = substr($target, $startIdx, $endIdx);
			$i += $startIdx + $endIdx + strlen($endWord) - 1;
		}
	}
	return $betweenStrings;
}

function recursive_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst);
    @chmod($dst,0777);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
               recursive_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                //php확장자 체크하여 파일에 .php가 포함되어있으면 php파일은 제외하고 복사
                if(strpos($file,'.php') === false){
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
    }
    closedir($dir);
}

function rmdir_ok($dir) {
    $dirs = dir($dir);
    while(false !== ($entry = $dirs->read())) {
        if(($entry != '.') && ($entry != '..')) {
            if(is_dir($dir.'/'.$entry)) {
                  rmdir_ok($dir.'/'.$entry);
            } else {
                  @unlink($dir.'/'.$entry);
            }
        }
    }
    $dirs->close();
    @rmdir($dir);
}


function Zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            $file = realpath($file);

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}
?>
