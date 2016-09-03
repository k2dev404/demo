<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/function.php');

$sText = httpRequest('http://www.cbr-xml-daily.ru/daily.xml');

preg_match_all("#<Valute ID=\"([^\"]+)[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>([^<]+)#i", $sText, $arMath, PREG_SET_ORDER);
foreach($arMath as $arCMath)
{
	if($arCMath[2] == 840){
		$nUSD = str_replace(',', '.',$arCMath[4]);
	}
	/*
	if($arCMath[2] == 978){
		$nEUR = str_replace(',', '.',$arCMath[4]);
	}
	*/
}

if(!$nUSD){
	exit;
}

$nOriginalUSD = $nUSD;

$nUSD += ($nUSD / 100) * 2;

$DB->Query("UPDATE `k2_site` SET `USD` = '".$nUSD."' WHERE `ID` = 1;");

foreach($LIB['BLOCK_ELEMENT']->Rows(4) as $arItem)
{
	if((int)$arItem['PRICE_USD']){
		$DB->Query("UPDATE `k2_block4` SET `PRICE` = '".($arItem['PRICE_USD'] * $nUSD)."' WHERE `ID` = '".$arItem['ID']."';");
	}
}

$sText = date('d.m.Y H:i').' - '.$nOriginalUSD.'('.$nUSD.')';

file_put_contents('log.txt', file_get_contents('log.txt')."\r\n".$sText);

?>