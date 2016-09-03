<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');
?><div class="content">
	<h1>Список шаблонов</h1>
	<table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php" class="button">Добавить шаблон</a></td>
        </tr>
    </table>
	<table width="100%" class="table">
    	<tr>
	   		<th class="first" width="1%">ID</th>
	   		<th>Название</th>
	   		<th>Тип</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
	   	$arEmail = $LIB['EMAIL']->Rows();
		for($i=0; $i<count($arEmail); $i++)
		{
			?><tr goto="edit.php?id=<?=$arEmail[$i]['ID']?>">
				<td><?=$arEmail[$i]['ID']?></td>
				<td><a href="edit.php?id=<?=$arEmail[$i]['ID']?>"><?=html($arEmail[$i]['NAME'])?></a></td>
				<td><?=$arEmail[$i]['TYPE']?></td>
				<td align="center"><a href="delete.php?id=<?=$arEmail[$i]['ID']?>&session=<?=$USER['SESSION']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arEmail[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
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