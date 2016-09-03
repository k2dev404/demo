<?

class ShopPayerElement
{
	function ID($nID, $nPayer)
	{
		global $DB, $MOD;
		if (!$arPayer = $MOD['SHOP_PAYER']->ID($nPayer)) {
			$this->Error = $MOD['SHOP_PAYER']->Error;

			return false;
		}
		if ($arElement = $DB->Row("SELECT * FROM `k2_mod_shop_payer".$arPayer['ID']."` WHERE `ID` = '".(int)$nID."'")) {
			return $arElement;
		}
		$this->Error = 'Элемент не найден';

		return false;
	}

	function Rows($nPayer, $arFilter = array(), $arOrderBy = array(), $arSelect = array(), $nSize = 0, $nLimit = 0)
	{
		global $LIB, $DB;

		$nPayer = (int)$nPayer;

		if ($nSize) {
			$LIB['NAV']->Setting['SIZE'] = $nSize;
			$LIB['NAV']->Setting['TOTAL'] = 0;
		}

		if ($nLimit) {
			$LIB['NAV']->Setting = array();
		}

		$arCFilter = array('FROM' => 'k2_mod_shop_payer'.$nPayer, 'WHERE' => $arFilter, 'ORDER_BY' => $arOrderBy, 'SELECT' => $arSelect, 'SIZE' => $nSize, 'LIMIT' => $nLimit);

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
		global $MOD, $DB, $LIB;

		if (!$arOrder = $MOD['SHOP_ORDER']->ID($nOrder)) {
			$this->Error = $MOD['SHOP_ORDER']->Error;

			return false;
		}

		if (!$arPayer = $MOD['SHOP_PAYER']->ID($arOrder['PAYER'])) {
			$this->Error = $MOD['SHOP_PAYER']->Error;

			return false;
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2_mod_shop_payer'.$arPayer['ID'], $arPar)) {
			$this->Error = $sError;

			return false;
		}
		if ($nID = $DB->Insert("INSERT INTO `k2_mod_shop_payer".$arPayer['ID']."` (`ORDER`) VALUES ('".$arOrder['ID']."')")) {
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_mod_shop_payer'.$arPayer['ID']), $arPar);

			return $nID;
		}

		return false;
	}

	function Delete($nID, $nPayer)
	{
		global $DB, $MOD;

		if (!$arPayer = $MOD['SHOP_PAYER']->ID($nPayer)) {
			$this->Error = $MOD['SHOP_PAYER']->Error;

			return false;
		}

		$DB->Query("DELETE FROM `k2_mod_shop_payer".$nPayer."` WHERE `ID` = '".(int)$nID."'");

		return true;
	}
}

?>