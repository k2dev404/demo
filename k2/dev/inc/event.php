<?

$LIB['EVENT']->Add('BEFORE_ADD_BLOCK_ELEMENT', 'catalogEdit');
$LIB['EVENT']->Add('BEFORE_EDIT_BLOCK_ELEMENT', 'catalogEdit');

function catalogEdit(&$arPar)
{
	global $LIB;

	if($arPar['PRICE_USD']){
		$arSite = $LIB['SITE']->ID(1);
		$arPar['PRICE'] = $arSite['USD'] * $arPar['PRICE_USD'];
	}
}
?>