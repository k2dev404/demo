<?

class BlockGroup
{
	function Rows()
	{
		global $DB;
		$arRows = $DB->Rows("SELECT * FROM `k2_block_group` ORDER BY `ID` ASC");

		return $arRows;
	}

	function ID($nID)
	{
		global $LIB, $DB;
		if ($arGroup = $DB->Row("SELECT * FROM `k2_block_group` WHERE `ID` = '".$nID."'")) {
			return $arGroup;
		}
		$this->Error = 'Группа не найдена';

		return false;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;
		if ($sError = formCheck(array('NAME' => 'Название'))) {
			$this->Error = $sError;

			return false;
		}

		if ($DB->Row("SELECT * FROM `k2_block_group` WHERE `NAME` = '".DBS($arPar['NAME'])."'")) {
			$this->Error = 'Такая группа уже существует';

			return false;
		}

		if ($nID = $DB->Insert("
		INSERT INTO `k2_block_group` (
			`NAME`
		)VALUES(
			'".DBS($arPar['NAME'])."'
		);
		")
		) {
			return $nID;
		}

		return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB;
		if (!$arGroup = $this->ID($nID)) {
			return false;
		}

		if ($sError = formCheck(array('NAME' => 'Название'))) {
			$this->Error = $sError;

			return false;
		}

		if ($DB->Row("SELECT * FROM `k2_block_group` WHERE `NAME` = '".DBS($arPar['NAME'])."' AND `ID` != '".$nID."'")) {
			$this->Error = 'Такая группа уже существует';

			return false;
		}

		if ($DB->Query("UPDATE k2_block_group
        SET
			`NAME` = '".DBS($arPar['NAME'])."'
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
		global $LIB, $DB;

		if (!$arGroup = $this->ID($nID)) {
			return false;
		}
		if ($LIB['BLOCK']->Rows($nID)) {
			$this->Error = 'Удаление невозможно. Сперва удалите все функциональные блоки из этой группы';

			return false;
		}
		$DB->Query("DELETE FROM `k2_block_group` WHERE `ID` = '".$nID."'");

		return true;
	}
}

?>