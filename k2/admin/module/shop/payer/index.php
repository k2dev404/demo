<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

tab(array(array('Модули', '/module/'), array('Интернет-магазин', '/module/shop/', 1)));
tab_(array(
	array('Заказы', '/module/shop/'),
	array('Плательщики', '/module/shop/payer/', 1),
	array('Адреса', '/module/shop/address/'),
	array('Статусы', '/module/shop/status/'),
	array('Оплата', '/module/shop/payment/'),
	array('Доставка', '/module/shop/delivery/'),
	array('Обработчики', '/module/shop/handler/')
));

?><div class="content">
	<h1>Список плательщиков</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php" class="button">Добавить плательщика</a></td>
        </tr>
    </table>
   	<table width="100%" class="table">
    	<tr>
	   		<th class="first" width="1%">ID</th>
	   		<th>Название</th>
	   		<th>Действие</th>
	   	</tr>
	   	<tbody><?
		$arPayer = $MOD['SHOP_PAYER']->Rows();
		for($i=0; $i<count($arPayer); $i++)
		{
			?><tr<?
			if(!$arPayer[$i]['ACTIVE']){
				?> class="passive"<?
			}
			?> goto="edit.php?id=<?=$arPayer[$i]['ID']?>">
				<td align="center"><?=$arPayer[$i]['ID']?></td>
				<td><a href="edit.php?id=<?=$arPayer[$i]['ID']?>"><?=$arPayer[$i]['NAME']?></a></td>
				<td align="center"><a href="delete.php?id=<?=$arPayer[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arPayer[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
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