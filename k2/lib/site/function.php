<?
function siteName($nID)
{
	global $DB;
	if ($arRow = $DB->Rows("SELECT NAME, DOMAIN FROM `k2_site` WHERE ID = '".(int)$nID."'")) {
		return ($arRows['DOMAIN'] ? $arRows['DOMAIN'] : $arRows['NAME']);
	}

	return false;
}

?>