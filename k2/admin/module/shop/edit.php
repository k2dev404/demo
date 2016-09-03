<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

tab(array(array('Модули', '/module/'), array('Интернет-магазин', '/module/shop/', 1)));
tab_(array(
	array('Заказы', '/module/shop/', 1), array('Плательщики', '/module/shop/payer/'), array('Адреса', '/module/shop/address/'), array('Статусы', '/module/shop/status/'), array('Оплата', '/module/shop/payment/'), array('Доставка', '/module/shop/delivery/'), array('Обработчики', '/module/shop/handler/')));

if (!$arOrder = $MOD['SHOP_ORDER']->ID($_ID)) {
	Redirect('/k2/admin/module/shop/');
}

if ($_POST) {
	if ($nID = $MOD['SHOP_ORDER']->Edit($_ID, $_POST)) {
		if ($_POST['BAPPLY_x']) {
			Redirect('edit.php?id='.$_ID.'&complite=1');
		} else {
			Redirect('/k2/admin/module/shop/');
		}
	}
} else {
	$_POST = $arOrder;
}

$arPayment_ = $MOD['SHOP_PAYMENT']->Rows();
for ($i = 0; $i < count($arPayment_); $i++) {
	$arPayment[$arPayment_[$i]['ID']] = $arPayment_[$i]['NAME'];
}
$arDelivery_ = $MOD['SHOP_DELIVERY']->Rows();
for ($i = 0; $i < count($arDelivery_); $i++) {
	$arDelivery[$arDelivery_[$i]['ID']] = $arDelivery_[$i]['NAME'];
}
$arPayer_ = $MOD['SHOP_PAYER']->Rows();
for ($i = 0; $i < count($arPayer_); $i++) {
	$arPayer[$arPayer_[$i]['ID']] = $arPayer_[$i]['NAME'];
}
$arUser_ = $LIB['USER']->Rows();
for ($i = 0; $i < count($arUser_); $i++) {
	$arUser[$arUser_[$i]['ID']] = $arUser_[$i]['LOGIN'];
}

?>
	<style type="text/css">
		.shopOrder {
			text-align: right;
			width: 180px;
			white-space: nowrap;
			padding-right: 5px;
		}
		.fieldGroup {
			margin-right: 0 !important;
		}
		.horizont {
			margin-top: 15px;
		}
		.horizont td {
			padding: 5px 15px 0 0;
		}
	</style>
	<div class="content">
	<!-- <a href="print.php?id=<?=$_ID?>" class="button" style="float:right" target="_blank">Распечатать</a> -->
	<h1>Заказ №<?=$arOrder['ID']?></h1>

	<form action="edit.php?id=<?=$_ID?>" method="post" enctype="multipart/form-data" class="form">
		<? formError($MOD['SHOP_ORDER']->Error) ?>
		<div class="fieldGroup">Информация о заказе</div>
		<table width="100%" class="horizont">
			<tr>
				<td class="shopOrder">Статус<span class="star">*</span>:</td>
				<td><select name="STATUS"><?
						$arStatus = $MOD['SHOP_STATUS']->Rows();
						for ($i = 0; $i < count($arStatus); $i++) {
							?>
							<option value="<?=$arStatus[$i]['ID']?>"<?
							if ($_POST['STATUS'] == $arStatus[$i]['ID']) {
								?> selected="selected"<?
							}
							?>><?=$arStatus[$i]['NAME']?></option><?
						}
						?></select></td>
			</tr>
			<tr>
				<td class="shopOrder">Дата создания:</td>
				<td><?=$arOrder['DATE_CREATED']?></td>
			</tr>
			<tr>
				<td class="shopOrder">Дата изменения:</td>
				<td><?=($arOrder['DATE_CHANGE'] == '0000-00-00 00:00:00' ? '-' : $arOrder['DATE_CHANGE'])?></td>
			</tr>
			<tr>
				<td class="shopOrder">Способ оплаты:</td>
				<td><?=($arPayment[$arOrder['PAYMENT']] ? $arPayment[$arOrder['PAYMENT']] : '-')?></td>
			</tr>
			<tr>
				<td class="shopOrder">Способ доставки:</td>
				<td><?=($arDelivery[$arOrder['DELIVERY']] ? $arDelivery[$arOrder['DELIVERY']] : '-')?></td>
			</tr><?
			if ($arOrder['DELIVERY_OPHEN']) {
				?>
				<tr>
				<td class="shopOrder">Транспортная компания:</td>
				<td><?=html($arOrder['DELIVERY_OPHEN'])?></td>
				</tr><?
			}
			?>
			<tr>
				<td class="shopOrder">Плательщик:</td>
				<td><?=($arPayer[$arOrder['PAYER']] ? $arPayer[$arOrder['PAYER']] : '-')?></td>
			</tr>
			<tr>
				<td class="shopOrder">Пользователь:</td>
				<td><?=($arUser[$arOrder['USER_CREATED']] ? '<a href="/k2/admin/user/edit.php?id='.$arOrder['USER_CREATED'].'">'.$arUser[$arOrder['USER_CREATED']].'</a>' : '-')?></td>
			</tr>
		</table>
		<div class="fieldGroup">Плательщик</div>
		<table width="100%" class="horizont"><?
			$arField_ = $LIB['FIELD']->Rows('k2_mod_shop_payer'.$arOrder['PAYER']);
			for ($i = 0; $i < count($arField_); $i++) {
				$arField[$arField_[$i]['FIELD']] = $arField_[$i]['NAME'];
			}
			$arPayerElement = $MOD['SHOP_PAYER_ELEMENT']->Rows($arOrder['PAYER'], array('ORDER' => $_ID));
			for ($i = 0; $i < count($arPayerElement); $i++) {
				foreach ($arPayerElement[$i] as $sKey => $sText) {
					if (!$arField[$sKey]) {
						continue;
					}
					?>
					<tr>
					<td class="shopOrder"><?=$arField[$sKey]?>:</td>
					<td><?=$sText?></td>
					</tr><?
				}
			}
		?></table><?
		$arField_ = $LIB['FIELD']->Rows('k2_mod_shop_address');
		if ($arAddress = $MOD['SHOP_ADDRESS']->Rows(array('ORDER' => $arOrder['ID']))) {
			?>
			<div class="fieldGroup">Адрес доставки</div>
			<table width="100%" class="horizont"><?

			$arField = array();
			for ($i = 0; $i < count($arField_); $i++) {
				?>
				<tr>
				<td class="shopOrder"><?=$arField_[$i]['NAME']?>:</td>
				<td><?
					if ($arAddress[0][$arField_[$i]['FIELD']]) {
						echo $arAddress[0][$arField_[$i]['FIELD']];
					} else {
						?>-<?
					}
					?></td>
				</tr><?
			}
			?></table><?
		}
		?>
		<div class="fieldGroup">Состав заказа</div>
		<br>
		<table width="100%" class="table">
			<tr>
				<th>Название</th>
				<th>Артикул</th>
				<th>Свойства</th>
				<th>Количество</th>
				<th>Цена</th>
			</tr><?
			$arOrderProduct = $MOD['SHOP_ORDER_PRODUCT']->Rows(array('ORDER' => $_ID));
			for ($i = 0; $i < count($arOrderProduct); $i++) {
				?>
				<tr>
					<td><?=$arOrderProduct[$i]['NAME']?></td>
					<td><?=$arOrderProduct[$i]['ARTICLE']?></td>
					<td><?
						if ($arData = unserialize($arOrderProduct[$i]['DATA'])) {
							foreach ($arData as $sKey => $sValue) {
								?><?=$sKey?>: <?=$sValue?><br><?
							}
						}
						?></td>
					<td align="center"><?=$arOrderProduct[$i]['QUANTITY']?></td>
					<td align="right"><?=$arOrderProduct[$i]['PRICE']?></td>
				</tr><?
			}
			?>
			<tr>
				<td colspan="4" align="right"><b>Доставка</b>:</td>
				<td align="right"><?
					if (($arDelivery = $MOD['SHOP_DELIVERY']->ID($arOrder['DELIVERY'])) && $arDelivery['PRICE'] != '0.00'){
						echo $arDelivery['PRICE'];
					}else{
						?>0<?
					}
				?></th>
			</tr>
			<tr>
				<td colspan="4" align="right"><b>Итого</b>:</td>
				<td align="right">
				<?=$arOrder['SUM']?></th>
			</tr>
		</table><?
		if ($sText = html($arOrder['COMMENT'])) {
			?>
			<div class="fieldGroup">Комментарий к заказу</div>
			<p style="padding-left:15px;"><?=$sText?></p><?
		}
		?>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>

			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/module/shop/">отменить</a></p>
		</div>
	</form>
	</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>