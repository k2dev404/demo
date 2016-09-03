<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');
if(!$LIB['BLOCK_GROUP']->Rows()){
	Redirect('/k2/admin/dev/block/');
}
if($_POST){
    $_POST['FILE'] = $_FILES['FILE'];
    if($nID = $LIB['BLOCK']->Import($_POST)){
	    Redirect('/k2/admin/dev/block/edit.php?id='.$nID);
	}
}
?><div class="content">
	<h1>Импорт</h1>
	<form method="post" enctype="multipart/form-data" class="form">
		<?formError($LIB['BLOCK']->Error)?>
		<div class="item">
			<div class="name">Группа<span class="star">*</span></div>
			<div class="field"><select name="BLOCK_GROUP"><?
			$arGroup = $LIB['BLOCK_GROUP']->Rows();
			for($i=0; $i<count($arGroup); $i++)
			{
				?><option value="<?=$arGroup[$i]['ID']?>"<?
				if($_POST['BLOCK_GROUP'] == $arGroup[$i]['ID']){
					?> selected<?
				}
				?>><?=$arGroup[$i]['NAME']?></option><?
			}
			?></select></div>
		</div>
		<div class="item">
			<div class="name">Файл<span class="star">*</span></div>
			<div class="field"><input type="file" name="FILE"></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/block/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>