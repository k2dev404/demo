<?
function formCheck($arField, $arPar = array())
{
	if (!$arPar) {
		$arPar = $_POST;
	}
	foreach ($arField as $sKey => $sValue) {
		$arPar[$sKey] = trim($arPar[$sKey]);
		$arPar[$sKey] = str_replace(' ', '', $arPar[$sKey]);
		if (empty($arPar[$sKey])) {
			return changeMessage($sValue);
		}
	}

	return false;
}

?>