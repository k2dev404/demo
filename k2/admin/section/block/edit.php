<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

if(!$arSBlock = $LIB['SECTION_BLOCK']->ID($_ID)){
	Redirect('/k2/admin/');
}
$arSection = $LIB['SECTION']->ID($arSBlock['SECTION']);
if($_POST){
	if($nID = $LIB['SECTION_BLOCK']->Edit($_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/section/block/?section='.$arSBlock['SECTION']);
		}
	}
}else{
	$_POST = $arSBlock;
}

tab(array(array('Раздел', '/section/edit.php?section='.$_SECTION, 1), array('Наполнение', '/section/content/?section='.$arSection['ID'])));
tab_(array(array('Настройки', '/section/edit.php?section='.$_SECTION), array('Функционал', '/section/block/?section='.$arSection['ID'], 1), array('Права доступа', '/section/permission.php?section='.$arSection['ID'])));

?><div class="content">
	<h1>Редактирование</h1>
    <form method="post" class="form">
    	<?formError($LIB['SECTION_BLOCK']->Error)?>
        <input type="hidden" name="SORT" value="<?=(int)$_POST['SORT']?>">
        <p>Функциональный блок: <b><?=blockName($arSBlock['BLOCK'])?></b></p>
        <div class="item">
			<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
			if($_POST['ACTIVE']){
				?> checked<?
			}
			?>>Активность</label>
		</div>
		<div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>" id="block-name"></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/section/block/?section=<?=$arSection['ID']?>">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>