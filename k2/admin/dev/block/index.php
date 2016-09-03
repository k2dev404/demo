<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');
?>
<div class="content">
	<h1>Список функциональных блоков</h1><?
	$arGroup = $LIB['BLOCK_GROUP']->Rows();
	?>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><?
            if($arGroup){
				?><a href="import/" class="button">Импортировать</a><a onclick="return $.layer({get:'/k2/admin/dev/group/add.php', title:'Добавить группу', w:397}, function(){k2.group.add()})" class="button">Добавить группу</a><a href="add.php" class="button">Добавить блок</a><?
			}else{
				?><a onclick="return $.layer({get:'/k2/admin/dev/group/add.php', title:'Добавить группу', w:380}, function(){k2.group.add()})" class="button">1Добавить группу</a><?
			}
            ?></td>
        </tr>
    </table>
   	<table width="100%" class="table">
    	<tr>
	   		<th width="1%">ID</th>
	   		<th>Название</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
		for($i=0; $i<count($arGroup); $i++)
		{
			$arBlock = $LIB['BLOCK']->Rows($arGroup[$i]['ID']);
			?><tr class="group">
				<td></td>
				<td><?=$arGroup[$i]['NAME']?></td>
				<td align="center"><?
				if($arBlock){
	            	?><div class="icon empty"></div><?
				}else{
					?><a href="/k2/admin/dev/group/delete.php?id=<?=$arGroup[$i]['ID']?>" onclick="return $.prompt(this)" class="icon deleteWhite" title="Удалить группу"></a><?
				}
				?><a href="#" onclick="$.layer({'get':'/k2/admin/dev/group/edit.php?id=<?=$arGroup[$i]['ID']?>', 'title':'Редактировать группу', w:398}, function(){k2.group.edit()})" class="icon editWhite" title="Редактировать группу"></a></td>
			</tr><?
			for($n=0; $n<count($arBlock); $n++)
			{
				?><tr goto="edit.php?id=<?=$arBlock[$n]['ID']?>">
					<td><?=$arBlock[$n]['ID']?></td>
					<td align="left"><a href="edit.php?id=<?=$arBlock[$n]['ID']?>"><?=html($arBlock[$n]['NAME'])?></a></td>
					<td align="center" nowrap><a href="delete.php?id=<?=$arBlock[$n]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arBlock[$n]['ID']?>" class="icon edit" title="Редактировать"></a></td>
				</tr><?
			}
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