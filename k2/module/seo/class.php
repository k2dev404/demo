<?
class Seo
{
	function Seo()
	{
		global $DB;

		$arRows = $DB->Rows("SELECT * FROM `k2_mod_seo_setting`");
		for($i=0; $i<count($arRows); $i++)
		{
			$arSetting[$arRows[$i]['TYPE']] = $arRows[$i]['SETTING'];
		}
		$this->Setting = $arSetting;
	}

	function Start()
	{
		global $CURRENT, $LIB, $MOD;

		if($this->Setting['REDIRECT_ACTIVE']){
			$MOD['SEO_REDIRECT']->Set();
		}

		$arType = array('SECTION', 'CATEGORY', 'ELEMENT');
		$arField = array('TITLE', 'KEYWORD', 'DESCRIPTION');
		foreach($arType as $sType)
		{
			if($CURRENT[$sType]['NAME']){
				$arSeo['TITLE'] = $CURRENT[$sType]['NAME'];
				Delayed('H1', $CURRENT[$sType]['NAME']);
			}
			foreach($arField as $sTag)
			{
				if($CURRENT[$sType]['SEO_'.$sTag]){
					$arSeo[$sTag] = $CURRENT[$sType]['SEO_'.$sTag];
				}
			}
		}

		Delayed('TITLE', $arSeo['TITLE']);
		Delayed('KEYWORD', $arSeo['KEYWORD']);
		Delayed('DESCRIPTION', $arSeo['DESCRIPTION']);
	}

	function End()
	{
    	global $MOD;

    	if($this->Setting['PAGE_ACTIVE']){
			$MOD['SEO_PAGE']->Set();
		}
	}
}
?>