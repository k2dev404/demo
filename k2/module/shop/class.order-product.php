<?

class ShopOrderProduct
{
	function ID($nID)
	{
		global $LIB, $DB;
		if ($arPayment = $DB->Row("SELECT * FROM `k2_mod_shop_order_product` WHERE `ID` = '".$nID."'")) {
			return $arPayment;
		}
		$this->Error = 'Продукт не найден';

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

		$arCFilter = array('FROM' => 'k2_mod_shop_order_product', 'WHERE' => $arFilter, 'ORDER_BY' => $arOrderBy, 'SELECT' => $arSelect, 'SIZE' => $nSize, 'LIMIT' => $nLimit);

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

	function Add($nOrder, $arPar = array())
	{
		global $DB, $MOD;

		if (!$arOrder = $MOD['SHOP_ORDER']->ID($nOrder)) {
			$this->Error = $MOD['SHOP_ORDER']->Error;

			return false;
		}

		if ($sError = formCheck(array(
			'ARTICLE' => 'Артикул', 'NAME' => 'Название', 'PRICE' => 'Цена', 'QUANTITY' => 'Количество'), $arPar)
		) {
			$this->Error = $sError;

			return false;
		}

		if ($nID = $DB->Insert("
			INSERT INTO `k2_mod_shop_order_product`(
				`ORDER`,
				`ARTICLE`,
				`NAME`,
				`PRICE`,
				`QUANTITY`,
				`DATA`
			)VALUES(
				'".$arOrder['ID']."', '".DBS($arPar['ARTICLE'])."', '".DBS($arPar['NAME'])."', '".DBS($arPar['PRICE'])."', '".(int)$arPar['QUANTITY']."', '".DBS(serialize($arPar['DATA']))."'
			);")
		) {
			return $nID;
		}

		return false;
	}

	function Delete($nID)
	{
		global $LIB, $DB;

		$DB->Query("DELETE FROM `k2_mod_shop_order_product` WHERE ID = '".$nID."'");

		return true;
	}
}

?>