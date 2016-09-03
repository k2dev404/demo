<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

if(!$arField = $LIB['FIELD']->ID($_ID)){
	Redirect('/k2/admin/dev/block/');
}
$nBlock = preg_replace("#k2_block(\d+)category#", "\\1", $arField['TABLE']);

$K2->Menu('TAB');
$K2->Menu('TAB_SUB', array(array('Настройки', '/dev/block/edit.php?id='.$nBlock), array('Поля категорий', '/dev/block/field/category/?id='.$nBlock, 1), array('Поля элементов', '/dev/block/field/element/?id='.$nBlock)));

if($_POST){
	if($nID = $LIB['FIELD']->Edit($_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$_ID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/block/field/category/?id='.$nBlock);
		}
	}
}else{
	$_POST = $arField;
}

?><div class="content">
	<h1>Редактирование</h1>
	<form action="edit.php?id=<?=$_ID?>" method="post" class="form">
    	<?formError($LIB['FIELD']->Error)?><?
    	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/dev/field/type/edit.php');
	    include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/dev/field/type/'.html(strtolower($arField['TYPE'])).'/edit.php');
		?><div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/block/field/category/?id=<?=$nBlock?>">отменить</a></p>
		</div>
	</form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>