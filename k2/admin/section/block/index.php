<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

tab(array(array('Раздел', '/section/edit.php?section='.$_SECTION, 1), array('Наполнение', '/section/content/?section='.$_SECTION)));
tab_(array(array('Настройки', '/section/edit.php?section='.$_SECTION), array('Функционал', '/section/block/?section='.$_SECTION, 1), array('Права доступа', '/section/permission.php?section='.$_SECTION)));
if(!$arSection = $LIB['SECTION']->ID($_SECTION, 1)){
	Redirect('/k2/admin/');
}
?>
<div class="content">
	<h1>Список функционала</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php?section=<?=$_SECTION?>" class="button">Добавить функционал</a></td>
        </tr>
    </table>
    <table width="100%" class="table">
    	<tr>
	   		<th class="first" width="1%"><div class="icon moveWhite" title="Сортировка"></div></th>
	   		<th width="1%">ID</th>
	   		<th width="50%">Название</th>
	   		<th>Функциональный блок</th>
	   		<th>Элементов</th>
	   		<th>Действие</th>
	   	</tr>
    	<tbody class="sf-body"><?
    	for($i=0; $i<count($arSection['BLOCK']); $i++)
	    {
	    	?><tr class="<?
			if(!$arSection['BLOCK'][$i]['ACTIVE']){
				?>passive<?
			}
			if($i%2){
				?> odd<?
			}
			?>" goto="/k2/admin/section/content/?section=<?=$_SECTION?>&block=<?=$arSection['BLOCK'][$i]['ID']?>" sort_id="<?=$arSection['BLOCK'][$i]['ID']?>">
				<td class="sf-td"><div class="icon move"></div></td>
				<td align="center"><?=$arSection['BLOCK'][$i]['ID']?></td>
				<td><a href="/k2/admin/section/content/?section=<?=$_SECTION?>&section_block=<?=$arSection['BLOCK'][$i]['ID']?>"><?=html($arSection['BLOCK'][$i]['NAME'])?></a></td>
				<td><a href="/k2/admin/dev/block/edit.php?id=<?=$arSection['BLOCK'][$i]['BLOCK']?>"><?=blockName($arSection['BLOCK'][$i]['BLOCK'])?></a></td>
				<td align="center"><a href="/k2/admin/section/content/?section=<?=$_SECTION?>&section_block=<?=$arSection['BLOCK'][$i]['ID']?>"><?
                $arTotalContent = $DB->Rows("SELECT COUNT(ID) AS `TOTAL` FROM `k2_block".$arSection['BLOCK'][$i]['BLOCK']."` WHERE `SECTION_BLOCK` = '".$arSection['BLOCK'][$i]['ID']."'");
                echo $arTotalContent[0]['TOTAL'];
				?></a></td>
				<td align="center"><a href="delete.php?id=<?=$arSection['BLOCK'][$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?section=<?=$_SECTION?>&id=<?=$arSection['BLOCK'][$i]['ID']?>" class="icon edit" title="Редактировать функционал"></a></td>
			</tr><?
	    }
     	if(!$i){
        	?><tr class="noblick empty">
        		<td colspan="6" align="center" height="100">Нет данных</td>
			</tr><?
     	}
		?>
		</tbody>
	</table>
	<script type="text/javascript">k2.block.sort(<?=$_SECTION?>)</script>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>