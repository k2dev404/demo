<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');

if($_POST){
	if($nID = $LIB['FORM']->Add($_POST)){
    	if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/form/');
		}
	}
}
?><div class="content">
	<h1>Добавление</h1>
    <form action="add.php" method="post" class="form">
    	<?formError($LIB['FORM']->Error)?>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>" autofocus></div>
		</div>
		<div class="item">
			<input type="hidden" name="CAPTCHA" value="0"><label><input type="checkbox" name="CAPTCHA" value="1"<?
			if($_POST['CAPTCHA']){
				?> checked<?
			}
			?>>Проверять защитную картинку</label>
		</div>
		<div class="item">
			<div class="name">Контроллер</div>
			<div class="field"><textarea name="CONTROLLER" cols="40" rows="6" data-code="true"><?=html($_POST['CONTROLLER'])?></textarea></div>
		</div>
		<div class="item">
			<div class="name">Шаблон</div>
			<div class="field"><textarea name="TEMPLATE" cols="40" rows="6" data-code="true"><?=html($_POST['TEMPLATE'])?></textarea></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/form/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>