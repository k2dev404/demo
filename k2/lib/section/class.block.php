<?

class SectionBlock
{
	function Rows($nSection)
	{
		global $DB;
		$arRows = $DB->Rows("SELECT * FROM `k2_section_block` WHERE `SECTION` = '".(int)$nSection."' ORDER BY `SORT` ASC");

		return $arRows;
	}

	function ID($nID)
	{
		global $DB;
		if ($arSectionBlock = $DB->Row("SELECT * FROM `k2_section_block` WHERE `ID` = '".(int)$nID."'")) {
			return $arSectionBlock;
		}
		$this->Error = 'Страница не найдена';

		return false;
	}

	function Add($nSectionID, $arPar = array())
	{
		global $LIB, $DB;

		if (!$LIB['SECTION']->ID($nSectionID)) {
			$this->Error = $LIB['SECTION']->Error;

			return false;
		}

		if (!$LIB['BLOCK']->ID($arPar['BLOCK'])) {
			$this->Error = $LIB['SECTION']->Error;

			return false;
		}

		if ($sError = formCheck(array('NAME' => 'Название'), $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if (!isset($arPar['SORT'])) {
			if ($arBlock = $DB->Rows("SELECT SORT FROM `k2_section_block` WHERE `SECTION` = '".$nSectionID."' ORDER BY `SORT` DESC LIMIT 1")) {
				$arPar['SORT'] = $arBlock[0]['SORT'] + 10;
			}
		}

		if ($nID = $DB->Insert("
			INSERT INTO `k2_section_block`(
				`SECTION`,
				`BLOCK`,
				`ACTIVE`,
				`NAME`,
				`SORT`
			)VALUES(
				'".$nSectionID."', '".$arPar['BLOCK']."', '".(int)$arPar['ACTIVE']."', '".DBS($arPar['NAME'])."', '".(int)$arPar['SORT']."'
		)")
		) {
			mkdir($_SERVER['DOCUMENT_ROOT'].'/files/section/'.$nSectionID.'/'.$nID);
			mkdir($_SERVER['DOCUMENT_ROOT'].'/k2/admin/files/section/'.$nSectionID.'/'.$nID);

			return true;
		}

		return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB;

		if (!$arSectionBlock = $this->ID($nID)) {
			return false;
		}

		$arPar += $arSectionBlock;

		if ($sError = formCheck(array('NAME' => 'Название'), $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($DB->Query("UPDATE
        	`k2_section_block`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`NAME` = '".DBS($arPar['NAME'])."',
			`SORT` = '".(int)$arPar['SORT']."'
        WHERE
        	ID = '".$nID."';
        ")
		) {
			return $nID;
		}

		return false;
	}

	function Delete($nID)
	{
		global $DB, $LIB;
		if ($arSBlock = $this->ID($nID)) {

			$arContent = $DB->Rows("SELECT ID FROM `k2_block".$arSBlock['BLOCK']."` WHERE `SECTION_BLOCK` = '".$nID."'");
			for ($i = 0; $i < count($arContent); $i++) {
				$LIB['FIELD']->DeleteContent(array('TABLE' => 'k2_block'.$arSBlock['BLOCK'], 'ELEMENT' => $arContent[$i]['ID']));
			}
			$arContent = $DB->Rows("SELECT ID FROM `k2_block".$arSBlock['BLOCK']."category` WHERE `SECTION_BLOCK` = '".$nID."'");
			for ($i = 0; $i < count($arContent); $i++) {
				$LIB['FIELD']->DeleteContent(array('TABLE' => 'k2_block'.$arSBlock['BLOCK'].'category', 'ELEMENT' => $arContent[$i]['ID']));
			}

			$DB->Query("DELETE FROM `k2_block".$arSBlock['BLOCK']."` WHERE `SECTION_BLOCK` = '".$nID."'");
			$DB->Query("DELETE FROM `k2_section_block` WHERE `ID` = '".$nID."'");
			rmdir($_SERVER['DOCUMENT_ROOT'].'/k2/files/section/'.$arSBlock['SECTION'].'/'.$nID);
		}

		return true;
	}
}

?>