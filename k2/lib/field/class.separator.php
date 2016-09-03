<?

class FieldSeparator
{
	function Rows($sTable, $nObj = 0)
	{
		global $DB;

		return $DB->Rows("SELECT * FROM `k2_field_separator` WHERE `TABLE` = '".DBS($sTable)."' ORDER BY `SORT` ASC");
	}

	function ID($nID)
	{
		global $DB;
		if ($arGroup = $DB->Row("SELECT * FROM `k2_field_separator` WHERE `ID` = '".(int)$nID."'")) {
			return $arGroup;
		}
		$this->Error = 'Разделитель не найден';

		return false;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;
		if (empty($arPar['NAME'])) {
			$this->Error = changeMessage('Название');

			return false;
		}
		if (!isset($arPar['SORT'])) {
			if ($arGroup = $DB->Rows("SELECT `SORT` FROM `k2_field_separator` WHERE `OBJECT` = '".(int)$arPar['OBJECT']."' AND `TABLE` = '".DBS($arPar['TABLE'])."' ORDER BY `SORT` DESC LIMIT 1")) {
				$arPar['SORT'] = $arGroup[0]['SORT'];
			}
			if ($arField = $DB->Rows("SELECT `SORT` FROM `k2_field` WHERE `OBJECT` = '".(int)$arPar['OBJECT']."' AND `TABLE` = '".DBS($arPar['TABLE'])."' ORDER BY `SORT` DESC LIMIT 1")) {
				if ($arField[0]['SORT'] > $arPar['SORT']) {
					$arPar['SORT'] = $arField[0]['SORT'];
				}
			}
			$arPar['SORT'] += 10;
		}
		if ($nID = $DB->Insert("
		INSERT INTO `k2_field_separator`(
			`OBJECT`,
			`TABLE`,
			`SORT`,
			`NAME`
		)VALUES(
			'".(int)$arPar['OBJECT']."', '".DBS($arPar['TABLE'])."', '".(int)$arPar['SORT']."', '".DBS($arPar['NAME'])."'
		);
		")
		) {
			return $nID;
		}

		return false;
	}

	function Edit($nID, $arPar = array(), $bFull = 0)
	{
		global $LIB, $DB;
		if (!$arGroup = $this->ID($nID)) {
			return false;
		}
		if (!$bFull) {
			$arPar += $arGroup;
		}
		if (empty($arPar['NAME'])) {
			$this->Error = changeMessage('Название');

			return false;
		}
		if ($DB->Query("UPDATE `k2_field_separator`
        SET
			`NAME` = '".DBS($arPar['NAME'])."',
			`SORT` = '".(int)$arPar['SORT']."'
        WHERE
        	`ID` = '".$nID."';
        ")
		) {
			return $nID;
		}

		return false;
	}

	function Delete($nID)
	{
		global $DB;
		$DB->Query("DELETE FROM `k2_field_separator` WHERE ID = '".$nID."'");

		return true;
	}
}

?>