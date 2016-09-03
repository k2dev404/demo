<?
class SeoRedirect
{
	function ID($nID)
	{
		global $LIB, $DB;
		if($arPayer = $DB->Row("SELECT * FROM `k2_mod_seo_redirect` WHERE `ID` = '".$nID."'")){
			return $arPayer;
        }
        $this->Error = 'Страница не найдена';
		return false;
	}

	function Rows()
	{
    	global $DB;
    	$arRows = $DB->Rows("SELECT * FROM `k2_mod_seo_redirect` ORDER BY `ID` DESC");
		return $arRows;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;

		if($sError = formCheck(array('PATH' => 'Путь', 'REDIRECT' => 'Перенаправить'))){
       		$this->Error = $sError;
			return false;
        }

        if(preg_match("#http://.*?(/.+)$#", $arPar['PATH'], $arMath)){
        	$arPar['PATH'] = $arMath[1];
        }
        if(preg_match("#http://.*?(/.+)$#", $arPar['REDIRECT'], $arMath)){
        	$arPar['REDIRECT'] = $arMath[1];
        }

        if($nID = $DB->Insert("
		INSERT INTO `k2_mod_seo_redirect` (
			`PATH`,
			`REDIRECT`
		)VALUES(
			'".DBS($arPar['PATH'])."', '".DBS($arPar['REDIRECT'])."'
		);")){

	        $this->ReloadActive();

        	return $nID;
		}
    	return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB;

        if(!$arRedirect = $this->ID($nID)){
        	return false;
        }

		if($sError = formCheck(array('PATH' => 'Путь', 'REDIRECT' => 'Перенаправить'))){
       		$this->Error = $sError;
			return false;
        }

        if(preg_match("#http://.*?(/.+)$#", $arPar['PATH'], $arMath)){
        	$arPar['PATH'] = $arMath[1];
        }
        if(preg_match("#http://.*?(/.+)$#", $arPar['REDIRECT'], $arMath)){
        	$arPar['REDIRECT'] = $arMath[1];
        }

        if($DB->Query("UPDATE `k2_mod_seo_redirect`
        SET
			`PATH` = '".DBS($arPar['PATH'])."',
			`REDIRECT` = '".DBS($arPar['REDIRECT'])."'
        WHERE
        	`ID` = '".$nID."';
        ")){

	        $this->ReloadActive();

	        return true;
        }

    	return false;
	}

	function Delete($nID)
	{
    	global $DB;

		$DB->Query("DELETE FROM `k2_mod_seo_redirect` WHERE ID = '".(int)$nID."'");

		$this->ReloadActive();

		return true;
	}

	function Set()
	{
    	global $DB, $LIB;

    	if($arRow = $DB->Row("SELECT `REDIRECT` FROM `k2_mod_seo_redirect` WHERE `PATH` = '".DBS($_SERVER['REQUEST_URI'])."'")){
    		Redirect($arRow['REDIRECT']);
    	}
	}

	function ReloadActive()
	{
		global $DB;

		$nActive = (int)(count($this->Rows()) > 0);

		$DB->Query("UPDATE `k2_mod_seo_setting` SET `SETTING` = '".$nActive."' WHERE TYPE = 'REDIRECT_ACTIVE'");
	}
}
?>