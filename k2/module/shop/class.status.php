<?
class ShopStatus
{
	function ID($nID)
	{
		global $LIB, $DB;
		if($arPayer = $DB->Row("SELECT * FROM `k2_mod_shop_status` WHERE `ID` = '".$nID."'")){
			return $arPayer;
		}
		$this->Error = 'Статус не найден';
		return false;
	}

	function Rows()
	{
		global $DB;
		$arPayer = $DB->Rows("SELECT * FROM `k2_mod_shop_status` ORDER BY `ID` ASC");
		return $arPayer;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB, $USER;

		if($sError = formCheck(array('NAME' => 'Название'))){
			$this->Error = $sError;
			return false;
		}

		if($nID = $DB->Insert("
		INSERT INTO `k2_mod_shop_status` (
			`NAME`
		)VALUES(
			'".DBS($arPar['NAME'])."'
		);")
		){
			return $nID;
		}
		return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB, $USER;

		if(!$arPayer = $this->ID($nID)){
			return false;
		}

		if($sError = formCheck(array('NAME' => 'Название'))){
			$this->Error = $sError;
			return false;
		}

		if($DB->Query("UPDATE `k2_mod_shop_status`
		SET
			`NAME` = '".DBS($arPar['NAME'])."'
		WHERE
			`ID` = '".$nID."';
		")){
			return true;
		}

		return false;
	}

	function Delete($nID)
	{
		global $LIB, $DB;

		$DB->Query("DELETE FROM `k2_mod_shop_status` WHERE ID = '".$nID."'");

		return true;
	}
}
?>