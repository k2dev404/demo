<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SEO');
tab(array(array('Модули', '/module/'), array('SEO-инструменты', '/module/seo/', 1)));
tab_(array(
	array('Поисковое продвижение', '/module/seo/page/'),
	array('Перенаправление', '/module/seo/redirect/', 1),
	array('Генератор sitemap.xml', '/module/seo/sitemap/'),
	array('Настройки robot.txt', '/module/seo/robot/')
));

?><div class="content">
	<h1>Список страниц</h1>
	<table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php" class="button">Добавить страницу</a></td>
        </tr>
    </table>
	<table width="100%" class="table">
    	<tr>
	   		<th class="first" width="1%">ID</th>
	   		<th>Путь</th>
	   		<th>Перенаправить</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
	   	$arRows = $MOD['SEO_REDIRECT']->Rows();
		for($i=0; $i<count($arRows); $i++)
		{
			?><tr goto="edit.php?id=<?=$arRows[$i]['ID']?>">
				<td><?=$arRows[$i]['ID']?></td>
				<td><a href="<?=html($arRows[$i]['PATH'])?>" target="_blank"><?=html($arRows[$i]['PATH'])?></a></td>
				<td><a href="edit.php?id=<?=$arRows[$i]['ID']?>"><?=html($arRows[$i]['REDIRECT'])?></a></td>
				<td align="center"><a href="delete.php?id=<?=$arRows[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arRows[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
			</tr><?
		}
    	if(!$i){
        	?><tr class="noblick empty">
        		<td colspan="4" align="center" height="100">Нет данных</td>
			</tr><?
    	}
		?></tbody>
	</table>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>