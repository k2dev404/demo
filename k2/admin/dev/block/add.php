<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');

if($_POST){
	if($nID = $LIB['BLOCK']->Add($_POST)){
    	if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/block/');
		}
	}
}
?><div class="content">
	<h1>Добавление</h1>
    <form action="add.php" method="post" class="form">
    	<?formError($LIB['BLOCK']->Error)?>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>" autofocus></div>
		</div>
		<div class="item">
			<div class="name">Группа<span class="star">*</span></div>
			<div class="field"><select name="BLOCK_GROUP"><?
			$arGroup = $LIB['BLOCK_GROUP']->Rows();
			for($i=0; $i<count($arGroup); $i++)
			{
				?><option value="<?=$arGroup[$i]['ID']?>"<?
				if($_POST['BLOCK_GROUP'] == $arGroup[$i]['ID']){
					?> selected="selected"<?
				}
				?>><?=$arGroup[$i]['NAME']?></option><?
			}
			?></select></div>
		</div>
        <div class="item"><label><input type="checkbox" name="CATEGORY" value="1"<?
	    if($_POST['CATEGORY']){
	    	?> checked="checked"<?
	    }
	    ?>>Отображать категории</label></div>
		<div class="item">
			<div class="name">Контроллер</div>
			<div class="field"><textarea name="CONTROLLER" cols="40" rows="6" data-code="true"><?=html($_POST['CONTROLLER'])?></textarea></div>
		</div>
		<div class="item">
			<div class="name">Шаблон</div>
			<div class="field"><textarea name="TEMPLATE" cols="40" rows="6" data-code="true"><?=html($_POST['TEMPLATE'])?></textarea></div>
		</div>
		<div class="item">
			<div class="name">Шаблон полного вывода</div>
			<div class="field"><textarea name="FOOTER" cols="40" rows="6" data-code="true"><?=html($_POST['TEMPLATE_FULL'])?></textarea></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/block/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>