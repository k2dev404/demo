<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SELECT');
tab(array(array('Настройки', '/setting/'), array('Списки', '/setting/select/', 1), array('Сайты', '/setting/site/'), array('Обновления', '/setting/update/'), array('Инструменты', '/setting/tool/')));
tab_(array(array('Настройки', '/setting/select/edit.php?id='.$_ID), array('Элементы', '/setting/select/option/?id='.$_ID, 1)));

if(!$arSelect = $LIB['SELECT']->ID($_ID)){
	Redirect('/k2/admin/setting/select/');
}
if($_POST){
	foreach($_POST['EDIT_OPTION'] as $nKey=>$sName)
	{
		if($sName){
			$LIB['SELECT_OPTION']->Edit($nKey, array('NAME' => $sName));
		}
	}
	for($i=0; $i<count($_POST['NEW_OPTION']); $i++)
	{
		if($_POST['NEW_OPTION'][$i]){
        	$LIB['SELECT_OPTION']->Add($_ID, array('NAME' => $_POST['NEW_OPTION'][$i]));
		}
	}
	if($_POST['BAPPLY_x']){
		Redirect('?id='.$_ID);
	}else{
		Redirect('/k2/admin/setting/select/');
	}
}
?>
<div class="content">
	<h1>Список элементов</h1>
    <form method="post" class="form">
    	<table width="100%" class="table">
    	<tr>
	   		<th width="1%" class="first">ID</th>
	   		<th>Название</th>
	   	</tr><?
	   		$n=0;
			for($i=0; $i<count($arSelect['OPTION']); $i++)
			{
				?><tr class="<?
				if($i%2){
					?> odd<?
				}
				?>">
					<td align="center"><?=$arSelect['OPTION'][$i]['ID']?></td>
					<td class="field"><input type="text" name="EDIT_OPTION[<?=$arSelect['OPTION'][$i]['ID']?>]" value="<?=html($arSelect['OPTION'][$i]['NAME'])?>" tabindex="<?=($n+1)?>"><a href="delete.php?id=<?=$arSelect['OPTION'][$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a></td>
				</tr><?
				$n++;
			}

			for($i=0; $i<5; $i++)
			{
				?><tr class="<?
				if($i%2){
					?> odd<?
				}
				?>">
					<td></td>
					<td class="field"><input type="text" name="NEW_OPTION[]" size="60" value="" tabindex="<?=($n+1)?>"></td>
				</tr><?
				$n++;
			}
		?>
		</table>
		<div class="saveBlock">
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/setting/select/">отменить</a></p>
		</div>
	</form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>