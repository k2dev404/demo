<?
class ShopDelivery
{
	function ID($nID)
	{
		global $LIB, $DB;
		if($arPayment = $DB->Row("SELECT * FROM `k2_mod_shop_delivery` WHERE `ID` = '".$nID."'")){
			return $arPayment;
        }
        $this->Error = 'Доставка не найдена';
		return false;
	}

	function Rows()
	{
    	global $DB;
    	return $DB->Rows("SELECT * FROM `k2_mod_shop_delivery` ORDER BY `ID` ASC");
	}

	function Add($arPar = array())
	{
		global $LIB, $DB, $USER;

		if($sError = formCheck(array('NAME' => 'Название'))){
       		$this->Error = $sError;
			return false;
        }

        if($nID = $DB->Insert("
		INSERT INTO `k2_mod_shop_delivery` (
			`ACTIVE`,
			`NAME`,
			`TEXT`,
			`PRICE`
		)VALUES(
			'".(int)$arPar['ACTIVE']."', '".DBS($arPar['NAME'])."', '".DBS($arPar['TEXT'])."', '".DBS($arPar['PRICE'])."'
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

        if($DB->Query("UPDATE `k2_mod_shop_delivery`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`NAME` = '".DBS($arPar['NAME'])."',
			`TEXT` = '".DBS($arPar['TEXT'])."',
			`PRICE` = '".DBS($arPar['PRICE'])."'
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

		$DB->Query("DELETE FROM `k2_mod_shop_delivery` WHERE ID = '".$nID."'");

		return true;
	}
}
?>