<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');
$K2->Menu('TAB_SUB');

$arType = $LIB['FIELD']->Type();
?>
<div class="content">
	<h1>Список полей</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="#" onclick="return $.layer({get:'/k2/admin/dev/field/separator/add.php?table=k2_user', title:'Добавить разделитель', w:380}, function(){k2.group.add()})" class="button">Добавить разделитель</a><a href="add.php" class="button">Добавить поле</a></td>
        </tr>
    </table>
   	<table width="100%" class="table">
    	<tr>
	   		<th class="first" width="1%"><div class="icon moveWhite" title="Сортировка"></div></th>
	   		<th width="1%">ID</th>
	   		<th width="50%">Описание</th>
	   		<th>Поле</th>
	   		<th>Тип</th>
	   		<th>Обязательное</th>
	   		<th>Действие</th>
	   	</tr>
    	<tbody class="sf-body"><?
    	if($arField = array_merge($LIB['FIELD']->Rows('k2_user'), $LIB['FIELD_SEPARATOR']->Rows('k2_user'))){
        	usort($arField, 'sortArray');
        	for($i=0; $i<count($arField); $i++)
			{
				if(!$arField[$i]['FIELD']){
                ?><tr field="<?=$arField[$i]['ID']?>" class="group">
						<td class="sf-td"><div class="icon move"></div></td>
						<td></td>
						<td colspan="4"><?=html($arField[$i]['NAME'])?></td>
						<td align="center"><a href="/k2/admin/dev/field/separator/delete.php?id=<?=$arField[$i]['ID']?>&back=<?=base64_encode($_SERVER['REQUEST_URI'])?>" onclick="return $.prompt(this)" class="icon deleteWhite" title="Удалить разделитель"></a><a href="#" onclick="$.layer({'get':'/k2/admin/dev/field/separator/edit.php?id=<?=$arField[$i]['ID']?>', 'title':'Редактировать разделитель', w:397}, function(){k2.group.edit()})" class="icon editWhite" title="Редактировать разделитель"></a></td>
					</tr><?
					continue;
				}
				?><tr goto="edit.php?id=<?=$arField[$i]['ID']?>" field="<?=$arField[$i]['ID']?>">
					<td class="sf-td"><div class="icon move"></div></td>
                    <td align="center"><?=$arField[$i]['ID']?></td>
					<td><a href="edit.php?id=<?=$arField[$i]['ID']?>"><?=$arField[$i]['NAME']?></a></td>
					<td><?=$arField[$i]['FIELD']?></td>
					<td><?
					echo $arType[$arField[$i]['TYPE']]['NAME'];
					if($arField[$i]['SETTING']['TYPE']){
						?> [ <?=$arType[$arField[$i]['TYPE']]['SETTING']['TYPE'][$arField[$i]['SETTING']['TYPE']]['NAME']?> ]<?
					}
					?></td>
					<td align="center"><?=($arField[$i]['REQUIRED']?'Да':'Нет')?></td>
					<td align="center"><a href="delete.php?id=<?=$arField[$i]['ID']?>&session=<?=$USER['SESSION']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arField[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
				</tr><?
			}
    	}else{
        	?><tr class="noblick empty">
        		<td colspan="7" align="center" height="100">Нет данных</td>
			</tr><?
    	}
		?>
		</tbody>
	</table>
	<script type="text/javascript">table.sort(0)</script>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>