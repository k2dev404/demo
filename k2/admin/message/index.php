<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

$K2->Menu('TAB');
?>
	<div class="content">
	<h1>Список форм</h1>
	<table width="100%" class="table">
		<tr>
			<th class="first" width="1%">ID</th>
			<th>Название</th>
			<th>Сообщений</th>
		</tr>
		<tbody><?
		$arList = $LIB['FORM']->Rows();
		for ($i = 0; $i < count($arList); $i++) {
			$arRow = $DB->Row("SELECT COUNT(*) AS `TOTAL` FROM `k2_form" . $arList[$i]['ID'] . "`");
			?>
			<tr goto="form/?form=<?=$arList[$i]['ID']?>">
			<td align="center"><?=$arList[$i]['ID']?></td>
			<td><a href="form/?form=<?=$arList[$i]['ID']?>"><?=html($arList[$i]['NAME'])?></a></td>
			<td align="center"><?=$arRow['TOTAL']?></td>
			</tr><?
		}
		if (!$i) {
			?>
			<tr class="noblick empty">
				<td colspan="3" align="center" height="100">Нет данных</td>
			</tr><?
		}
		?></tbody>
	</table>
	</div><?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/footer.php');
?>