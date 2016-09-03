<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/header.php');
permissionCheck('MODULE');

tab(array(array('Модули', '/module/', 1)));
?>
	<div class="content">
	<h1>Список модулей</h1>
	<table width="100%" class="nav">
		<tr>
			<td align="right"><a href="import/" class="button">Импортировать</a></td>
		</tr>
	</table>
	<table width="100%" class="table">
		<tr>
			<th class="first">Модуль</th>
			<th>Описание</th>
			<th>Версия</th>
			<th>Действие</th>
		</tr>
		<tbody><?
		$arModule = $LIB['MODULE']->Rows();
		for ($i = 0; $i < count($arModule); $i++) {
			if (!$arModule[$i]['ACTIVE']) {
				continue;
			}
			$sModule = strtolower($arModule[$i]['MODULE']);
			?>
			<tr goto="<?=$sModule?>/">
			<td><a href="<?=$sModule?>/"><?=$arModule[$i]['NAME']?></a></td>
			<td><?=$arModule[$i]['TEXT']?></td>
			<td align="center"><?=$arModule[$i]['VERSION']?></td>
			<td align="center"><?
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/module/' . $sModule . '/module-delete.php')) {
					?><a href="<?=$sModule?>/module-delete.php" onclick="return $.prompt(this)" class="icon delete"
					     title="Удалить"></a><?
				} else {
					?>
					<div class="icon empty"></div><?
				}
				?><a href="<?=$sModule?>/" class="icon edit" title="Редактировать"></a></td>
			</tr><?
		}
		if (!$i) {
			?>
			<tr class="noblick empty">
				<td colspan="4" align="center" height="100">Нет данных</td>
			</tr><?
		}
		?></tbody>
	</table>
	</div><?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/footer.php');
?>