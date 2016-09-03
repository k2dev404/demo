<?

class SeoSitemap
{
	function __construct()
	{
		global $DB;

		foreach ($DB->Rows("SELECT * FROM `k2_mod_seo_sitemap`") as $arRow) {
			$this->DBURL[$arRow['URL']] = $arRow;
		}
	}

	function Rows()
	{
		global $DB;

		$arRows = $DB->Rows("SELECT * FROM `k2_mod_seo_sitemap` ORDER BY `ID` ASC");

		return $arRows;
	}

	function Clear()
	{
		global $DB;

		$DB->Query("TRUNCATE k2_mod_seo_sitemap");
		//unlink($_SERVER['DOCUMENT_ROOT'].$this->Setting['SITEMAP_FILENAME']);
	}

	function Start($sDomain, $arSetting = array())
	{
		global $DB;

		$this->Domain = $sDomain;
		$this->Setting = $arSetting;

		if (!count($this->DBURL)) {
			$this->Request(array($sDomain.'/'));
		}

		$arLink = array();
		$arRows = $DB->Rows("SELECT `URL` FROM `k2_mod_seo_sitemap` WHERE `COMPLITE` = 0 ORDER BY `ID` ASC LIMIT ".(int)$this->Setting['SITEMAP_MAXLINK']);
		for ($i = 0, $c = count($arRows); $i < $c; $i++) {
			$arLink[] = $arRows[$i]['URL'];
		}

		if ($arLink) {
			$this->Request($arLink);
			return 'next';
		}

		if ($this->Setting['SITEMAP_ROBOT']) {
			$this->Robot();
		}

		$this->Priority();
		$this->CreateFile();


		if ($this->Setting['SITEMAP_ROBOT_LINK']) {
			$this->AddLinkInRobot();
		}

		return 'complite';
	}

	function AddLinkInRobot()
	{
		$arRobot = array();
		foreach (preg_split('#[\n]+#is', file_get_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt')) as $sLine) {
			$sLine = trim(current(explode('#', trim($sLine), 2)));
			if (substr_count($sLine, ':') < 1) {
				continue;
			}
			$arLine = explode(':', $sLine, 2);
			if (strtolower(trim($arLine[0])) == 'sitemap') {
				continue;
			}
			$arRobot[] = $sLine;
		}

		if(!$arRobot){
			$arRobot[] = 'User-Agent: *';
		}
		$arRobot[] = 'Sitemap: '.$this->Setting['SITEMAP_ADDRESS'];

		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt', implode("\r\n", $arRobot));
	}

	function Priority()
	{
		global $DB;

		$DB->Query("UPDATE `k2_mod_seo_sitemap` SET `PRIORITY` = '1.0' WHERE `URL` = '".DBS($this->Domain.'/')."';");
		$DB->Query("UPDATE `k2_mod_seo_sitemap` SET `PRIORITY` = '0.5' WHERE `URL` != '".DBS($this->Domain.'/')."';");

		if($this->Setting['SITEMAP_PRIORITY']){
			foreach(explode("\r\n", $this->Setting['SITEMAP_PRIORITY']) as $sLine)
			{
				$arExp = preg_split("#[\s]+#", $sLine);
				if(count($arExp) == 2){
					$DB->Query("UPDATE `k2_mod_seo_sitemap` SET `PRIORITY` = '".DBS($arExp[1])."' WHERE `URL` = '".DBS($arExp[0])."';");
				}
			}
		}
	}

	function CreateFile()
	{
		global $DB;

		$xml = new SimpleXMLElement('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

		$sDate = date('Y-m-d');

		$arRows = $DB->Rows("SELECT * FROM `k2_mod_seo_sitemap` WHERE `STATUS` = 200 ORDER BY `URL` ASC");
		foreach($arRows as $arRow)
		{
			$node = $xml->addChild('url');
			$node->addChild('loc', html($arRow['URL']));
			$node->addChild('priority', html($arRow['PRIORITY']));
			$node->addChild('lastmod', $sDate);
		}

		return $xml->asXML($_SERVER['DOCUMENT_ROOT'].$this->Setting['SITEMAP_FILENAME']);
	}

	function RobotRegex($sURL)
	{
		$sURL = str_replace(array('.', '*'), array('\.', '.*'), $sURL);

		if (substr($sURL, 0, 1) == '/') {
			$sURL = '^'.$sURL;
		}

		return $sURL;
	}

	function SortByLength($a, $b)
	{
		if (strlen($b[1]) < strlen($a[1])) {
			return 1;
		} elseif (strlen($b[1]) == strlen($a[1])
		) {
			return 0;
		} else {
			return -1;
		}
	}

	function Robot()
	{
		global $DB;

		$arRules = array();
		foreach (preg_split('#[\n]+#is', file_get_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt')) as $sLine) {
			$sLine = trim(current(explode('#', trim($sLine), 2)));
			if (substr_count($sLine, ':') < 1) {
				continue;
			}
			$arLine = explode(':', $sLine, 2);

			$sDirective = strtolower(trim($arLine[0]));
			$sValue = trim($arLine[1]);

			if (!in_array($sDirective, array('allow', 'disallow', 'clean-param'))) {
				continue;
			}

			$arRules[] = array($sDirective, $sValue);
		}

		if (!$arRules) {
			return true;
		}

		usort($arRules, array('SeoSitemap', 'SortByLength'));

		foreach ($arRules as &$arRule) {
			$sDirective = $arRule[0];
			$sValue = $arRule[1];

			if(in_array($sDirective, array('allow', 'disallow'))){
				$arRule[2] = $this->RobotRegex($sValue);
			}

			if($sDirective == 'clean-param'){
				$arExp = explode(' ', $sValue);

				$sURL = trim($arExp[1]);
				$arVar = explode('&', trim($arExp[0]));

				$arRule[2] = $this->RobotRegex($sURL);
				$arRule[3] = $arVar;
			}

		}

		$arRows = $this->Rows();
		foreach($arRows as $arRow)
		{
			if($arRow['STATUS'] != 200){
				continue;
			}

			$bDelete = false;

			foreach($arRules as $arCRule)
			{
				$sURLClear = str_replace($this->Domain, '', $arRow['URL']);
				if(!$sURLClear){
					$sURLClear = '/';
				}

				if(!preg_match("#".$arCRule[2]."#", $sURLClear)){
					continue;
				}

				if($arCRule[0] == 'allow'){
					$bDelete = false;
				}

				if($arCRule[0] == 'disallow'){
					$bDelete = true;
				}

				if($arCRule[0] == 'clean-param'){
					$arParse = parse_url($sURLClear);

					if($arParse['query']){
						$bFind = false;
						$arNewQuery = array();
						$arGetQuery = explode('&', urldecode($arParse['query']));
						foreach($arGetQuery as $sString)
						{
							$arExp = explode('=', $sString);
							if(in_array($arExp[0], $arCRule[3])){
								$bFind = true;
							}else{
								$arNewQuery[] = $arExp[0].'='.$arExp[1];
							}
						}

						if(!$bFind){
							continue;
						}

						$sNewLink = $this->Domain.$arParse['path'];
						if($arNewQuery){
							$sNewLink .= '?'.implode('&', $arNewQuery);
						}

						$DB->Query("UPDATE `k2_mod_seo_sitemap` SET `URL` = '".DBS($sNewLink)."' WHERE `ID` = '".$arRow['ID']."';");
					}
				}
			}

			if($bDelete){
				$DB->Query("DELETE FROM `k2_mod_seo_sitemap` WHERE `ID` = '".$arRow['ID']."'");
			}
		}

		$arUniqLink = array();

		$arRows = $this->Rows();
		foreach($arRows as $arRow) {
			if ($arRow['STATUS'] != 200) {
				continue;
			}

			if($arUniqLink[$arRow['URL']]){
				$DB->Query("DELETE FROM `k2_mod_seo_sitemap` WHERE `ID` = '".$arRow['ID']."'");
			}

			$arUniqLink[$arRow['URL']] = $arRow['ID'];
		}
	}

	function Link($sURL, $arPar = array())
	{
		global $DB;

		if ($DB->Row("SELECT 1 FROM `k2_mod_seo_sitemap` WHERE `URL` = '".DBS($sURL)."'")) {
			$QB = new QueryBuilder;
			$QB->Update('k2_mod_seo_sitemap');

			foreach ($arPar as $sKey => $sValue) {
				$QB->Set('`'.$sKey.'` = ?', $sValue);
			}

			$QB->Where('`URL` = ?', $sURL);
			$DB->Query($QB->Build());
		} else {
			$arPar['URL'] = $sURL;

			$QB = new QueryBuilder;
			$QB->Insert('k2_mod_seo_sitemap', $arPar);
			$DB->Query($QB->Build());
		}
	}

	private function Request($arURL)
	{
		$arHandler = array();

		foreach ($arURL as $sURL) {

			if ($this->DBURL[$sURL]['COMPLITE'] == 1) {
				continue;
			}

			$cURL = curl_init();
			curl_setopt($cURL, CURLOPT_URL, $sURL);
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($cURL, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; k2bot[sm]/1.0)');
			curl_setopt($cURL, CURLOPT_AUTOREFERER, true);
			curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($cURL, CURLOPT_MAXREDIRS, 10);

			$arHandler[$sURL] = $cURL;

			$this->DBURL[$sURL]['COMPLITE'] = true;
		}

		$arMultiHandler = curl_multi_init();

		foreach ($arHandler as $cURL) {
			curl_multi_add_handle($arMultiHandler, $cURL);
		}

		do {
			$mCURL = curl_multi_exec($arMultiHandler, $bActive);
		} while ($mCURL == CURLM_CALL_MULTI_PERFORM || $bActive);

		foreach ($arHandler as $sURL => $cURL) {
			$arInfo = curl_getinfo($cURL);

			if ($arInfo['http_code'] == 200) {
				$this->parseContent(curl_multi_getcontent($cURL));
			} else if (in_array($arInfo['http_code'], array(301, 302))) {
				$this->parseContent('<a href="'.$arInfo['redirect_url'].'">redirect</a>');
			}

			$this->Link($sURL, array('STATUS' => $arInfo['http_code'], 'COMPLITE' => 1));
		}
		curl_multi_close($arMultiHandler);
	}

	private function parseContent($sContent)
	{
		preg_match_all("#<a[^>]*href\s*=\s*'([^']*)'|<a[^>]*href\s*=\s*\"([^\"]*)\"#is", $sContent, $arMatch);

		$arLink = array_merge($arMatch[1], $arMatch[2]);
		for ($i = 0; $i < count($arLink); $i++) {
			$sLink = trim($arLink[$i]);

			if (!$sLink || $sLink == '/') {
				continue;
			}

			$sFirst = substr($sLink, 0, 1);
			if ($sFirst != '/') {
				continue;
			}

			if (count(explode('.', $sLink)) > 1) {
				continue;
			}

			$this->Link($this->Domain.$sLink);
		}
	}
}

?>