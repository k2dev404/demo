<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SETTING');
tab(array(array('Настройки', '/setting/'), array('Списки', '/setting/select/'), array('Сайты', '/setting/site/', 1), array('Обновления', '/setting/update/'), array('Инструменты', '/setting/tool/')));
if($_POST){
	if($nID = $LIB['SITE']->Add($_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/setting/site/');
		}
	}
}else{
	$_POST['ACTIVE'] = 1;
}
?><div class="content">
	<h1>Добавление</h1>
    <form method="post" enctype="multipart/form-data" class="form">
    	<?formError($LIB['SITE']->Error)?>
        <div class="item">
			<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
			if($_POST['ACTIVE']){
				?> checked<?
			}
			?>>Активность</label>
		</div>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Домен</div>
			<div class="field"><input type="text" name="DOMAIN" value="<?=html($_POST['DOMAIN'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Псевдонимы(по одному на строчке)</div>
			<div class="field"><textarea type="text" name="ALIAS" rows="6"><?=html($_POST['ALIAS'])?></textarea></div>
		</div>
        <div class="item">
			<div class="name">Шаблон дизайна<span class="star">*</span></div>
			<div class="field"><select name="DESIGN"><?
			$arDesign = $LIB['DESIGN']->Rows();
			for($i=0; $i<count($arDesign); $i++)
			{
				?><option value="<?=$arDesign[$i]['ID']?>"<?
				if($_POST['DESIGN'] == $arDesign[$i]['ID']){
					?> selected<?
				}
				?>><?=$arDesign[$i]['ID']?>. <?=html($arDesign[$i]['NAME'])?></option><?
			}
			?></select></div>
		</div><?
		$arField = array_merge($LIB['FIELD']->Rows('k2_site'), $LIB['FIELD_SEPARATOR']->Rows('k2_site'));
		for($i=0; $i<count($arField); $i++)
		{
	        if(!$i){
	        	usort($arField, 'sortArray');
	        }
	        if(!$arField[$i]['FIELD']){
	        	?><div class="fieldGroup"><?=$arField[$i]['NAME']?></div><?
	        }else{
	       		echo $LIB['FORM']->Element($arField[$i]['ID'], '<div class="item"><div class="name">%NAME%</div><div class="field">%FIELD%</div></div>');
	        }
		}
		?>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/setting/site/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>