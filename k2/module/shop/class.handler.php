<?
class ShopHandler
{
	function ID($nHandler)
	{
		global $LIB, $DB;
		if($arHandler = $DB->Row("SELECT * FROM `k2_mod_shop_handler` WHERE `HANDLER` = '".DBS($nHandler)."'")){
			$arHandler['FIELD'] = unserialize($arHandler['FIELD']);
			$arHandler['DATA'] = unserialize($arHandler['DATA']);
			return $arHandler;
        }
        $this->Error = 'Обработчик не найден';
		return false;
	}

	function Rows()
	{
    	global $DB;
    	$arHandler = $DB->Rows("SELECT * FROM `k2_mod_shop_handler` ORDER BY `NAME` ASC");
		return $arHandler;
	}

	function Edit($sHandler, $arPar = array())
	{
		global $LIB, $DB, $USER;

        if(!$arHandler = $this->ID($sHandler)){
        	return false;
        }

        if($DB->Query("UPDATE `k2_mod_shop_handler`
        SET
			`DATA` = '".DBS(serialize($arPar['DATA']))."'
        WHERE
        	`HANDLER` = '".DBS($sHandler)."';
        ")){
	        return true;
        }
    	return false;
	}

	function Delete($nHandler)
	{
    	global $LIB, $DB;

		$DB->Query("DELETE FROM `k2_mod_shop_handler` WHERE `HANDLER` = '".$nHandler."'");

		return true;
	}
}
?>