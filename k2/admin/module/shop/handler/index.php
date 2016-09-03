<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

tab(array(array('Модули', '/module/'), array('Интернет-магазин', '/module/shop/', 1)));
tab_(array(
	array('Заказы', '/module/shop/'),
	array('Плательщики', '/module/shop/payer/'),
	array('Адреса', '/module/shop/address/'),
	array('Статусы', '/module/shop/status/'),
	array('Оплата', '/module/shop/payment/'),
	array('Доставка', '/module/shop/delivery/'),
	array('Обработчики', '/module/shop/handler/', 1)
));

?><div class="content">
	<h1>Список обработчиков</h1>
	<table width="100%" class="table">
    	<tr>
	   		<th class="first">Название</th>
	   		<th>Действие</th>
	   	</tr>
    	<tbody><?
    	$arHandler = $MOD['SHOP_HANDLER']->Rows();
		for($i=0; $i<count($arHandler); $i++)
		{
			?><tr goto="<?=$arHandler[$i]['HANDLER']?>/">
				<td><a href="<?=$arHandler[$i]['HANDLER']?>/"><?=$arHandler[$i]['NAME']?></a></td>
				<td align="center"><a href="<?=$arHandler[$i]['HANDLER']?>/" class="icon edit" title="Редактировать"></a></td>
			</tr><?
		}
     	if(!$i){
        	?><tr class="noblick empty">
        		<td colspan="3" align="center" height="100">Нет данных</td>
			</tr><?
     	}
		?>
		</tbody>
	</table>
</div><?

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>