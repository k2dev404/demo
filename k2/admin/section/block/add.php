<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

tab(array(array('Раздел', '/section/edit.php?section='.$_SECTION, 1), array('Наполнение', '/section/content/?section='.$_SECTION)));
tab_(array(array('Настройки', '/section/edit.php?section='.$_SECTION), array('Функционал', '/section/block/?section='.$_SECTION, 1), array('Права доступа', '/section/permission.php?section='.$_SECTION)));

if(!$arSection = $LIB['SECTION']->ID($_SECTION, 1)){
	Redirect('/k2/admin/');
}
if($_POST){
	if($nID = $LIB['SECTION_BLOCK']->Add($_SECTION, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/section/block/?section='.$_SECTION);
		}
	}
}else{
	$_POST['ACTIVE'] = 1;
}
?><div class="content">
	<h1>Добавление</h1>
    <form method="post" class="form">
    	<?formError($LIB['SECTION_BLOCK']->Error)?>
        <div class="item">
			<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
			if($_POST['ACTIVE']){
				?> checked<?
			}
			?>>Активность</label>
		</div>
		<div class="item">
			<div class="name">Функциональный блок<span class="star">*</span></div>
			<div class="field"><select name="BLOCK" id="block"><?
			$arGroup = $LIB['BLOCK_GROUP']->Rows();
			for($i=0; $i<count($arGroup); $i++)
			{
				?><optgroup label="<?=$arGroup[$i]['NAME']?>"><?
				$arBlock = $LIB['BLOCK']->Rows($arGroup[$i]['ID']);
				if(!$sName){
					$sName = $arBlock[0]['NAME'];
				}
		        for($n=0; $n<count($arBlock); $n++)
				{
					?><option value="<?=$arBlock[$n]['ID']?>"<?
					if($_POST['BLOCK'] == $arBlock[$n]['ID']){
						?> selected="selected"<?
						if(!$_POST['NAME']){
							$_POST['NAME'] = $arBlock[$n]['NAME'];
						}
					}
					?>><?=$arBlock[$n]['ID']?>. <?=$arBlock[$n]['NAME']?></option><?
				}
				?></optgroup><?
			}
			if(!$_POST['NAME']){
				$_POST['NAME'] = $sName;
			}
			?></select></div>
		</div>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>" id="block-name"></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/section/block/?section=<?=$_SECTION?>">отменить</a></p>
		</div>
		<script type="text/javascript">
		$(function(){
			$('#block').change(function(){
				text = $('#block :selected').text();
				text = text.replace(/\d+\. /, '');
				$('#block-name').val(text);
			});
		});
		</script>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>