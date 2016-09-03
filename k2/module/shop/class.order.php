<?

class ShopOrder
{
	function ID($nID)
	{
		global $LIB, $DB;
		if ($arOrder = $DB->Row("SELECT * FROM `k2_mod_shop_order` WHERE `ID` = '".$nID."'")) {
			return $arOrder;
		}
		$this->Error = 'Заказ не найден';

		return false;
	}

	function Rows($arFilter = array(), $arOrderBy = array(), $arSelect = array(), $nSize = 0, $nLimit = 0)
	{
		global $LIB, $DB;

		if ($nSize) {
			$LIB['NAV']->Setting['SIZE'] = $nSize;
			$LIB['NAV']->Setting['TOTAL'] = 0;
		}

		if ($nLimit) {
			$LIB['NAV']->Setting = array();
		}

		$arCFilter = array('FROM' => 'k2_mod_shop_order', 'WHERE' => $arFilter, 'ORDER_BY' => $arOrderBy, 'SELECT' => $arSelect, 'SIZE' => $nSize, 'LIMIT' => $nLimit);

		$sSQL = $DB->CSQL($arCFilter);

		if ((!$arList = $DB->Rows($sSQL)) && $_GET['page'] > 1) {
			$_GET['page'] = 1;
			$sSQL = $DB->CSQL($arCFilter);
			$arList = $DB->Rows($sSQL);
		}
		if ($arList) {
			$arCount = $DB->Row("SELECT FOUND_ROWS()");
			$LIB['NAV']->Setting['TOTAL'] = $arCount['FOUND_ROWS()'];
		}

		return $arList;
	}

	function Add($arPar = array())
	{
		global $MOD, $DB, $USER, $LIB;

		if (!$arCart = $MOD['SHOP_CART']->Rows()) {
			$this->Error = 'Корзина пуста';

			return false;
		}

		if (!$MOD['SHOP_PAYER']->ID($arPar['PAYER'])) {
			$this->Error = 'Укажите плательщика';

			return false;
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2_mod_shop_payer'.$arPar['PAYER'], $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($arPar['DELIVERY'] != 1) {
			if ($sError = $LIB['FIELD']->CheckAll('k2_mod_shop_address', $arPar)) {
				$this->Error = $sError;

				return false;
			}
		}

		if (!$MOD['SHOP_STATUS']->ID($arPar['STATUS'])) {
			$this->Error = 'Укажите статус';

			return false;
		}

		if (!$arDelivery = $MOD['SHOP_DELIVERY']->ID($arPar['DELIVERY'])) {
			$this->Error = 'Укажите способ доставки';

			return false;
		}

		if (!$MOD['SHOP_PAYMENT']->ID($arPar['PAYMENT'])) {
			$this->Error = 'Укажите способ оплаты';

			return false;
		}

		if ($nID = $DB->Insert("
		INSERT INTO `k2_mod_shop_order`(
			`DATE_CREATED`,
			`USER_CREATED`,
			`PAYER`,
			`STATUS`,
			`SUM`,
			`DELIVERY`,
			`PAYMENT`,
			`COMMENT`
		)VALUES(
			NOW(), '".$USER['ID']."', '".(int)$arPar['PAYER']."', '".(int)$arPar['STATUS']."', '".($MOD['SHOP_CART']->Sum() + $arDelivery['PRICE'])."', '".(int)$arPar['DELIVERY']."', '".(int)$arPar['PAYMENT']."', '".DBS($arPar['COMMENT'])."'
		);")
		) {
			for ($i = 0; $i < count($arCart); $i++) {
				$MOD['SHOP_ORDER_PRODUCT']->Add($nID, $arCart[$i]);
			}

			$MOD['SHOP_PAYER_ELEMENT']->Add($nID, $arPar);
			$MOD['SHOP_ADDRESS']->Add($nID, $arPar);
			$MOD['SHOP_CART']->Clear();

			return $nID;
		}

		return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $MOD, $DB, $USER;

		if (!$arOrder = $MOD['SHOP_ORDER']->ID($nID)) {
			$this->Error = $MOD['SHOP_ORDER']->Error;

			return false;
		}

		if ($DB->Query("UPDATE `k2_mod_shop_order`
        SET
			`DATE_CHANGE` = NOW(),
			`USER_CHANGE` = '".$USER['ID']."',
			`STATUS` = '".(int)$arPar['STATUS']."'
        WHERE
        	`ID` = '".$nID."';
        ")
		) {
			return true;
		}

		return false;
	}

	function Delete($nID)
	{
		global $MOD, $DB;

		if (!$arOrder = $MOD['SHOP_ORDER']->ID($nID)) {
			$this->Error = $MOD['SHOP_ORDER']->Error;

			return false;
		}

		$arProduct = $MOD['SHOP_ORDER_PRODUCT']->Rows(array('ORDER' => $nID));
		for ($i = 0; $i < count($arProduct); $i++) {
			$MOD['SHOP_ORDER_PRODUCT']->Delete($arProduct[$i]['ID']);
		}

		$arPayerElement = $MOD['SHOP_PAYER_ELEMENT']->Rows($arOrder['PAYER'], array('ORDER' => $arOrder['ID']));
		for ($i = 0; $i < count($arPayerElement); $i++) {
			$MOD['SHOP_PAYER_ELEMENT']->Delete($arPayerElement[$i]['ID'], $arOrder['PAYER']);
		}

		$arAddress = $MOD['SHOP_ADDRESS']->Rows(array('ORDER' => $arOrder['ID']));
		for ($i = 0; $i < count($arAddress); $i++) {
			$MOD['SHOP_ADDRESS']->Delete($arAddress[$i]['ID']);
		}

		$DB->Query("DELETE FROM `k2_mod_shop_order` WHERE ID = '".$nID."'");

		return true;
	}
}

?>