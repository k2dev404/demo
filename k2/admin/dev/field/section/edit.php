<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');
$K2->Menu('TAB_SUB');

if(!$arField = $LIB['FIELD']->ID($_ID)){
	Redirect('/k2/admin/dev/field/section/');
}
if($_POST){
	if($nID = $LIB['FIELD']->Edit($_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$_ID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/field/section/');
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
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/field/section/">отменить</a></p>
		</div>
	</form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>