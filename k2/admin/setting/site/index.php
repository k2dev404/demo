<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SETTING');
tab(array(array('Настройки', '/setting/'), array('Списки', '/setting/select/'), array('Сайты', '/setting/site/', 1), array('Обновления', '/setting/update/'), array('Инструменты', '/setting/tool/')));
?>
<div class="content">
	<h1>Список сайтов</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php" class="button">Добавить сайт</a></td>
        </tr>
    </table>
    <table width="100%" class="table">
    	<tr>
	   		<th width="1%" class="first">ID</th>
	   		<th width="50%">Название</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
    	$arSite = $LIB['SITE']->Rows();
       	for($i=0; $i<count($arSite); $i++)
		{
			?><tr class="<?
			if($i%2){
				?> odd<?
			}
			?>" goto="/k2/admin/setting/site/edit.php?id=<?=$arSite[$i]['ID']?>">
	            <td align="center"><?=$arSite[$i]['ID']?></td>
	            <td><a href="edit.php?id=<?=$arSite[$i]['ID']?>"><?=html($arSite[$i]['NAME'])?></a></td>
	            <td align="center"><?
	            if($DB->Rows("SELECT 1 FROM `k2_section` WHERE `SITE` = ".$arSite[$i]['ID']." LIMIT 1")){
	            	?><div class="icon empty"></div><?
	            }else{
	            	?><a href="delete.php?id=<?=$arSite[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><?
	            }
	            ?><a href="http://<?=$arSite[$i]['DOMAIN']?>" class="icon home" target="_blank" title="Перейти на сайт"></a><a href="edit.php?id=<?=$arSite[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
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