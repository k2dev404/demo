<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

tab(array(array('Модули', '/module/', 1)));

if($_POST){
    $_POST['FILE'] = $_FILES['FILE'];
    if($sModule = $LIB['MODULE']->Import($_POST)){
	    Redirect('/k2/admin/module/'.strtolower($sModule).'/');
	}
}

?><div class="content">
	<h1>Импорт</h1>
	<form method="post" enctype="multipart/form-data" class="form">
		<?formError($LIB['MODULE']->Error)?>
		<div class="item">
			<div class="name">Файл<span class="star">*</span></div>
			<div class="field"><input type="file" name="FILE"></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" name="sub" value="Сохранить"> или <a href="/k2/admin/module/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>