<?
class SeoPage
{
	function ID($nID)
	{
		global $DB;
		if($arPayer = $DB->Row("SELECT * FROM `k2_mod_seo_page` WHERE `ID` = '".$nID."'")){
			return $arPayer;
        }
        $this->Error = 'Страница не найдена';
		return false;
	}

	function Rows()
	{
    	global $DB;

    	$arRows = $DB->Rows("SELECT * FROM `k2_mod_seo_page` ORDER BY `ID` DESC");

		return $arRows;
	}

	function Add($arPar = array())
	{
		global $DB;

		if($sError = formCheck(array('PAGE' => 'Путь'))){
       		$this->Error = $sError;
			return false;
        }

        if(preg_match("#http://.*?(/.+)$#", $arPar['PAGE'], $arMath)){
        	$arPar['PAGE'] = $arMath[1];
        }

        if($nID = $DB->Insert("
		INSERT INTO `k2_mod_seo_page` (
			`PAGE`,
			`TITLE`,
			`H1`,
			`KEYWORD`,
			`DESCRIPTION`,
			`TEXT`
		)VALUES(
			'".DBS($arPar['PAGE'])."', '".DBS($arPar['TITLE'])."', '".DBS($arPar['H1'])."', '".DBS($arPar['KEYWORD'])."', '".DBS($arPar['DESCRIPTION'])."', '".DBS($arPar['TEXT'])."'
		);")){

	        $this->ReloadActive();

        	return $nID;
		}
    	return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB;

        if(!$arEmail = $this->ID($nID)){
        	return false;
        }

        if($sError = formCheck(array('PAGE' => 'Путь'))){
       		$this->Error = $sError;
			return false;
        }

        if(preg_match("#http://.*?(/.+)$#", $arPar['PAGE'], $arMath)){
        	$arPar['PAGE'] = $arMath[1];
        }

        if($DB->Query("UPDATE `k2_mod_seo_page`
        SET
			`PAGE` = '".DBS($arPar['PAGE'])."',
			`TITLE` = '".DBS($arPar['TITLE'])."',
			`H1` = '".DBS($arPar['H1'])."',
			`KEYWORD` = '".DBS($arPar['KEYWORD'])."',
			`DESCRIPTION` = '".DBS($arPar['DESCRIPTION'])."',
			`TEXT` = '".DBS($arPar['TEXT'])."'
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

		$DB->Query("DELETE FROM `k2_mod_seo_page` WHERE ID = '".(int)$nID."'");

		$this->ReloadActive();

		return true;
	}

	function Set()
	{
    	global $DB, $LIB, $DELAYED_VARIABLE, $MOD;

    	$arField = array('TITLE', 'H1', 'KEYWORD', 'DESCRIPTION', 'TEXT');

    	$arRows = $DB->Rows("SELECT * FROM `k2_mod_seo_page` ORDER BY ID DESC");
		for($i=0; $i<count($arRows); $i++)
		{
        	$arSeo = false;
        	if(strpos($arRows[$i]['PAGE'], '*') !== false){
        		$sPage = str_replace('*', '', $arRows[$i]['PAGE']);
        		if(strpos($_SERVER['REQUEST_URI'], $sPage) !== false){
	        		$arSeo = $arRows[$i];
	        	}
        	}else{
        		if($_SERVER['REQUEST_URI'] == $arRows[$i]['PAGE']){
	        		$arSeo = $arRows[$i];
	        	}
        	}
            if($arSeo){
	            foreach($arField as $sField)
	        	{
		        	$arSeo[$sField] = str_replace('%MASK%', $DELAYED_VARIABLE[$sField], $arSeo[$sField]);
		        	if($arSeo[$sField]){
		        		Delayed($sField, $arSeo[$sField]);
		        	}
	        	}
	        	return true;
            }
		}

		return false;
	}

	function ReloadActive()
	{
		global $DB;

		$nActive = (int)(count($this->Rows()) > 0);

		$DB->Query("UPDATE `k2_mod_seo_setting` SET `SETTING` = '".$nActive."' WHERE TYPE = 'PAGE_ACTIVE'");
	}
}
?>