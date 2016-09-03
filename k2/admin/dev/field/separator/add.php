<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('DEV');

if($_POST['NAME']){
	$LIB['FIELD_SEPARATOR']->Add(array('TABLE' => $_REQUEST['table'], 'NAME' => $_POST['NAME']));
    Redirect($_BACK);
}
?>
<form action="/k2/admin/dev/field/separator/add.php?table=<?=html($_REQUEST['table'])?>&back=<?=base64_encode($_SERVER['HTTP_REFERER'])?>" method="post" id="group-form" class="form">
	<div class="item">
		<div class="name">Название</div>
		<div class="field"><input type="text" name="NAME" value="" required autofocus></div>
	</div>
	<div style="height:30px;">
		<input type="submit" class="sub rightSub" value="Сохранить">
	</div>
</form>