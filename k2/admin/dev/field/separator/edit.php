<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('DEV');

if(!$arGroup = $LIB['FIELD_SEPARATOR']->ID($_ID)){
	exit;
}

if($_POST['NAME']){
	$LIB['FIELD_SEPARATOR']->Edit($_ID, $_POST);
	Redirect($_BACK);
}
?>
<form action="/k2/admin/dev/field/separator/edit.php?id=<?=$_ID?>&back=<?=base64_encode($_SERVER['HTTP_REFERER'])?>" method="post" id="group-form" class="form">
	<div class="item">
		<div class="name">Название</div>
		<div class="field"><input type="text" name="NAME" value="<?=html($arGroup['NAME'])?>" required autofocus></div>
	</div>
	<div style="height:30px;">
		<input type="submit" class="sub rightSub" value="Сохранить">
	</div>
</form>