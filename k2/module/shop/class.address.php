<?

class ShopAddress
{
	function ID($nID)
	{
		global $DB;

		if ($arElement = $DB->Row("SELECT * FROM `k2_mod_shop_address` WHERE `ID` = '".(int)$nID."'")) {
			return $arElement;
		}
		$this->Error = 'Адрес не найден';

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

		$arCFilter = array('FROM' => 'k2_mod_shop_address', 'WHERE' => $arFilter, 'ORDER_BY' => $arOrderBy, 'SELECT' => $arSelect, 'SIZE' => $nSize, 'LIMIT' => $nLimit);

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

		if (!$LIB['FIELD']->Rows('k2_mod_shop_address')) {
			return true;
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2_mod_shop_address', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($nID = $DB->Insert("INSERT INTO `k2_mod_shop_address` (`ORDER`) VALUES ('".$arOrder['ID']."')")) {
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_mod_shop_address'), $arPar);

			return $nID;
		}

		return false;
	}

	function Delete($nID)
	{
		global $DB;

		echo $nID;

		$DB->Query("DELETE FROM `k2_mod_shop_address` WHERE `ID` = '".(int)$nID."'");

		return true;
	}
}

?>