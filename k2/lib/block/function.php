<?
function blockName($nID)
{
	global $DB;
	if ($arRow = $DB->Row("SELECT `NAME` FROM `k2_block` WHERE `ID` = '".(int)$nID."'")) {
		return $arRow['NAME'];
	}

	return false;
}

?>