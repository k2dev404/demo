<?

class Select
{
	function Rows()
	{
		global $DB;
		$arRows = $DB->Rows("SELECT * FROM `k2_select`");

		return $arRows;
	}

	function ID($nID)
	{
		global $LIB, $DB;
		if ($arSelect = $DB->Row("SELECT * FROM `k2_select` WHERE ID = '".(int)$nID."'")) {
			if ($arSelectOption = $DB->Rows("SELECT * FROM `k2_select_option` WHERE `SELECT` = '".(int)$nID."' ORDER BY ".$arSelect['FIELD_SORT']." ".$arSelect['METHOD_SORT'])) {
				$arSelect['OPTION'] = $arSelectOption;
			}

			return $arSelect;
		}
		$this->Error = 'Элемент не найден';

		return false;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;
		if (empty($arPar['NAME'])) {
			$this->Error = changeMessage('Название');

			return false;
		}
		if ($DB->Rows("SELECT * FROM `k2_select` WHERE `NAME` LIKE '".DBS($arPar['NAME'])."'")) {
			$this->Error = 'Список с таким названием уже есть';

			return false;
		}
		if (!in_array($arPar['FIELD_SORT'], array('ID', 'NAME'))) {
			$arPar['FIELD_SORT'] = 'ID';
		}
		if (!in_array($arPar['METHOD_SORT'], array('ASC', 'DESC'))) {
			$arPar['METHOD_SORT'] = 'ASC';
		}
		if ($nID = $DB->Insert("INSERT
		INTO
			`k2_select` (`NAME`, `FIELD_SORT`, `METHOD_SORT`)
		VALUES
			('".DBS($arPar['NAME'])."', '".$arPar['FIELD_SORT']."', '".$arPar['METHOD_SORT']."'
		);")
		) {
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
		$arSelect += $arPar;
		if (empty($arPar['NAME'])) {
			$this->Error = changeMessage('Название');

			return false;
		}
		if ($DB->Rows("SELECT * FROM `k2_select` WHERE `NAME` LIKE '".DBS($arPar['NAME'])."' AND ID != '".$nID."'")) {
			$this->Error = 'Список с таким названием уже есть';

			return false;
		}
		if (!in_array($arPar['FIELD_SORT'], array('ID', 'NAME'))) {
			$arPar['FIELD_SORT'] = 'ID';
		}
		if (!in_array($arPar['METHOD_SORT'], array('ASC', 'DESC'))) {
			$arPar['METHOD_SORT'] = 'ASC';
		}
		if ($DB->Query("UPDATE
			`k2_select`
		SET
			`NAME` = '".DBS($arPar['NAME'])."',
			`FIELD_SORT` = '".$arPar['FIELD_SORT']."',
			`METHOD_SORT` = '".$arPar['METHOD_SORT']."'
		WHERE
			`ID` = '".(int)$nID."'")
		) {
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
		if ($DB->Query("DELETE FROM `k2_select_option` WHERE `SELECT` = '".$nID."'") && $DB->Query("DELETE FROM `k2_select` WHERE ID = '".$nID."'")) {
			return true;
		}

		return false;
	}
}

?>