<?
global $DILER_PRICE;

if($_GET['order']){
	Delayed('H1', 'Спасибо! Ваш заказ принят');
	$this->Template('complite.php');
	return;
}

if($_GET['ajax']){
	if($_GET['action'] == 'add'){
		$arElm = $LIB['BLOCK_ELEMENT']->ID($_POST['ID'], 9);
		if (!$arElm) {
			exit;
		}

		if(!$_POST['QUANTITY']){
			$_POST['QUANTITY'] = 1;
		}

		$arPhoto = $LIB['PHOTO']->Preview($arElm['PHOTO'], array('WIDTH' => 130, 'HEIGHT' => 130, 'FIX' => 1));

		$arPar = array(
				'CODE' => $arElm['ID'],
				'NAME' => $arElm['NAME'],
				'PRICE' => $arElm['PRICE'],
				'QUANTITY' => $_POST['QUANTITY'],
				'ARTICLE' => $arElm['NAME'],
				'DATA_TMP' => array('PHOTO' => $arPhoto['PATH'], 'URL' => $arElm['URL'])
		);

		$MOD['SHOP_CART']->Add($arPar);

		echo $MOD['SHOP_CART']->Quantity();

		exit;
	}

	if($_GET['action'] == 'calc'){
		foreach($_POST['QUANTITY'] as $nKey => $nQuantity)
		{
			$MOD['SHOP_CART']->Reload(array('CODE' => $nKey, 'QUANTITY' => $nQuantity));
		}
	}

	if($_GET['action'] == 'order'){
		if(!$_POST['agree']){
			exit('Для оформления заказа необходимо принять условия продажи');
		}

		$arPost = $_POST;
		$arPost['PAYER'] = 1;
		$arPost['STATUS'] = 1;

		if ($nID = $MOD['SHOP_ORDER']->Add($arPost)) {

			$_SESSION['ORDER'] = $nID;

			echo 'ok';
		}else{
			echo $MOD['SHOP_ORDER']->Error;
		}
		exit;
	}

	if($_GET['action'] == 'contact'){
		if ($sError = $LIB['FIELD']->CheckAll('k2_mod_shop_payer1', $_POST)) {
			?>[{error: '<?=$sError?>'}]<?
		} else {
			$_SESSION['ORDER']['NAME'] = $_POST['NAME'];
			$_SESSION['ORDER']['EMAIL'] = $_POST['EMAIL'];
			$_SESSION['ORDER']['PHONE'] = $_POST['PHONE'];

			?>[{redirect: '?action=delivery', error: ''}]<?
		}
		exit;
	}

	if($_GET['action'] == 'delivery'){
		if (!$MOD['SHOP_DELIVERY']->ID($_POST['DELIVERY'])) {
			$sError = 'Укажите способ доставки';
		} elseif ($_POST['DELIVERY'] == 2 && ($sError = $LIB['FIELD']->CheckAll('k2_mod_shop_address', $_POST))) {

		}

		if($sError){
			?>[{error: '<?=$sError?>'}]<?
		}else {
			$_SESSION['ORDER']['DELIVERY'] = $_POST['DELIVERY'];
			$_SESSION['ORDER']['CITY'] = $_POST['CITY'];
			$_SESSION['ORDER']['STREET'] = $_POST['STREET'];
			$_SESSION['ORDER']['HOME'] = $_POST['HOME'];
			$_SESSION['ORDER']['KV'] = $_POST['KV'];
			$_SESSION['ORDER']['INDEX'] = $_POST['INDEX'];
			$_SESSION['ORDER']['TEXT'] = $_POST['TEXT'];

			?>[{redirect: '?action=payment', error: ''}]<?
		}
		exit;
	}

	if($_GET['action'] == 'payment'){
		if (!$MOD['SHOP_PAYMENT']->ID($_POST['PAYMENT'])) {
			$sError = 'Укажите способ оплаты';
		}

		if($sError){
			?>[{error: '<?=$sError?>'}]<?
		}else {
			$_SESSION['ORDER']['PAYMENT'] = $_POST['PAYMENT'];

			?>[{redirect: '?action=result', error: ''}]<?
		}
		exit;
	}

	if($_GET['action'] == 'result'){

		$_SESSION['ORDER']['COMMENT'] = $_POST['COMMENT'];

		$_SESSION['ORDER']['STATUS'] = 1;
		$_SESSION['ORDER']['PAYER'] = 1;

		$arCart = $MOD['SHOP_CART']->Rows();

		if ($nID = $MOD['SHOP_ORDER']->Add($_SESSION['ORDER'])) {

			$arPayment = $MOD['SHOP_PAYMENT']->ID($_SESSION['ORDER']['PAYMENT']);
			$arDelivery = $MOD['SHOP_DELIVERY']->ID($_SESSION['ORDER']['DELIVERY']);

			$arField = $LIB['FIELD']->Rows('k2_mod_shop_payer1');
			for ($i = 0; $i < count($arField); $i++) {
				$this->Prop[] = array($arField[$i]['NAME'], $_SESSION['ORDER'][$arField[$i]['FIELD']]);
			}

			$this->Prop[] = array('Способ оплаты', $arPayment['NAME']);
			$this->Prop[] = array('Вариант доставки', $arDelivery['NAME']);

			if ($arDelivery['ID'] > 1) {
				$arAddress[] = $_SESSION['ORDER']['INDEX'];
				$arAddress[] = $_SESSION['ORDER']['CITY'];
				$arAddress[] = 'ул. '.$_SESSION['ORDER']['STREET'];
				$arAddress[] = $_SESSION['ORDER']['HOME'];
				if ($_SESSION['ORDER']['KV']) {
					$arAddress[] = 'кв. '.$_SESSION['ORDER']['KV'];
				}
				$this->Address = implode(', ', $arAddress);
				$this->Prop[] = array('Адрес доставки', implode(', ', $arAddress));
			}

			$sBody .= '<h4>Состав заказа</h4>
		<table cellpadding="2" cellspacing="0" border="1">
			<tr>
				<th>Артикул</th>
				<th>Наименование</th>
				<th>Кол-во</th>
				<th>Цена</th>
			</tr>';
			for ($i = 0, $c = count($arCart); $i < $c; $i++) {
				$nTotal += $arCart[$i]['PRICE'] * $arCart[$i]['QUANTITY'];

				$sBody .= '<tr>
				<td>'.$arCart[$i]['ARTICLE'].'</td>
				<td>'.$arCart[$i]['NAME'].'</td>
				<td align="center">'.$arCart[$i]['QUANTITY'].'</td>
				<td>'.(int)$arCart[$i]['PRICE'].'  руб.</td>
				</tr>';
			}
			$this->Total = $this->TotalFinal = $nTotal;
			if ($arDelivery['PRICE']) {
				$this->TotalFinal = $nTotal + $arDelivery['PRICE'];
			}


			$sBody .= '</table><br>
		<p><b>Сумма: '.(int)$this->Total.' руб.</b><br>
		<b>Итого: '.(int)$this->TotalFinal.' руб.</b><br></p>';

			$sBody .= '<h4>Информация о заказе</h4>';
			foreach ($this->Prop as $arArray) {
				$sBody .= $arArray[0].': '.html($arArray[1]).'<br>';
			}

			$LIB['EMAIL']->Send('', 'FORM', array('TEXT' => $sBody), 1);
			$LIB['EMAIL']->Send($_SESSION['ORDER']['EMAIL'], 'SHOP_ORDER_USER', array('TEXT' => $sBody, 'USER' => $_SESSION['ORDER']['NAME'], 'ORDER' => $nID), 1);


			?>[{redirect: '/cart/?order=<?=$nID?>', error: ''}]<?
		} else {
			?>[{error: '<?=$MOD['SHOP_ORDER']->Error?>'}]<?
		}
		exit;
	}
}

if($_GET['action'] == 'delete'){
	$MOD['SHOP_CART']->Delete($_GET['id']);
	Redirect($CURRENT['SECTION']['URL']);
}

$this->Cart = $MOD['SHOP_CART']->Rows(false, false, array('ID' => 'ASC'));

if(!$this->Cart){
	?>
	<div class="cartEmpty">Корзина пуста</div>
	<?
	return;
}

$this->Delivery = $MOD['SHOP_DELIVERY']->Rows();
$this->Payment = $MOD['SHOP_PAYMENT']->Rows();

if($_GET['action'] == 'result'){
	$arField = $LIB['FIELD']->Rows('k2_mod_shop_payer1');
	for ($i = 0; $i < count($arField); $i++) {
		$this->Prop[] = array($arField[$i]['NAME'], $_SESSION['ORDER'][$arField[$i]['FIELD']]);
	}

	$arPayment = $MOD['SHOP_PAYMENT']->ID($_SESSION['ORDER']['PAYMENT']);
	$arDelivery = $MOD['SHOP_DELIVERY']->ID($_SESSION['ORDER']['DELIVERY']);

	$this->Prop[] = array('Способ оплаты', $arPayment['NAME']);
	$this->Prop[] = array('Вариант доставки', $arDelivery['NAME']);

	if ($arDelivery['ID'] > 1) {
		$arAddress[] = $_SESSION['ORDER']['INDEX'];
		$arAddress[] = $_SESSION['ORDER']['CITY'];
		$arAddress[] = 'ул. '.$_SESSION['ORDER']['STREET'];
		$arAddress[] = $_SESSION['ORDER']['HOME'];
		if ($_SESSION['ORDER']['KV']) {
			$arAddress[] = 'кв. '.$_SESSION['ORDER']['KV'];
		}
		$this->Address = implode(', ', $arAddress);
		$this->Prop[] = array('Адрес доставки', implode(', ', $arAddress));
	}
}








$this->Menu = array(
		'contact' => 'Контактная информация',
		'delivery' => 'Способ получения',
		'payment' => 'Оплата',
		'result' => 'Завершение оформления'
);

$this->Template();
?>