<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');

if(!$arForm = $LIB['BLOCK']->ID($_GET['form'])){
	exit;
}

$arField['ID'] =           array('NAME' => 'ID', 'FORMAT' => '', 'ALIGN' => 'center', 'ACTIVE' => 1);
$arField['DATE_CREATED'] = array('NAME' => 'Дата создания', 'FORMAT' => 'DATE_TIME', 'ALIGN' => 'center', 'ACTIVE' => 1);
$arField['DATE_CHANGE'] =  array('NAME' => 'Дата изменения', 'FORMAT' => 'DATE_TIME', 'ALIGN' => 'center', 'ACTIVE' => 0);
$arField['USER_CREATED'] = array('NAME' => 'Кем создана', 'FORMAT' => 'USER', 'ALIGN' => 'center', 'ACTIVE' => 0);
$arField['USER_CHANGE'] =  array('NAME' => 'Кем изменена', 'FORMAT' => 'USER', 'ALIGN' => 'center', 'ACTIVE' => 0);

$arField = fieldFormat('k2_form'.$arForm['ID'], $arField);
if($arSSetting = userSettingView(false, array('TYPE' => 11, 'OBJECT' => $arForm['ID']))){
	$arRows = $DB->Rows("SHOW COLUMNS FROM `k2_form".$arForm['ID']."`");
 	for($i=0; $i<count($arRows); $i++)
	{
		$arIssetField[$arRows[$i]['Field']] = 1;
	}
	$arData = unserialize($arSSetting['DATA']);
	foreach($arData as $sKey => $arValue)
	{
		if($arIssetField[$sKey]){
			$arField[$sKey] = $arValue;
		}
   	}
}

?><form action="/k2/admin/message/form/setting-save.php?form=<?=$arForm['ID']?>&back=<?=html($_GET['back'])?>" method="post">
	<div class="settingFieldBox">
		<table class="table">
		   	<thead>
			   	<tr>
			   		<th class="first"><div class="icon moveWhite" title="Сортировка"></div></th>
			   		<th><input type="checkbox" title="Вкл/выкл показ полей" onclick="table.check.all(this, '.sf-body')"></th>
			   		<th width="99%">Поле</th>
			   	</tr>
		   	</thead>
		   	<tbody class="sf-body"><?
		   	foreach($arField as $sField=>$arProp)
			{
				?><tr>
					<td class="sf-td"><div class="icon move"></div></td>
					<td><input type="checkbox" name="ACTIVE[<?=$sField?>]" value="1"<?
					if($arProp['ACTIVE']){
						?> checked<?
					}
					?>></td>
					<td><input type="hidden" name="FIELD[<?=$sField?>]" value="<?=$sField?>">
					<input type="hidden" name="FORMAT[<?=$sField?>]" value="<?=$arProp['FORMAT']?>">
					<input type="hidden" name="ALIGN[<?=$sField?>]" id="align<?=$i?>" value="<?=$arProp['ALIGN']?>">
		   			<input type="hidden" name="NAME[<?=$sField?>]" value="<?=html($arProp['NAME'])?>">
					<?=html($arProp['NAME'])?></td>
				</tr><?
				$i++;
			}
			?>
			</tbody>
		</table>
	</div>
	<div style="padding-top:5px"><label><input type="checkbox" name="PREVIEW" value="1"<?
	if($arSSetting['PREVIEW']){
		?> checked="checked"<?
	}
	?>>Показать картинки вместо названий</label></div><?
	if($USER['USER_GROUP'] == 1){
		?><div><label><input type="checkbox" name="DEFAULT" value="1"<?
		if($arSSetting['DEFAULT']){
			?> checked="checked"<?
		}
		?>>По умолчанию для всех пользователей</label></div><?
	}
	if($arSSetting){
		?><div><a href="/k2/admin/message/form/setting-reset.php?form=<?=$arForm['ID']?>&back=<?=html($_GET['back'])?>">Сбросить настройки</a></div><?
	}
	?><input type="submit" class="sub rightSub" value="Сохранить">
</form><?
?>