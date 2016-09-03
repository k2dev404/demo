<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('USER', 'GROUP');
tab(array(array('Пользователи', '/user/'), array('Группы', '/user/group/', 1)));
?>
<div class="content">
	<h1>Список групп</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php" class="button">Добавить группу</a></td>
        </tr>
    </table>
    <table width="100%" class="table">
    	<tr>
	   		<th width="1%" class="first">ID</th>
	   		<th>Название</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
   		$arList = $LIB['USER_GROUP']->Rows();
    	for($i=0; $i<count($arList); $i++)
		{
			?><tr class="<?
			if($i%2){
				?> odd<?
			}
			?>" goto="edit.php?id=<?=$arList[$i]['ID']?>">
            	<td align="center"><?=$arList[$i]['ID']?></td>
                <td><a href="edit.php?id=<?=$arList[$i]['ID']?>"><?=$arList[$i]['NAME']?></a></td>
				<td align="center"><?
				if($DB->Row("SELECT 1 FROM `k2_user` WHERE `USER_GROUP` = '".$arList[$i]['ID']."' LIMIT 1")){
                	?><div class="icon empty"></div><?
				}else{
					?><a href="delete.php?id=<?=$arList[$i]['ID']?>&session=<?=$USER['SESSION']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><?
				}
				?><a href="edit.php?id=<?=$arList[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
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