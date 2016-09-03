<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');
$K2->Menu('TAB_SUB', array(array('Настройки', '/dev/block/edit.php?id='.$_ID, 1), array('Поля категорий', '/dev/block/field/category/?id='.$_ID), array('Поля элементов', '/dev/block/field/element/?id='.$_ID)), array('<a href="/k2/admin/dev/block/export.php?id='.$_ID.'" style="color:green">Экспортировать</a>', '<a href="javascript:void(0);" onclick="$.layer({\'get\':\'/k2/admin/dev/block/template-add.php?id='.$_ID.'\', title:\'Добавить шаблон\', w:398}, function(){k2.template.add();})" style="color:green">Добавить шаблон</a>'));

if(!$arBlock = $LIB['BLOCK']->ID($_ID, 0, 1)){
	Redirect('/k2/admin/dev/block/');
}
if($_POST){
	if($nID = $LIB['BLOCK']->Edit($_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/block/');
		}
	}
}else{
	$_POST = $arBlock;
}

$sDirTemplate = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/block/'.$_ID.'/';

?>
	<div class="content">
	<h1>Редактирование</h1>
	<form action="edit.php?id=<?=$_ID?>" method="post" class="form">
		<? formError($LIB['BLOCK']->Error) ?>
		<div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Группа<span class="star">*</span></div>
			<div class="field"><select name="BLOCK_GROUP">
					<?
					$arGroup = $LIB['BLOCK_GROUP']->Rows();
					for($i = 0; $i < count($arGroup); $i++){
						?>
						<option value="<?=$arGroup[$i]['ID']?>"<?
						if($_POST['BLOCK_GROUP'] == $arGroup[$i]['ID']){
							?> selected="selected"<?
						}
						?>><?=$arGroup[$i]['NAME']?>
						</option>
						<?
					}
					?></select></div>
		</div>
		<div class="item">
			<label>
				<input type="checkbox" name="CATEGORY" value="1"<?
				if($_POST['CATEGORY']){
					?> checked="checked"<?
				}
				?>>Отображать категории</label>
		</div>
		<div class="item">
			<div class="name">Файл альтернативной формы редактирования элементов</div>
			<div class="field"><input type="text" name="FORM_EDIT_ELEMENT" value="<?=html($_POST['FORM_EDIT_ELEMENT'])?>"></div>
		</div>
		<div class="item">
			<div class="l">Контроллер</div>
			<div class="r"></div>
			<div class="clear"></div>
			<div class="field">
				<textarea name="CONTROLLER" cols="40" rows="6" data-code="true"><?=html($_POST['CONTROLLER'])?></textarea>
				<div class="note">Файл <?=$sDirTemplate?>controller.php</div>
			</div>
		</div>
		<div class="item">
			<div class="l">Шаблон</div>
			<div class="clear"></div>
			<div class="field">
				<textarea name="TEMPLATE" cols="40" rows="6" data-code="true"><?=html($_POST['TEMPLATE'])?></textarea>
				<div class="note">Файл <?=$sDirTemplate?>template.php</div>
			</div>
		</div>
		<?
		if(file_exists($sDirTemplate.'template-full.php')){
			?>
			<div class="item">
				<div class="l">Шаблон полного вывода</div>
				<div class="r"><a href="/k2/admin/dev/block/template-delete.php?path=/block/<?=$_ID?>/template-full.php" onclick="return $.prompt(this)" class="closeMini" title="Удалить шаблон"></a></div>
				<div class="clear"></div>
				<div class="field">
					<textarea name="TEMPLATE_FULL" cols="40" rows="6" data-code="true"><?=html($_POST['TEMPLATE_FULL'])?></textarea>
					<div class="note">Файл <?=$sDirTemplate?>template-full.php</div>
				</div>
			</div>
			<?
		}

		for($i = 0; $i < count($arBlock['TEMPLATE_OPHEN']); $i++){
			?>
			<div class="item">
			<input type="hidden" name="TEMPLATE_OPHEN[<?=$i?>][ID]" value="<?=$arBlock['TEMPLATE_OPHEN'][$i]['ID']?>">
			<a name="t<?=$arBlock['TEMPLATE_OPHEN'][$i]['ID']?>"></a>
			<div class="l"><?=$arBlock['TEMPLATE_OPHEN'][$i]['NAME']?></div>
			<div class="r"><a href="/k2/admin/dev/block/template-delete.php?id=<?=$arBlock['TEMPLATE_OPHEN'][$i]['ID']?>" onclick="return $.prompt(this)" class="closeMini" title="Удалить шаблон"></a></div>
			<div class="clear"></div>
			<div class="field">
				<textarea name="TEMPLATE_OPHEN[<?=$i?>][TEMPLATE]" cols="40" rows="6" data-code="true"><?=html($_POST['TEMPLATE_OPHEN'][$i]['TEMPLATE'])?></textarea>
				<div class="note">Файл <?=$sDirTemplate.$arBlock['TEMPLATE_OPHEN'][$i]['FILE']?></div>
			</div>
			</div><?
		}
		?>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/block/">отменить</a></p>
		</div>
	</form>
	</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>