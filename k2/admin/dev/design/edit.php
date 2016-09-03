<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');

$arDesign = $LIB['DESIGN']->ID($_ID, 1);
if(!$arDesign){
	Redirect('/k2/admin/design/');
}
if($_POST){
	if($nID = $LIB['DESIGN']->Edit($_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/design/');
		}
	}
}else{
	$_POST = $arDesign;
}

$sDirTemplate = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/design/'.$_ID.'/';

?><div class="content">
	<h1>Редактирование</h1>
    <form action="edit.php?id=<?=$_ID?>" method="post" class="form">
    	<?formError($LIB['DESIGN']->Error)?>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Верх</div>
			<div class="field">
				<textarea name="HEADER" cols="40" rows="6" data-code="true"><?=html($_POST['HEADER'])?></textarea>
				<div class="note">Файл <?=$sDirTemplate?>header.php</div>
			</div>
		</div>
		<div class="item">
			<div class="name">Низ</div>
			<div class="field">
				<textarea name="FOOTER" cols="40" rows="6" data-code="true"><?=html($_POST['FOOTER'])?></textarea>
				<div class="note">Файл <?=$sDirTemplate?>footer.php</div>
			</div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/design/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>