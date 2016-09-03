<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');
$K2->Menu('TAB_SUB', array(array('Настройки', '/dev/block/edit.php?id='.$_ID), array('Поля категорий', '/dev/block/field/category/?id='.$_ID), array('Поля элементов', '/dev/block/field/element/?id='.$_ID, 1)));

if(!$arBlock = $LIB['BLOCK']->ID($_ID, 0, 1)){
	Redirect('/k2/admin/dev/block/');
}

$arType = $LIB['FIELD']->Type();

?><div class="content">
	<h1>Список полей</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php?id=<?=$_ID?>" class="button">Добавить поле</a></td>
        </tr>
    </table>
       	<table width="100%" class="table">
    	<tr>
	   		<th width="1%"><div class="icon moveWhite" title="Сортировка"></div></th>
	   		<th width="1%">ID</th>
	   		<th width="50%">Описание</th>
	   		<th>Поле</th>
	   		<th>Тип</th>
	   		<th>Обязательное</th>
	   		<th>Действие</th>
	   	</tr>
    	<tbody class="sf-body"><?
    	$arField = $LIB['FIELD']->Rows('k2_block'.$_ID);
       	for($i=0; $i<count($arField); $i++)
		{
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
				<td align="center"><a href="delete.php?id=<?=$arField[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arField[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
			</tr><?
		}
     	if(!$i){
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