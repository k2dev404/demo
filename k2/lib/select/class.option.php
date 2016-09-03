<?

class SelectOption
{
	function ID($nID)
	{
		global $LIB, $DB;
		if ($arRow = $DB->Row("SELECT * FROM `k2_select_option` WHERE ID = '".(int)$nID."'")) {
			return $arRow;
		}
		$this->Error = '������� �� ������';

		return false;
	}

	function Add($nID, $arPar = array())
	{
		global $LIB, $DB, $USER;
		if (!$arSelect = $LIB['SELECT']->ID($nID)) {
			return false;
		}
		if (empty($arPar['NAME'])) {
			$this->Error = changeMessage('��������');

			return false;
		}
		if ($nID = $DB->Insert("INSERT INTO `k2_select_option` (`SELECT`, `NAME`) VALUES ('".(int)$nID."', '".DBS($arPar['NAME'])."');")) {
			return $nID;
		}

		return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB;
		if (!$arSelect = $this->ID($nID)) {
			return false;
		}
		if (empty($arPar['NAME'])) {
			$this->Error = changeMessage('��������');

			return false;
		}
		if ($DB->Query("UPDATE `k2_select_option` SET `NAME` = '".DBS($arPar['NAME'])."' WHERE `ID` = '".(int)$nID."'")) {
			return true;
		}

		return false;
	}

	function Delete($nID)
	{
		global $LIB, $DB;
		if (!$arField = $this->ID($nID)) {
			return false;
		}
		if ($DB->Query("DELETE FROM `k2_select_option` WHERE ID = '".$nID."'")) {
			return true;
		}

		return false;
	}
}

?>