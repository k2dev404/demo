<?
class ShopPayment
{
	function ID($nID)
	{
		global $LIB, $DB;
		if($arPayment = $DB->Row("SELECT * FROM `k2_mod_shop_payment` WHERE `ID` = '".$nID."'")){
			return $arPayment;
        }
        $this->Error = 'Способ оплаты не найден';
		return false;
	}

	function Rows()
	{
    	global $DB;
    	return $DB->Rows("SELECT * FROM `k2_mod_shop_payment` ORDER BY `ID` ASC");
	}

	function Add($arPar = array())
	{
		global $LIB, $DB, $USER;

		if($sError = formCheck(array('NAME' => 'Название'))){
       		$this->Error = $sError;
			return false;
        }

        if($nID = $DB->Insert("
		INSERT INTO `k2_mod_shop_payment` (
			`ACTIVE`,
			`NAME`,
			`TEXT`
		)VALUES(
			'".(int)$arPar['ACTIVE']."', '".DBS($arPar['NAME'])."', '".DBS($arPar['TEXT'])."'
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

        if($DB->Query("UPDATE `k2_mod_shop_payment`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`NAME` = '".DBS($arPar['NAME'])."',
			`TEXT` = '".DBS($arPar['TEXT'])."'
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

		$DB->Query("DELETE FROM `k2_mod_shop_payment` WHERE ID = '".$nID."'");

		return true;
	}
}
?>