<?
class ShopPayer
{
	function ID($nID)
	{
		global $LIB, $DB;
		if($arPayer = $DB->Row("SELECT * FROM `k2_mod_shop_payer` WHERE `ID` = '".$nID."'")){
			return $arPayer;
        }
        $this->Error = 'Плательщик не найден';
		return false;
	}

	function Rows()
	{
    	global $DB;
    	$arPayer = $DB->Rows("SELECT * FROM `k2_mod_shop_payer` ORDER BY `ID` ASC");
		return $arPayer;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB, $USER;

		if($sError = formCheck(array('NAME' => 'Название'))){
       		$this->Error = $sError;
			return false;
        }

        if(($nID = $DB->Insert("
		INSERT INTO `k2_mod_shop_payer` (
			`NAME`,
			`ACTIVE`,
			`SORT`
		)VALUES(
			'".DBS($arPar['NAME'])."', '".(int)$arPar['ACTIVE']."', '".(int)$arPar['SORT']."'
		);")) && $DB->Query("CREATE TABLE `k2_mod_shop_payer".$nID."` (
			`ID` int(11) NOT NULL AUTO_INCREMENT,
			`SHOP_ORDER` int(11) NOT NULL,
			PRIMARY KEY (`ID`),
			KEY `SHOP_ORDER` (`SHOP_ORDER`)
			)ENGINE=InnoDB DEFAULT CHARSET=utf8;")
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

        if($DB->Query("UPDATE `k2_mod_shop_payer`
        SET
			`NAME` = '".DBS($arPar['NAME'])."',
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`SORT` = '".(int)$arPar['SORT']."'
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

		$DB->Query("DELETE FROM `k2_mod_shop_payer` WHERE ID = '".(int)$nID."'");
		$DB->Query("DROP TABLE `k2_mod_shop_payer".(int)$nID."`");

		return true;
	}
}
?>