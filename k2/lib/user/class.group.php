<?

class UserGroup
{
	function Rows($bPermission = 0)
	{
		global $DB;
		$arGroup = $DB->Rows("SELECT * FROM `k2_user_group` ORDER BY `ID` ASC");
		if($bPermission){
			for($i = 0; $i < count($arGroup); $i++){
				$arGroup[$i]['PERMISSION_DEFAULT'] = unserialize($arGroup[$i]['PERMISSION_DEFAULT']);
				$arGroup[$i]['PERMISSION_SITE'] = unserialize($arGroup[$i]['PERMISSION_SITE']);
				$arGroup[$i]['PERMISSION_SECTION'] = unserialize($arGroup[$i]['PERMISSION_SECTION']);
			}
		}

		return $arGroup;
	}

	function Name($nID)
	{
		global $DB;
		if($arGroup = $DB->Row("SELECT `NAME` FROM `k2_user_group` WHERE `ID` = '".(int)$nID."'")){
			return $arGroup['NAME'];
		}

		return false;
	}

	function ID($nID)
	{
		global $LIB, $DB;
		if($arGroup = $DB->Row("SELECT * FROM `k2_user_group` WHERE `ID` = '".$nID."'")){

			$arGroup['PERMISSION_DEFAULT'] = unserialize($arGroup['PERMISSION_DEFAULT']);
			$arGroup['PERMISSION_SITE'] = unserialize($arGroup['PERMISSION_SITE']);
			$arGroup['PERMISSION_SECTION'] = unserialize($arGroup['PERMISSION_SECTION']);

			return $arGroup;
		}
		$this->Error = 'Группа не найдена';

		return false;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB, $USER;

		if($sError = formCheck(array('NAME' => 'Название'))){
			$this->Error = $sError;

			return false;
		}

		if($DB->Rows("SELECT 1 FROM `k2_user_group` WHERE `NAME` LIKE '".DBS($arPar['NAME'])."'")){
			$this->Error = 'Такая группа уже существует';

			return false;
		}

		if($nID = $DB->Insert("
		INSERT INTO `k2_user_group`(
			`NAME`,
			`PERMISSION_DEFAULT`,
			`PERMISSION_SITE`,
			`PERMISSION_SECTION`
		)VALUES(
			'".DBS($arPar['NAME'])."', '".DBS(serialize($arPar['PERMISSION_DEFAULT']))."', '".DBS(serialize($arPar['PERMISSION_SITE']))."', '".DBS(serialize($arPar['PERMISSION_SECTION']))."'
		);
		")
		){
			return $nID;
		}

		return false;
	}

	function Edit($nID, $arPar = array(), $bFull = 0)
	{
		global $LIB, $DB, $USER;

		if(!$arGroup = $this->ID($nID)){
			return false;
		}

		if(!$bFull){
			$arPar += $arGroup;
		}

		if($sError = formCheck(array('NAME' => 'Название'), $arPar)){
			$this->Error = $sError;

			return false;
		}

		if($DB->Rows("SELECT 1 FROM `k2_user_group` WHERE `NAME` LIKE '".DBS($arPar['NAME'])."' AND `ID` != '".(int)$nID."'")){
			$this->Error = 'Такая группа уже существует';

			return false;
		}

		if($DB->Query("UPDATE `k2_user_group`
        SET
			`NAME` = '".DBS($arPar['NAME'])."',
			`PERMISSION_DEFAULT` = '".DBS(serialize($arPar['PERMISSION_DEFAULT']))."',
			`PERMISSION_SITE` = '".DBS(serialize($arPar['PERMISSION_SITE']))."',
			`PERMISSION_SECTION` = '".DBS(serialize($arPar['PERMISSION_SECTION']))."'
        WHERE
        	`ID` = '".$nID."';
        ")
		){
			return $nID;
		}

		return false;
	}

	function Delete($nID)
	{
		global $LIB, $DB, $USER;
		if(!$arGroup = $this->ID($nID)){
			return false;
		}
		if($USER['USER_GROUP'] != 1){
			return;
		}
		if($DB->Row("SELECT 1 FROM `k2_user` WHERE `USER_GROUP` = '".$arGroup['ID']."' LIMIT 1")){
			$this->Error = 'Удаление невозможно. Сперва удалите всех пользователей из этой группы';

			return false;
		}

		if($DB->Query("DELETE FROM `k2_user_group` WHERE ID = '".$arGroup['ID']."'")){
			return true;
		}

		return false;
	}

	function Permission($nSection, $arPar)
	{
		global $LIB, $DB, $USER;

		if($USER['USER_GROUP'] != 1){
			return false;
		}

		foreach($arPar as $nGroup => $nPermission){
			if(!$nGroup){
				$DB->Query("UPDATE `k2_section` SET `PERMISSION` = '".(int)$nPermission."' WHERE `ID` = '".(int)$nSection."'");
				continue;
			}
			if(!$nPermission){
				continue;
			}
			$DB->Query("DELETE FROM `k2_section_permission` WHERE `USER_GROUP` = '".(int)$nGroup."' AND `SECTION` = '".(int)$nSection."'");
			$DB->Insert("
			INSERT INTO `k2_section_permission`(
				`USER_GROUP`,
				`SECTION`,
				`PERMISSION`
			)VALUES(
				'".(int)$nGroup."', '".(int)$nSection."', '".(int)$nPermission."'
			)");
		}

		return true;
	}
}

?>