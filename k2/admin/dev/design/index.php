<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');
?>
<div class="content">
	<h1>Список макетов</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php" class="button">Добавить макет</a></td>
        </tr>
    </table>
   	<table width="100%" class="table">
    	<tr>
	   		<th class="first" width="1%">ID</th>
	   		<th>Название</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
	   	$arList = $LIB['DESIGN']->Rows();
	   	for($i=0; $i<count($arList); $i++)
		{
			?><tr goto="edit.php?id=<?=$arList[$i]['ID']?>">
				<td align="center"><?=$arList[$i]['ID']?></td>
				<td><a href="edit.php?id=<?=$arList[$i]['ID']?>"><?=html($arList[$i]['NAME'])?></a></td>
				<td align="center"><a href="delete.php?id=<?=$arList[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arList[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
			</tr><?
		}
    	if(!$i){
        	?><tr class="noblick empty">
        		<td colspan="3" align="center" height="100">Нет данных</td>
			</tr><?
    	}
		?></tbody>
	</table>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>