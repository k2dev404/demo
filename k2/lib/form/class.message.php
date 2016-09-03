<?

class FormMessage
{
	function ID($nID, $nForm)
	{
		global $DB, $LIB;

		if (!$arForm = $LIB['FORM']->ID($nForm)) {
			$this->Error = $LIB['FORM']->Error;

			return false;
		}

		if ($arMessage = $DB->Row("SELECT * FROM `k2_form".$arForm['ID']."` WHERE `ID` = '".(int)$nID."'")) {
			return $arMessage;
		}

		$this->Error = 'Сообщение не найдено';

		return false;
	}

	function Rows($nForm, $arFilter = array(), $arOrderBy = array(), $arSelect = array(), $nSize = 0, $nLimit = 0)
	{
		global $LIB, $DB;

		$nForm = (int)$nForm;

		if ($nSize) {
			$LIB['NAV']->Setting['SIZE'] = $nSize;
			$LIB['NAV']->Setting['TOTAL'] = 0;
		}

		if ($nLimit) {
			$LIB['NAV']->Setting = array();
		}

		$arCFilter = array('FROM' => 'k2_form'.$nForm, 'WHERE' => $arFilter, 'ORDER_BY' => $arOrderBy, 'SELECT' => $arSelect, 'SIZE' => $nSize, 'LIMIT' => $nLimit);

		$sSQL = $DB->CSQL($arCFilter);

		if ((!$arList = $DB->Rows($sSQL)) && $_GET['page'] > 1) {
			$_GET['page'] = 1;
			$sSQL = $DB->CSQL($arCFilter);
			$arList = $DB->Rows($sSQL);
		}
		if ($arList) {
			$arCount = $DB->Row("SELECT FOUND_ROWS()");
			$LIB['NAV']->Setting['TOTAL'] = $arCount['FOUND_ROWS()'];
		}

		return $arList;
	}

	function Add($nForm, $arPar = array())
	{
		global $LIB, $DB, $USER;

		if (!$arForm = $LIB['FORM']->ID($nForm)) {
			$this->Error = $LIB['FORM']->Error;

			return false;
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2_form'.$arForm['ID'], $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($arForm['CAPTCHA'] && ($_SESSION['CAPTCHA'] != $arPar['CAPTCHA'])) {
			$this->Error = 'Введены неправильные символы на картинке';

			return false;
		}

		$arPar['FORM'] = $nForm;

		if ($sError = $LIB['EVENT']->Execute('BEFORE_ADD_FORM_MESSAGE', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($nID = $DB->Insert("
        	INSERT INTO `k2_form".$arPar['FORM']."` (
	        	`DATE_CREATED`,
	        	`USER_CREATED`
        	) VALUES (
        		NOW(),
	        	'".$USER['ID']."'
        	)")
		) {
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_form'.$arPar['FORM']), $arPar);
			$arPar['ID'] = $nID;

			$LIB['EVENT']->Execute('AFTER_ADD_FORM_MESSAGE', $arPar);

			return $nID;
		}

		return false;
	}

	function Edit($nID, $nForm, $arPar = array(), $bFull = false)
	{
		global $LIB, $DB, $USER;

		if (!$arMessage = $this->ID($nID, $nForm)) {
			return false;
		}

		if (!$bFull) {
			$arPar += $arMessage;
		}

		$arPar['FORM'] = (int)$nForm;

		if ($sError = $LIB['FIELD']->CheckAll('k2_form'.$arPar['FORM'], $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($sError = $LIB['EVENT']->Execute('BEFORE_EDIT_FORM_MESSAGE', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($DB->Query("UPDATE
        	`k2_form".$arPar['FORM']."`
        SET
			`DATE_CHANGE` = NOW(),
			`USER_CHANGE` = '".$USER['ID']."'
        WHERE
        	ID = '".$nID."';
        ")
		) {
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_form'.$arPar['FORM']), $arPar);
			$LIB['EVENT']->Execute('AFTER_EDIT_FORM_MESSAGE', $arPar);

			return true;
		}

		return false;
	}

	function Delete($nID, $nForm)
	{
		global $DB, $LIB;

		if (!$arMessage = $this->ID($nID, $nForm)) {
			return false;
		}

		$arMessage['FORM'] = $nForm;

		$LIB['EVENT']->Execute('BEFORE_DELETE_FORM_MESSAGE', $arMessage);
		$LIB['FIELD']->DeleteContent(array('TABLE' => 'k2_form'.$arMessage['FORM'], 'ELEMENT' => $arMessage['ID']));
		$DB->Query("DELETE FROM `k2_form".$nForm."` WHERE `ID` = '".$arMessage['ID']."'");
		$LIB['EVENT']->Execute('AFTER_DELETE_FORM_MESSAGE', $arMessage);
	}
}

?>