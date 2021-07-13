<?php
include_once("./_common.php");
if($_admin['level'] < 10) alert('권한이 없습니다.', _ADMIN_URL.'/');

$mNum = 9;
include_once(_ADMIN_PATH.'/inc/head.php');

function file_load($file){
	if(file_exists($file)) {
		$fp = fopen ($file,'r');
		return @fread ($fp,filesize($file));
		fclose ($fp);
	}
}

function auto_upload($filedir, $filename, $moment, $previous, $is_ext, $is_auto) {
	if(!$filename) return $previous;

	//점이없는 파일이면 종료
	if(substr_count($filename,'.') == 0) {msg($mstr[file][noext]);return;}

	//점이 1개이상인 파일명처리를 위해 루프처리
	$filename_arr = explode('.',$filename);
	$filename_end = count($filename_arr) - 1;
	for($i=0;$i<=$filename_end;$i++) {
		if($first) $first.= ".";
		$first.= $filename_arr[$i];
		if($i == $filename_end) $last = $filename_arr[$i];
	}

	$last = strtolower($last);

	//허용파일 옶션 미입력시 이미지만 첨부 가능
	if($is_ext && strpos(" .".$is_ext, $last) < 1) {
		print"<script>alert('허용된파일이 아닙니다. ($is_ext) $last');history.go(-1);</script>";
		exit;
	}

	$newfile = $filename;

	//파일명 자동정의
	if($is_auto) {
		//파일명 자리수를 줄이기 위해 10진수 파일명일 36진수로 변경
		$newfile = base_convert(date('YmdHis',time()).rand(1000,9999), 10, 36);
		if($is_auto != 1) $newfile.= "$is_auto";
		$newfile = $newfile . '.' . $last;
	}

	//새로운 파일첨부시만 이전파일이 있으면 삭제
	if($filename && $previous && is_file($filedir.$previous)) unlink($filedir.$previous);

	//업로드전 중복여부 검사
	if(is_file($filedir.$newfile)) {print"<script>alert('$filedir$newfile 중복된 파일이 있습니다.');</script>"; return;}

	//파일업로드
	$result = move_uploaded_file($moment, $filedir.$newfile);

	if(!$result) {print"<script>alert('파일을 업로드할 수 없습니다.(server:$moment, moveto:$filedir$newfile)');</script>"; return;}

	return $newfile;
}

//첨부파일 경로는 trash로 임시 폴더입니다.
$filedir = _ADMIN_PATH."/trash/";
$is_ext = "xlsx";

if($act == 'upload') {
	header('Content-Type: text/html; charset=UTF-8');
	include _PATH."/PHPExcel/Classes/PHPExcel.php";
    $excel = new PHPExcel();
?>

<script type="text/javascript">
function doNotReload() {
	if( (event.ctrlKey == true && (event.keyCode == 78 || event.keyCode == 82)) || (event.keyCode == 116) ) {
		event.keyCode = 0;
		event.cancelBubble = true;
		event.returnValue = false;
		alert("새로고침 방지");
		return false;
	}
}
document.onkeydown = doNotReload;
</script>

<?php
	if(!$upfile){
		if(!$_FILES["file"]['tmp_name']) {
			print"<script>alert('첨부된 파일이 없습니다.');</script>";
			exit;
		}

		//파일업로드
		$upfile = auto_upload("$filedir", $_FILES["file"]['name'], $_FILES["file"]['tmp_name'], "", $is_ext, 1);
	}

	$filename = $filedir.$upfile;
    $allData = array();
	try{
        $excel = PHPExcel_IOFactory::load($filename);
        $sheetsCount = $excel->getSheetCount();
        for($i = 0; $i < $sheetsCount; $i++){
            $excel->setActiveSheetIndex($i);
            $sheet = $excel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            for($row = 1; $row <= $highestRow; $row++){
                $rowData = $sheet->rangeToArray("A" . $row . ":" . $highestColumn . $row, NULL, TRUE, FALSE);
                $allData[$row] = $rowData[0];
            }
        }
    }catch(exception $e){
        echo $e;
    }
	$total = count($allData);
?>

<h2><i class="xi-file-upload-o"></i> 아래의 리스트를 업데이트 하시겠습니까? ( 총 <?=$total?>건)</h2>
<div style="height: 30px;">
	<form name="frm_inc" method="post" action="<?=$PHP_SELF?>?act=upload">
	<input type="hidden" name="upfile" value="<?=$upfile?>">
	<input type="hidden" name="run" value="y">
	<input type="submit" value=" 적용하기 " style="font-weight: bold; padding-top: 2px; cursor:pointer;">&nbsp;
	<input type="button" value=" 창닫기 " style="font-weight: bold; padding-top: 2px; cursor:pointer;" onclick="window.close();">
	</form>
</div>

<table class="table01" id="dataList">

<tr>
	<th>NO</th>
	<th>email</th>
	<th><?php echo ($run == 'y') ? "처리결과" : "상태";?></th>
</tr>

<?php
	foreach($allData as $key => $value){
		//데이터가 있는경우만 진행
		if($value[0] && $value[0] != "no"){
			//실행하기 처리시
			if($run == 'y'){
				$result[$i] = sql_query(" INSERT INTO `rewardDev` SET `email` = '".$value[0]."' ");
				if($result[$i]) $oktrue[$i] = 1;
				$oktrue[$i] = $oktrue[$i] ? '<font color=blue>처리완료</font>' : '<font color=red>처리실패</font>';
			}else{
				$oktrue[$i] = 1;
				$oktrue[$i] = $oktrue[$i] ? '<font color=green>적용가능</font>' : '<font color=red>중복자료</font>';
			}
?>
<tr>
	<td align="center"><?php echo $key?></td>
	<td align="center"><?php echo $value[0]?></td>
	<td align="center"><?php echo $oktrue[$i]?></td>
</tr>
<?php
		}
	}
?>
</table>
<?php
	include_once(_ADMIN_PATH.'/inc/foot.php');
	exit;
}
?>

	<h2><i class="xi-file-upload-o"></i> 엑셀 데이터 일괄등록</h2>

<script>
function ischeck_submit(f){
	var filename = f.file.value;
	var ext = filename.slice(filename.lastIndexOf(".")+1).toLowerCase();

  if(!f.file.value) {
		alert("파일을 첨부하세요.");
		f.file.focus();
		return false;
  }

	if(ext != 'xlsx') {
		alert("파일 첨부는 엑셀(*.xlsx)파일만 가능합니다.");
		return false;
	}

	return true;
}
</script>

<table width="100%" cellpadding="0" cellspacing="0" class="jList">
<form name="frm_ed" method="post" action="<?=$PHP_SELF?>?act=upload" enctype="multipart/form-data" onsubmit="return ischeck_submit(this)" target="_blank">

<tr>
	<th width="7%">파일첨부</th>
	<td width="93%" class="field" style="padding: 10px;"><input class="ed" type=file name=file value="" style="width:300px;"> <button type="submit" class="excelUpload"><i class="xi-upload"></i></button></td>
</tr>
<!--tr>
	<td>샘플파일</td>
	<td>
		<img border=0 src="/adm/img/icon_file.gif" align="absmiddle"> <a href="/data/sample1.xlsx" target=_blank><b>샘플다운받기</b></a>
		<span class=ico_it></span> 샘플파일 형태로 작성하세요.
	</td>
</tr-->
<tr>
	<th>주의사항</th>
	<td class="field" style="padding:10px;font-weight:500;color:#b40000;">
		<div>※ 엑셀(*.xlsx)파일만 업로드 가능합니다.</div>
		<div>※ 엑셀파일 작성시 정해진 필드 형식에 맞춰 작성해야 합니다. 하단 예시를 참고하세요.</div>
		<div>※ 모든 필드는 필수항목입니다.</div>
	</td>
</tr>
<tr>
	<th>예시</th>
	<td>
		<table class="table02">
			<tr>
				<th></th>
				<th>A</th>
		    	<th>B</th>
		    	<th>C</th>
		    	<th>D</th>
		    	<th>E</th>
		    	<th>F</th>
		    	<th>G</th>
		    	<th>H</th>
		    </tr>
			<tr>
				<td class="first">1</td>
				<td class="tit">No</td>
		    	<td class="tit">Name</td>
		    	<td class="tit">IP</td>
		    	<td class="tit">Address</td>
		    	<td class="tit">Private Key</td>
		    	<td class="tit">TXID</td>
		    	<td class="tit">TXINDEX</td>
		    	<td class="tit">Wallet IP</td>
		    </tr>
			<tr>
				<td class="first">2</td>
				<td>3062</td>
		    	<td>comma_mn_extra_2992</td>
		    	<td>3.8.91.30</td>
		    	<td class="equal">CcQN7oozE1euq4i6cRf8mGiYmB6ntr35dF</td>
		    	<td class="equal">2a6sCnhcwS3FTaXGh4PVVaTekUfzw26kpEcE9cvfVUFRh3ggsMv</td>
		    	<td class="equal">05ec28246f31c0510149443bbc2e453479e3b2b57439853501bc38de7a6af9d7</td>
		    	<td>1</td>
		    	<td>34.200.246.108</td>
		    </tr>
			<tr>
				<td class="first">3</td>
				<td>3061</td>
		    	<td>comma_mn_extra_2991</td>
		    	<td>34.244.109.172</td>
		    	<td class="equal">CZQCNWc5ZtDct9AtNHSaQKv8cS1prUtAE5</td>
		    	<td class="equal">2ZyQQBZyZLnALJWK55MJsUB5mz4WGB6hC6B1dFnuc6amBBuNELF</td>
		    	<td class="equal">8b5ed615900ec85154184d5d1b868c0d803ee5aaf760b6b6ed4e00d2c9de1edc</td>
		    	<td>1</td>
		    	<td>18.215.245.218</td>
		    </tr>
		</table>
		<div class="guide">※ 예시 <span>1번 열은 항목에 대한 설명</span>으로 생략 가능합니다.</div>
	</td>
</tr>
</form>
</table>

<?php
include_once(_ADMIN_PATH.'/inc/foot.php');
?>
