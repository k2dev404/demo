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
	array('Доставка', '/module/shop/delivery/', 1),
	array('Обработчики', '/module/shop/handler/')
));

?><div class="content">
	<h1>Список способов доставки</h1>
    <table width="100%" class="nav">
    	<tr>
            <td align="right"><a href="add.php" class="button">Добавить доставку</a></td>
        </tr>
    </table>
       	<table width="100%" class="table">
    	<tr>
	   		<th width="1%">ID</th>
	   		<th class="first">Название</th>
	   		<th>Описание</th>
	   		<th>Действие</th>
	   	</tr>
    	<tbody><?
    	$arDelivery = $MOD['SHOP_DELIVERY']->Rows();
		for($i=0; $i<count($arDelivery); $i++)
		{
			?><tr<?
			if(!$arDelivery[$i]['ACTIVE']){
				?> class="passive"<?
			}
			?> goto="edit.php?id=<?=$arDelivery[$i]['ID']?>">
				<td align="center"><?=$arDelivery[$i]['ID']?></td>
				<td><a href="edit.php?id=<?=$arDelivery[$i]['ID']?>"><?=$arDelivery[$i]['NAME']?></a></td>
				<td><?=$arDelivery[$i]['TEXT']?></td>
				<td align="center"><a href="delete.php?id=<?=$arDelivery[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arDelivery[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
			</tr><?
		}
     	if(!$i){
        	?><tr class="noblick empty">
        		<td colspan="4" align="center" height="100">Нет данных</td>
			</tr><?
     	}
		?>
		</tbody>
	</table>
</div><?

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>