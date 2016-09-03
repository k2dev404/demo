<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SETTING');
tab(array(array('Настройки', '/setting/'), array('Списки', '/setting/select/'), array('Сайты', '/setting/site/', 1), array('Обновления', '/setting/update/'), array('Инструменты', '/setting/tool/')));
tab_(array(array('Настройки', '/setting/site/edit.php?id='.$_ID), array('Права доступа', '/setting/site/permission.php?id='.$_ID, 1)));

if(!$arSite = $LIB['SITE']->ID($_ID, 1)){
	Redirect('/k2/admin/setting/site/');
}
$arGroup = $LIB['USER_GROUP']->Rows(1);
if($_POST){
	$arSite['PERMISSION'] = $_POST['PERMISSION'];
	$LIB['SITE']->Edit($_ID, $arSite);
	foreach($_POST['PERMISSION_SITE'] as $nGroup => $nPermission)
	{
		$arUserGroup = $LIB['USER_GROUP']->ID($nGroup);
		$arPermission = array();
		$arPermission = $arUserGroup['PERMISSION_SITE'];
		$arPermission[$_ID] = $nPermission;
        if(!$arPermission[$_ID]){
        	unset($arPermission[$_ID]);
        }
		$LIB['USER_GROUP']->Edit($nGroup, array('PERMISSION_SITE' => $arPermission));
	}
	Redirect('permission.php?id='.$_ID.'&complite=1');
}else{
	$_POST['PERMISSION'] = $arSite['PERMISSION'];
    for($i=0; $i<count($arGroup); $i++)
    {
    	if($arGroup[$i]['ID'] == 1){
    		continue;
    	}
    	$_POST['PERMISSION_SITE'][$arGroup[$i]['ID']] = $arGroup[$i]['PERMISSION_SITE'][$_ID];
    }
}
$arName = array('По умолчанию', 'Просмотр', 'Управление контентом', 'Управление разделом', 'Запретить');
?><div class="content">
	<h1>Права доступа</h1>
    <form action="permission.php?id=<?=$_ID?>" method="post" class="form"><?
    if($_GET['complite']){
		?><div class="complite">Данные сохранены</div><p></p><?
	}
    ?>
	    <table width="100%" class="table">
	    	<tr>
		   		<th class="first">Группа</th>
		   		<th>Права доступа</th>
		   	</tr>
		   	<tr>
				<td>Значение по умолчанию</td>
				<td class="field"><select name="PERMISSION"><?
				for($n=0; $n<count($arName); $n++)
				{
					if(!$n){
						continue;
					}
					?><option value="<?=$n?>"<?if($_POST['PERMISSION'] == $n){?> selected="selected"<?}?>><?=$arName[$n]?></option><?
				}
				?></select></td>
			</tr>
		   	<?
		   	for($i=0; $i<count($arGroup); $i++)
			{
				if($arGroup[$i]['ID'] == 1){
					continue;
				}
				?><tr class="<?
				if($i%2){
					?> odd<?
				}
				?>">
					<td><?=$arGroup[$i]['NAME']?></td>
					<td class="field"><select name="PERMISSION_SITE[<?=$arGroup[$i]['ID']?>]"><?
					for($n=0; $n<count($arName); $n++)
					{
						?><option value="<?=$n?>"<?if($_POST['PERMISSION_SITE'][$arGroup[$i]['ID']] == $n){?> selected="selected"<?}?>><?=$arName[$n]?></option><?
					}
					?></select></td>
				</tr><?
			}
			?>
		</table>
		<div class="saveBlock">
			<p><input type="submit" class="sub" value="Сохранить"></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>