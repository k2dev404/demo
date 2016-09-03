<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/header.php');
permissionCheck('SECTION_PERMISSION');

if (!$arSection = $LIB['SECTION']->ID($_SECTION)) {
	Redirect('/k2/admin/');
}

$arGroup = $LIB['USER_GROUP']->Rows(1);
if ($_POST) {
	$arSection['PERMISSION'] = $_POST['PERMISSION'];
	$LIB['SECTION']->Edit($_SECTION, $arSection);
	foreach ($_POST['PERMISSION_SECTION'] as $nGroup => $nPermission) {
		$arUserGroup = $LIB['USER_GROUP']->ID($nGroup);
		$arPermission = array();
		$arPermission = $arUserGroup['PERMISSION_SECTION'];
		$arPermission[$_SECTION] = $nPermission;
		if (!$arPermission[$_SECTION]) {
			unset($arPermission[$_SECTION]);
		}
		$LIB['USER_GROUP']->Edit($nGroup, array('PERMISSION_SECTION' => $arPermission));
	}
	Redirect('permission.php?section=' . $_SECTION . '&complite=1');
} else {
	$_POST['PERMISSION'] = $arSection['PERMISSION'];
	for ($i = 0; $i < count($arGroup); $i++) {
		if ($arGroup[$i]['ID'] == 1) {
			continue;
		}
		$_POST['PERMISSION_SECTION'][$arGroup[$i]['ID']] = $arGroup[$i]['PERMISSION_SECTION'][$_SECTION];
	}
}

tab(array(array('Раздел', '/section/edit.php?section=' . $_SECTION, 1), array('Наполнение', '/section/content/?section=' . $_SECTION)));
tab_(array(array('Настройки', '/section/edit.php?section=' . $_SECTION), array('Функционал', '/section/block/?section=' . $_SECTION), array('Права доступа', '/section/permission.php?section=' . $_SECTION, 1)));
$arName = array('По умолчанию', 'Просмотр', 'Управление контентом', 'Управление разделом', 'Запретить');

?>
	<div class="content">
	<h1>Права доступа</h1>

	<form method="post" class="form"><?
		if ($_GET['complite']) {
			?>
			<div class="complite">Данные сохранены</div><p></p><?
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
						for ($n = 0; $n < count($arName); $n++) {
							if (!$n) {
								continue;
							}
							?>
							<option value="<?=$n?>"<?if ($_POST['PERMISSION'] == $n) {
								?> selected="selected"<?
							}?>><?=$arName[$n]?></option><?
						}
						?></select></td>
			</tr>
			<?

			for ($i = 0; $i < count($arGroup); $i++) {
				if ($arGroup[$i]['ID'] == 1) {
					continue;
				}
				?>
				<tr class="<?
				if ($i % 2) {
					?> odd<?
				}
				?>">
				<td><?=$arGroup[$i]['NAME']?></td>
				<td class="field"><select name="PERMISSION_SECTION[<?=$arGroup[$i]['ID']?>]"><?
						for ($n = 0; $n < count($arName); $n++) {
							?>
							<option value="<?=$n?>"<?if ($_POST['PERMISSION_SECTION'][$arGroup[$i]['ID']] == $n) {
								?> selected="selected"<?
							}?>><?=$arName[$n]?></option><?
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
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/footer.php');
?>