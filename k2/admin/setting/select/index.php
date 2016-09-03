<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SELECT');
tab(array(array('Настройки', '/setting/'), array('Списки', '/setting/select/', 1), array('Сайты', '/setting/site/'), array('Обновления', '/setting/update/'), array('Инструменты', '/setting/tool/')));
?>
<div class="content">
	<h1>Перечень списков</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php" class="button">Добавить список</a></td>
        </tr>
    </table>
    <table width="100%" class="table">
    	<tr>
	   		<th width="1%" class="first">ID</th>
	   		<th width="50%">Название</th>
	   		<th>Элементов</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
    	$arSelect = $LIB['SELECT']->Rows();
       	for($i=0; $i<count($arSelect); $i++)
		{
			?><tr class="<?
			if($i%2){
				?> odd<?
			}
			?>" goto="option/?id=<?=$arSelect[$i]['ID']?>">
				<td align="center"><?=$arSelect[$i]['ID']?></td>
				<td><a href="option/?id=<?=$arSelect[$i]['ID']?>"><?=html($arSelect[$i]['NAME'])?></a></td>
				<td><?
			    $arSelect_ = $LIB['SELECT']->ID($arSelect[$i]['ID']);
				echo count($arSelect_['OPTION']);
				?></td>
				<td align="center"><a href="delete.php?id=<?=$arSelect[$i]['ID']?>&session=<?=$USER['SESSION']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arSelect[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
			</tr><?
		}
     	if(!$i){
        	?><tr class="noblick empty">
        		<td colspan="5" align="center" height="100">Нет данных</td>
			</tr><?
     	}
		?></tbody>
	</table>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>