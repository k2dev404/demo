<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SETTING');
$K2->Menu('TAB');

$sFileUpdate = $_SERVER['DOCUMENT_ROOT'].'/tmp/k2update.zip';
$sDirUpdate = $_SERVER['DOCUMENT_ROOT'].'/tmp/update/';

if($_FILES['FILE']){
	if(!preg_match("#zip#", $_FILES['FILE']['type']) || !file_exists($_FILES['FILE']['tmp_name'])){
		$sError = 'Загрузите файл обновлений';
	}else{
		@mkdir($sDirUpdate);
		if(!@copy($_FILES['FILE']['tmp_name'], $sFileUpdate)){
			$sError = 'Не могу создать временный файл '.$sFileUpdate;
		}elseif(!unZip($sFileUpdate, $sDirUpdate)){
			$sError = 'Файл обновления распакован с ошибками';
		}else{
			@include_once($sDirUpdate.'version.php');
			if($arVersion['FOR'] != $SYSTEM['VERSION']){
				$sError = 'Это обновление не подходит для текущей версии системы';
			}else{
				include_once($sDirUpdate.'after.php');
				dirCopy($sDirUpdate.'file/', $_SERVER['DOCUMENT_ROOT'].'/');
				$DB->Dump($sDirUpdate.'db.sql');
				include_once($sDirUpdate.'before.php');
				$DB->Query("UPDATE `k2_system` SET `VERSION` = '".$arVersion['VERSION']."', `DATE_UPDATE` = NOW(), `LAST_CONNECT` = NOW();");
				dirDelete($sDirUpdate);
				unlink($sFileUpdate);
				Redirect('?complite=1');
			}
		}
		dirDelete($sDirUpdate);
		unlink($sFileUpdate);
	}
}

?><div class="content">
	<h1>Обновление системы</h1>
    <form action="?action=upload" method="post" enctype="multipart/form-data" class="form"><?
	    if($_GET['complite']){
	    	?><div class="complite">Обновление установлено</div><?
	    }elseif($sError){
	    	formError($sError);
	    }else{
	    	?><div class="warning">Перед установкой обновлений рекомендуется сделать резервную копию сайта</div><?
	    }
    	?><div class="item">
			<div class="name">Файл<span class="star">*</span></div>
			<div class="field"><input type="file" name="FILE"></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Отправить"></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>