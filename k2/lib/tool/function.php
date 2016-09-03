<?
function Delayed($sKey, $sText)
{
	global $DELAYED_VARIABLE;

	$DELAYED_VARIABLE[$sKey] = $sText;
}

function Redirect($sPath = '/', $nStatus = 0)
{
	if($nStatus == 302){
		header($_SERVER['SERVER_PROTOCOL'].' 302 Moved Permanently');
		header('Location: '.$sPath, true, 302);
		exit;
	}
	header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
	header('Location: '.$sPath, true, 301);
	exit;
}

function dateFormat($sDate, $sTemplate = 'd.m.Y, H:i')
{
	$arMonth = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');

	$arDate = array();
	if(preg_match("#^(\d{4})-(\d{2})-(\d{2})(\s(\d{2}):(\d{2}):(\d{2}))?#", $sDate, $arMath)){
		$arDate['Y'] = $arMath[1];
		$arDate['M'] = $arMath[2];
		$arDate['D'] = $arMath[3];
		$arDate['H'] = $arMath[5];
		$arDate['I'] = $arMath[6];
		$arDate['S'] = $arMath[7];
	}
	if(preg_match("#^(\d{2})\.(\d{2})\.(\d{4})(\s(\d{2}):(\d{2}))?#", $sDate, $arMath)){
		$arDate['Y'] = $arMath[3];
		$arDate['M'] = $arMath[2];
		$arDate['D'] = $arMath[1];
		$arDate['H'] = $arMath[5];
		$arDate['I'] = $arMath[6];
		$arDate['S'] = $arMath[7];
	}

	$arDate['Y'] = (int)$arDate['Y'];
	$arDate['M'] = (int)$arDate['M'];
	$arDate['D'] = (int)$arDate['D'];

	if(!$arDate['Y'] || !$arDate['M'] || !$arDate['D']){
		return false;
	}

	$sTemplate = str_replace('month', $arMonth[(int)$arDate['M'] - 1], $sTemplate);
	$sDate = date($sTemplate, mktime($arDate['H'], $arDate['I'], $arDate['S'], $arDate['M'], $arDate['D'], $arDate['Y']));

	return $sDate;
}

function bufferContent($sCont)
{
	global $LIB, $DELAYED_VARIABLE;
	if($DELAYED_VARIABLE){
		foreach($DELAYED_VARIABLE as $sKey => $sValue){
			$sCont = str_replace('<!-- $'.$sKey.'$ -->', $sValue, $sCont);
		}
	}

	return $sCont;
}

function clearArray($arList = array())
{
	$arNewList = array();
	foreach($arList as $arList){
		if($arList){
			$arNewList[] = $arList;
		}
	}

	return $arNewList;
}

function p($mVar, $bAdmin = false)
{
	if($bAdmin){
		global $USER;

		if($USER['USER_GROUP'] != 1){
			return;
		}
	}
	?>
	<pre>
	<? print_r($mVar); ?>
	</pre>
	<?
}

function sortArray($a, $b)
{
	if($a['SORT'] == $b['SORT']){
		return 0;
	}
	if($a['SORT'] < $b['SORT']){
		return -1;
	}

	return 1;
}

function httpRequest($sURL, $arPar = array())
{
	$sCookie = tempnam($_SERVER['DOCUMENT_ROOT'].'/tmp/', 'cookie');

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $sURL);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $sCookie);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 15);

	$sText = curl_exec($ch);
	$sStatus = curl_getinfo($ch);
	curl_close($ch);
	if($arPar['GZIP']){
		$sText = gzinflate(substr($sText, 10));
	}

	unlink($sCookie);

	if($sStatus['http_code'] != 200){
		if($sStatus['http_code'] == 301 || $sStatus['http_code'] == 302){
			list($arHeader) = explode("\r\n\r\n", $sText, 2);
			preg_match("/(Location:|URI:)[^(\n)]*/", $arHeader, $arMatch);
			$sCURL = trim(str_replace($arMatch[1], '', $arMatch[0]));
			$arParse = parse_url($sCURL);

			return (isset($arParse)) ? httpRequest($sCURL) : '';
		}
	}else{
		$arExp = explode("\r\n\r\n", $sText, 2);

		return $arExp[1];
	}
}

function urlQuery($arGet = array(), $arGetDelete = array())
{
	$arURL = array();

	$arParse = parse_url($_SERVER['REQUEST_URI']);
	$sURL = $arParse['path'];

	if($arParse['query']){
		foreach(explode('&', urldecode($arParse['query'])) as $sString){
			$arExp = explode('=', $sString);
			if(!in_array($arExp[0], $arGetDelete) && !isset($arGet[$arExp[0]])){
				$arURL[] = urlencode($arExp[0]).'='.urlencode($arExp[1]);
			}
		}
	}

	foreach($arGet as $sKey => $sValue){
		$arURL[] = html($sKey).'='.html($sValue);
	}

	if($arURL){
		$sURL .= '?';
	}

	return $sURL.implode('&', $arURL);
}

function genPassword($nLength = 10, $bLower = false)
{
	$sPassword = substr(str_shuffle('qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP'), 0, $nLength);
	if($bLower){
		return strtolower($sPassword);
	}

	return $sPassword;
}

function changeMessage($sName, $sKey = 'FORM_EMPTY_FIELD')
{
	global $MESS;
	if($MESS[$sKey]){
		$MESS[$sKey] = str_replace('%FIELD%', $sName, $MESS[$sKey]);

		return $MESS[$sKey];
	}

	return 'Неизвестная ошибка';
}

function html($sText)
{
	return htmlspecialchars($sText);
}

function htmlBack($sText)
{
	return str_replace(array('&lt;', '&gt;', '&quot;', '&amp;', '&nbsp;'), array('<', '>', '"', '&', ' '), $sText);
}

function Lang($sKey)
{
	global $MESS;
	if($MESS[$sKey]){
		return $MESS[$sKey];
	}

	return 'Неизвестная ошибка';
}

function toArray($mText)
{
	return clearArray(explode(',', $mText));
}

function respectiveURL($arPar, $sType)
{
	global $CURRENT, $LIB;

	$sURL = $arPar['URL_ORIGINAL'];

	if($arPar['URL_REDIRECT']){
		return $arPar['URL_REDIRECT'];
	}

	if($arPar['URL_ALTERNATIVE']){
		$sURL = $arPar['URL_ALTERNATIVE'];

		$arList = $LIB['URL']->RowsCache();

		if($sType == 'category'){

			foreach($arList as $arItem){
				if($arPar['SECTION_BLOCK'] == $arItem['SECTION_BLOCK'] && $arPar['ID'] == $arItem['CATEGORY']){
					return $arItem['URL'];
				}
			}
		}

		if($sType == 'element'){
			foreach($arList as $arItem){
				if($arPar['SECTION_BLOCK'] == $arItem['SECTION_BLOCK'] && $arPar['ID'] == $arItem['ELEMENT']){
					return $arItem['URL'];
				}
			}
		}
	}

	if($arPar['FOLDER'] && $CURRENT['SITE']['SECTION_INDEX'] == $arPar['ID']){
		$sURL = '/';
	}

	return $sURL;
}

function Declination($nNumber, array $arTitle)
{
	$arArray = array(2, 0, 1, 1, 1, 2);
	return $arTitle[($nNumber % 100 > 4 && $nNumber % 100 < 20) ? 2 : $arArray[min($nNumber % 10, 5)]];
}

function unserializeArray($sText)
{
	$sText = unserialize($sText);
	if(!is_array($sText)){
		return [];
	}
	return $sText;
}

?>