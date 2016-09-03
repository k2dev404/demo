<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('DEV');

$arTable = array('block', 'component');

if($_POST['NAME']){
	$LIB[strtoupper($arTable[$_OBJECT]).'_GROUP']->Add($_REQUEST);
	Redirect('/k2/admin/dev/'.$arTable[$_OBJECT].'/');
}
?>
<form action="/k2/admin/dev/group/add.php?object=<?=$_OBJECT?>" method="post" id="group-form" class="form">
	<div class="item">
		<div class="name">Название</div>
		<div class="field"><input type="text" name="NAME" value="" required autofocus></div>
	</div>
	<div style="height:30px;">
		<input type="submit" class="sub rightSub" value="Сохранить">
	</div>
</form>