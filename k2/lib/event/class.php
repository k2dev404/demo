<?

class Event
{
	function Add($sEvent, $sFunc)
	{
		$this->Handler[$sEvent][] = $sFunc;
	}

	function Execute($sEvent, &$arPar, $arInfo = array())
	{
		$this->Error = false;

		if (!$this->Handler[$sEvent]) {
			return false;
		}

		foreach ($this->Handler[$sEvent] as $sFunc) {
			$arParams = array(&$arPar);
			if ($arInfo) {
				$arParams[] = $arInfo;
			}
			if ($sResult = call_user_func_array($sFunc, $arParams)) {
				return $sResult;
			}
		}
	}
}
?>