<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SETTING');
$K2->Menu('TAB');

tab_(array(array('Настройки', '/setting/site/edit.php?id='.$_ID, 1), array('Права доступа', '/setting/site/permission.php?id='.$_ID)));

if(!$arSite = $LIB['SITE']->ID($_ID)){
	Redirect('/k2/admin/site/');
}
if($_POST){
	if($nID = $LIB['SITE']->Edit($_ID, $_POST, 1)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$_ID.'&complite=1');
		}else{
			Redirect('/k2/admin/setting/site/');
		}
	}
}else{
	$_POST = $arSite;
}
?><div class="content">
	<h1>Редактирование</h1>
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
		</div>
		<div class="item">
			<div class="name">Начальная страница<span class="star">*</span></div>
			<div class="field"><select name="SECTION_INDEX"><?
			$arSection = $LIB['SECTION']->Map(array('SITE' => $_ID));
			for($i=0; $i<count($arSection); $i++)
			{
				?><option value="<?=$arSection[$i]['ID']?>"<?
				if($_POST['SECTION_INDEX'] == $arSection[$i]['ID']){
					?> selected<?
				}
				?>><?=str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $arSection[$i]['LEVEL'])?>|--- <?=html($arSection[$i]['NAME'])?></option><?
			}
			?></select></div>
		</div>
        <div class="item">
			<div class="name">Страница 404<span class="star">*</span></div>
			<div class="field"><select name="SECTION_NOT_FOUND"><?
			for($i=0; $i<count($arSection); $i++)
			{
				?><option value="<?=$arSection[$i]['ID']?>"<?
				if($_POST['SECTION_NOT_FOUND'] == $arSection[$i]['ID']){
					?> selected="selected"<?
				}
				?>><?=str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $arSection[$i]['LEVEL'])?>|--- <?=html($arSection[$i]['NAME'])?></option><?
			}
			?></select></div>
		</div>
		<?
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