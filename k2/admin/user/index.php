<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('USER');
tab(array(array('Пользователи', '/user/', 1), array('Группы', '/user/group/')));
?>
	<div class="content">
	<h1>Список пользователей</h1><?

	$arField['ID'] = array('NAME' => 'ID', 'FORMAT' => '', 'ALIGN' => 'center', 'ACTIVE' => 1);
	$arField['LOGIN'] = array('NAME' => 'Логин', 'FORMAT' => '', 'ALIGN' => 'left', 'ACTIVE' => 1);
	$arField['EMAIL'] = array('NAME' => 'E-mail', 'FORMAT' => '', 'ALIGN' => 'left', 'ACTIVE' => 1);
	$arField['USER_GROUP'] = array('NAME' => 'Группа', 'FORMAT' => 'USER_GROUP', 'ALIGN' => 'left', 'ACTIVE' => 1);
	$arField = fieldFormat('k2_user', $arField);

	if($arSettingView = userSettingView(false, array('TYPE' => 10))){
		$arRows = $DB->Rows("SHOW COLUMNS FROM `k2_user`");
		for($i = 0; $i < count($arRows); $i++){
			$arIssetField[$arRows[$i]['Field']] = 1;
		}
		$arNewField = array();
		$arData = unserialize($arSettingView['DATA']);
		foreach($arData as $sKey => $arValue){
			if($arIssetField[$sKey]){
				$arNewField[$sKey] = ($arField[$sKey]['NAME'] ? $arField[$sKey] : $arValue);
				$arNewField[$sKey]['ACTIVE'] = $arValue['ACTIVE'];
				$arNewField[$sKey]['ALIGN'] = $arValue['ALIGN'];
			}
		}
		$arField = $arNewField;
	}

	//p($arData);

	$QB = new QueryBuilder;
	$QB->From('k2_user AS U');
	$QB->Select('U.ID, U.ACTIVE');
	$QB->Num = true;

	$nLimit = 20;
	$arSort = array('FIELD' => 'ID', 'METHOD' => 'asc');
	if($arRows = userSettingSession(true)){
		if($arField[$arRows['PAGE_SORT']['FIELD']]){
			$arSort = $arRows['PAGE_SORT'];
		}
		if($arRows['PAGE_SIZE'] > 1){
			$nLimit = $arRows['PAGE_SIZE'];
		}
	}

	$QB->OrderBy('U.'.$arSort['FIELD'].' '.$arSort['METHOD']);

	$nOffset = 0;
	if($_PAGE > 1){
		$nOffset = $_PAGE * $nLimit - $nLimit;
	}

	$QB->Limit($nOffset.', '.$nLimit);

	$arTableHead[] = array('HTML' => '<th width="1%" class="first"><input type="checkbox" title="Отметить поля" onclick="table.check.all(this, \'.table-body\')"></th>');

	$arTableHead = fieldTableHead('U', $QB, $arField, $arSort, $arTableHead);

	$arTableHead[] = array('NAME' => 'Действие');

	$arList = $DB->Rows($QB->Build());

	$arCount = $DB->Row("SELECT FOUND_ROWS()");

	$nPage = $_PAGE;
	$sNav = navPage($arCount['FOUND_ROWS()'], $nLimit);
	if($nPage > $_PAGE){
		Redirect('/k2/admin/user/');
	}

	$arUserLogin = userAllLogin();

	?>
	<table width="100%" class="nav">
		<tr>
			<td><?=$sNav?></td>
			<td align="right"><a href="#"
			                     onclick="return $.layer({get:'setting.php', title:'Настройки отображения', w:600}, function(){table.sort(-1, 'sf-body');})"
			                     class="button">Настройки отображения</a><a href="add.php" class="button">Добавить
					пользователя</a></td>
		</tr>
	</table>
	<form method="post" id="form">
		<input type="hidden" name="session" value="<?=$USER['SESSION']?>">
		<table width="100%" class="table">
			<tr><?=tableHead($arTableHead, $arSort);?></tr>
			<tbody class="table-body"><?
			for($i = 0; $i < count($arList); $i++){
				?>
				<tr class="<?
				if(!$arList[$i]['ACTIVE']){
					?> passive<?
				}
				?>" goto="edit.php?id=<?=$arList[$i]['ID']?>">
					<td>
						<input type="checkbox" name="ID[]" value="<?=$arList[$i]['ID']?>">
					</td>
					<?
					tableBody(array('CONTENT' => $arList[$i], 'FIELD' => $arField, 'USER_LOGIN' => $arUserLogin, 'PREVIEW' => $arSettingView['PREVIEW']));
					?>
					<td align="center">
						<a href="delete.php?id=<?=$arList[$i]['ID']?>&session=<?=$USER['SESSION']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a>
						<a href="edit.php?id=<?=$arList[$i]['ID']?>" class="icon edit" title="Редактировать"></a>
					</td>
				</tr>
				<?
			}
			if(!$i){
				?>
				<tr class="noblick empty">
				<td colspan="<?=count($arTableHead) + 2?>" align="center" height="100">Нет данных</td>
				</tr><?
			}
			?>
			</tbody>
		</table>
		<table width="100%" class="nav">
			<tr>
				<td>
					<div class="navPage"><?=$sNav?></div>
				</td>
			</tr>
		</table>
	</form>
	<table width="100%" class="select">
		<tr>
			<td>С отмеченными<select id="action" disabled>
					<option value="">Выбрать действие</option>
					<option value="delete">Удалить</option>
				</select>
				<script>
					$('#action').change(function () {
						val = $(this).val();
						if (!val) {
							return false;
						}
						data = $('#form').serialize();
						if (data.length) {
							if (val == 'delete') {
								$.prompt(this, {
									'href': '/k2/admin/user/',
									'yes': 'return actionDelete(1)',
									'no': 'return actionDelete(0)'
								});
							}
						}
					});
					$('#form input').change(function () {
						$('#action')[$('.table-body input:checkbox:checked').size() ? 'removeAttr' : 'attr']('disabled', 'disabled');
					});
				</script>
			</td>
			<td align="right">На странице <select id="sizePage" url="/k2/admin/user/?"><?
					$arSize = array(10, 20, 50, 100);
					for($i = 0; $i < count($arSize); $i++){
						?>
						<option<?
						if($nLimit == $arSize[$i]){
							?> selected<?
						}
						?>><?=$arSize[$i]?></option><?
					}
					?></select></td>
		</tr>
	</table>
	</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>