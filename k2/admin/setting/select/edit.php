<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SELECT');
tab(array(array('Настройки', '/setting/'), array('Списки', '/setting/select/', 1), array('Сайты', '/setting/site/'), array('Обновления', '/setting/update/'), array('Инструменты', '/setting/tool/')));
tab_(array(array('Настройки', '/setting/select/edit.php?id='.$_ID, 1), array('Элементы', '/setting/select/option/?id='.$_ID)));
$arSelect = $LIB['SELECT']->ID($_ID);
if(!$arSelect){
	Redirect('/k2/admin/setting/select/');
}
if($_POST){

	if($nID = $LIB['SELECT']->Edit($_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$_ID);
		}else{
			Redirect('/k2/admin/setting/select/');
		}
	}
}else{
	$_POST = $arSelect;
}
?><div class="content">
	<h1>Редактирование</h1>
    <form method="post" class="form">
    	<?formError($LIB['SELECT']->Error)?>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div>
        <div class="item">
			<div class="name">Сортировать по<span class="star">*</span></div>
			<div class="field"><select name="FIELD_SORT"><?
			$arSort = array('ID' => 'ID', 'NAME' => 'Названию');
			foreach($arSort as $nKey=>$nValue)
			{
				?><option value="<?=$nKey?>"<?
				if($nKey == $_POST['FIELD_SORT']){
					?> selected="selected"<?
				}
				?>><?=$nValue?></option><?
			}
			?></select></div>
		</div>
        <div class="item">
			<div class="name">Метод сортировки<span class="star">*</span></div>
			<div class="field"><select name="METHOD_SORT"><?
			$arSort = array('ASC' => 'ASC', 'DESC' => 'DESC');
			foreach($arSort as $nKey=>$nValue)
			{
				?><option value="<?=$nKey?>"<?
				if($nKey == $_POST['FIELD_SORT']){
					?> selected="selected"<?
				}
				?>><?=$nValue?></option><?
			}
			?></select></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/setting/select/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>