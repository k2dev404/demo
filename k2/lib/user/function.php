<?
function userLogin($nID)
{
	global $DB;
	if ($arUser = $DB->Row("SELECT `LOGIN` FROM `k2_user` WHERE `ID` = '".(int)$nID."'")) {
		return $arUser['LOGIN'];
	}
}

function groupName($nID)
{
	global $DB;
	if ($arGroup = $DB->Row("SELECT `NAME` FROM `k2_user_group` WHERE `ID` = '".(int)$nID."'")) {
		return $arGroup['NAME'];
	}
}

?>
