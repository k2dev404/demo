<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');
$K2->Menu('TAB_SUB', array(array('Настройки', '/dev/form/edit.php?id='.$_ID, 1), array('Поля', '/dev/form/field/?id='.$_ID)));

if(!$arForm = $LIB['FORM']->ID($_ID, 0, 1)){
	Redirect('/k2/admin/dev/form/');
}
if($_POST){
	if($nID = $LIB['FORM']->Edit($_ID, $_POST)){
    	if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/form/');
		}
	}
}else{
	$_POST = $arForm;
}

$sDirTemplate = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/form/'.$_ID.'/';

?><div class="content">
	<h1>Редактирование</h1>
    <form action="edit.php?id=<?=$_ID?>" method="post" class="form">
    	<?formError($LIB['FORM']->Error)?>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div>
		<div class="item">
			<input type="hidden" name="CAPTCHA" value="0"><label><input type="checkbox" name="CAPTCHA" value="1"<?
			if($_POST['CAPTCHA']){
				?> checked<?
			}
			?>>Проверять защитную картинку</label>
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
		</div><?
		for($i=0; $i<count($arForm['TEMPLATE_OPHEN']); $i++)
	    {
	    	?><div class="item">
		    	<input type="hidden" name="TEMPLATE_OPHEN[<?=$i?>][ID]" value="<?=$arForm['TEMPLATE_OPHEN'][$i]['ID']?>">
		    	<a name="t<?=$arForm['TEMPLATE_OPHEN'][$i]['ID']?>"></a>
		    	<div class="l"><?=$arForm['TEMPLATE_OPHEN'][$i]['NAME']?></div>
		    	<div class="r"><a href="/k2/admin/dev/form/template-delete.php?id=<?=$arForm['TEMPLATE_OPHEN'][$i]['ID']?>" onclick="return $.prompt(this)" class="closeMini" title="Удалить шаблон"></a></div>
		    	<div class="clear"></div>
		    	<div class="field">
		    		<textarea name="TEMPLATE_OPHEN[<?=$i?>][TEMPLATE]" cols="40" rows="6" data-code="true"><?=html($_POST['TEMPLATE_OPHEN'][$i]['TEMPLATE'])?></textarea>
		    		<div class="note">Файл <?=$sDirTemplate.$arForm['TEMPLATE_OPHEN'][$i]['FILE']?></div>
		    	</div>
		    </div><?
	    }
		?>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/form/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>