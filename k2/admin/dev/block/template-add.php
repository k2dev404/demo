<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('DEV');

if(!$arBlock = $LIB['BLOCK']->ID($_ID)){
	Redirect('/k2/admin/dev/block/');
}

if($_POST['NAME'] && $_POST['FILE']){
	$_POST['OBJECT'] = 1;
	$_POST['OBJECT_ID'] = $arBlock['ID'];
	$nID = $LIB['TEMPLATE']->Add($_POST);
	Redirect('/k2/admin/dev/block/edit.php?id='.$arBlock['ID'].'#t'.$nID);
}
?>
<form action="/k2/admin/dev/block/template-add.php?id=<?=$_ID?>" method="post" id="group-form" class="form">
	<div class="item">
		<div class="name">Название<span class="star">*</span></div>
		<div class="field"><input type="text" name="NAME" value="" required autofocus id="transcription-from"></div>
	</div>
	<div class="item">
		<div class="name">Файл<span class="star">*</span></div>
		<div class="field"><input type="text" name="FILE" value="" required id="transcription-to"></div>
	</div>
	<div style="height:30px;">
		<input type="submit" class="sub rightSub" value="Сохранить">
	</div>
</form>