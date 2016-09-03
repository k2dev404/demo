<?

class BlockElement
{
	function ID($nID, $nSBlock)
	{
		global $DB, $LIB;

		if (!$arSBlock = $LIB['SECTION_BLOCK']->ID($nSBlock)) {
			$this->Error = $LIB['SECTION_BLOCK']->Error;

			return false;
		}

		if ($arElement = $DB->Row("SELECT * FROM `k2_block".$arSBlock['BLOCK']."` WHERE `ID` = '".(int)$nID."'")) {
			$arSection = $DB->Row("SELECT `URL_ORIGINAL` FROM `k2_section` WHERE `ID` = '".$arSBlock['SECTION']."'");
			$arElement['URL_ORIGINAL'] = $arSection['URL_ORIGINAL'].$nSBlock.'/'.$arElement['ID'].'/';

			$arElement['URL'] = respectiveURL($arElement, 'element');
			$arElement['URL_BACK'] = $arSection['URL_ORIGINAL'];

			return $arElement;
		}

		$this->Error = 'Элемент не найден';

		return false;
	}

	function Rows($nBlock, $arFilter = array(), $arOrderBy = array(), $arSelect = array(), $nSize = 0, $nLimit = 0)
	{
		global $LIB, $DB;

		$nBlock = (int)$nBlock;

		if ($arSelect) {
			$arSelect[] = 'ID';
			$arSelect[] = 'SECTION_BLOCK';
		}

		if ($nSize) {
			$LIB['NAV']->Setting['SIZE'] = $nSize;
			$LIB['NAV']->Setting['TOTAL'] = 0;
		}

		if ($nLimit) {
			$LIB['NAV']->Setting = array();
		}

		$arCFilter = array('FROM' => 'k2_block'.$nBlock, 'WHERE' => $arFilter, 'ORDER_BY' => $arOrderBy, 'SELECT' => $arSelect, 'SIZE' => $nSize, 'LIMIT' => $nLimit);

		$sSQL = $DB->CSQL($arCFilter);

		if ((!$arList = $DB->Rows($sSQL)) && $_GET['page'] > 1) {
			$_GET['page'] = 1;
			$sSQL = $DB->CSQL($arCFilter);
			$arList = $DB->Rows($sSQL);
		}

		if ($arList) {
			$arCount = $DB->Row("SELECT FOUND_ROWS()");
			$LIB['NAV']->Setting['TOTAL'] = $arCount['FOUND_ROWS()'];

			$arRows = $DB->Rows("SELECT ID, URL_ORIGINAL FROM k2_section");
			for ($i = 0; $i < count($arRows); $i++) {
				$arSectionURL[$arRows[$i]['ID']] = $arRows[$i]['URL_ORIGINAL'];
			}

			for ($i = 0; $i < count($arList); $i++) {
				$arList[$i]['URL_ORIGINAL'] = $arSectionURL[$arList[$i]['SECTION']].$arList[$i]['SECTION_BLOCK'].'/'.$arList[$i]['ID'].'/';
				$arList[$i]['URL'] = respectiveURL($arList[$i], 'element');
			}
		}

		return $arList;
	}

	function Add($nSBlock, $arPar = array())
	{
		global $LIB, $DB, $USER;

		if (!$arSBlock = $LIB['SECTION_BLOCK']->ID($nSBlock)) {
			$this->Error = $LIB['SECTION_BLOCK']->Error;

			return false;
		}
		if (!$arSection = $LIB['SECTION']->ID($arSBlock['SECTION'])) {
			$this->Error = $LIB['SECTION']->Error;

			return false;
		}

		if (!$arPar['SECTION']) {
			$arPar['SECTION'] = $arSection['ID'];
		}

		$arPar['SECTION_BLOCK'] = $arSBlock['ID'];
		$arPar['BLOCK'] = $arSBlock['BLOCK'];
		$arPar['SITE'] = $arSection['SITE'];

		if ($arPar['URL_ALTERNATIVE'] && !$LIB['URL']->Check($arPar['URL_ALTERNATIVE'])) {
			$this->Error = $LIB['URL']->Error;

			return false;
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2_block'.$arPar['BLOCK'], $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($sError = $LIB['EVENT']->Execute('BEFORE_ADD_BLOCK_ELEMENT', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($nID = $DB->Insert("
        	INSERT INTO `k2_block".$arPar['BLOCK']."` (
	        	`DATE_CREATED`,
	        	`USER_CREATED`,
	        	`ACTIVE`,
	        	`SORT`,
	        	`SECTION`,
	        	`SECTION_BLOCK`,
	        	`CATEGORY`,
	        	`URL_ALTERNATIVE`,
	        	`SEO_TITLE`,
	        	`SEO_KEYWORD`,
	        	`SEO_DESCRIPTION`
        	) VALUES (
        		NOW(),
	        	'".$USER['ID']."',
	        	'".(int)$arPar['ACTIVE']."',
	        	'".(int)$arPar['SORT']."',
	        	'".(int)$arPar['SECTION']."',
	        	'".(int)$arPar['SECTION_BLOCK']."',
	        	'".(int)$arPar['CATEGORY']."',
	        	'".DBS($arPar['URL_ALTERNATIVE'])."',
	        	'".DBS($arPar['SEO_TITLE'])."',
	        	'".DBS($arPar['SEO_KEYWORD'])."',
	        	'".DBS($arPar['SEO_DESCRIPTION'])."'
        	)")
		) {
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_block'.$arPar['BLOCK']), $arPar);
			$arPar['ID'] = $nID;

			$LIB['URL']->Add(array('URL' => $arPar['URL_ALTERNATIVE'], 'SITE' => $arPar['SITE'], 'SECTION' => $arPar['SECTION'], 'SECTION_BLOCK' => $arPar['SECTION_BLOCK'], 'CATEGORY' => $arPar['CATEGORY'], 'ELEMENT' => $nID));

			$LIB['EVENT']->Execute('AFTER_ADD_BLOCK_ELEMENT', $arPar);

			return $nID;
		}

		return false;
	}

	function Edit($nID, $nSBlock, $arPar = array(), $bFull = false)
	{
		global $LIB, $DB, $USER;

		if (!$arElement = $this->ID($nID, $nSBlock)) {
			return false;
		}

		if (!$arSBlock = $LIB['SECTION_BLOCK']->ID($arElement['SECTION_BLOCK'])) {
			$this->Error = $LIB['SECTION_BLOCK']->Error;

			return false;
		}
		if (!$arSection = $LIB['SECTION']->ID($arSBlock['SECTION'])) {
			$this->Error = $LIB['SECTION']->Error;

			return false;
		}

		if (!$bFull) {
			$arPar += $arElement;
		}

		if (!$arPar['SECTION']) {
			$arPar['SECTION'] = $arSection['ID'];
		}
		if (!$arPar['SECTION_BLOCK']) {
			$arPar['SECTION_BLOCK'] = $arSBlock['ID'];
		}
		$arPar['BLOCK'] = $arSBlock['BLOCK'];
		$arPar['SITE'] = $arSection['SITE'];

		$arPar['ELEMENT'] = $nID;

		if ($arPar['URL_ALTERNATIVE'] && !$LIB['URL']->Check($arPar['URL_ALTERNATIVE'], $arPar)) {
			$this->Error = $LIB['URL']->Error;

			return false;
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2_block'.$arPar['BLOCK'], $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($sError = $LIB['EVENT']->Execute('BEFORE_EDIT_BLOCK_ELEMENT', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($DB->Query("UPDATE
        	`k2_block".$arPar['BLOCK']."`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`SORT` = '".(int)$arPar['SORT']."',
			`DATE_CHANGE` = NOW(),
			`USER_CHANGE` = '".$USER['ID']."',
			`SECTION` = '".(int)$arPar['SECTION']."',
			`SECTION_BLOCK` = '".(int)$arPar['SECTION_BLOCK']."',
			`CATEGORY` = '".(int)$arPar['CATEGORY']."',
			`URL_ALTERNATIVE` = '".DBS($arPar['URL_ALTERNATIVE'])."',
			`SEO_TITLE` = '".DBS($arPar['SEO_TITLE'])."',
			`SEO_KEYWORD` = '".DBS($arPar['SEO_KEYWORD'])."',
			`SEO_DESCRIPTION` = '".DBS($arPar['SEO_DESCRIPTION'])."'
        WHERE
        	ID = '".$nID."';
        ")
		) {
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_block'.$arPar['BLOCK']), $arPar);

			$arParCopy = $arPar;
			$arParCopy['ELEMENT'] = $nID;
			$arParCopy['URL'] = $arParCopy['URL_ALTERNATIVE'];
			$arParCopy['OLD_URL'] = $arElement['URL_ALTERNATIVE'];
			$LIB['URL']->Add($arParCopy);

			$LIB['EVENT']->Execute('AFTER_EDIT_BLOCK_ELEMENT', $arPar);

			return true;
		}

		return false;
	}

	function Delete($nID, $nSBlock)
	{
		global $DB, $LIB;

		if (!$arElm = $this->ID($nID, $nSBlock)) {
			return false;
		}

		if (!$arSBlock = $LIB['SECTION_BLOCK']->ID($nSBlock)) {
			$this->Error = $LIB['SECTION_BLOCK']->Error;

			return false;
		}

		$LIB['EVENT']->Execute('BEFORE_DELETE_BLOCK_ELEMENT', $arElm);
		$LIB['FIELD']->DeleteContent(array('TABLE' => 'k2_block'.$arSBlock['BLOCK'], 'ELEMENT' => $nID));
		$LIB['URL']->Delete($arElm['URL_ALTERNATIVE']);
		$DB->Query("DELETE FROM `k2_block".$arSBlock['BLOCK']."` WHERE `ID` = '".(int)$nID."'");
		$LIB['EVENT']->Execute('AFTER_DELETE_BLOCK_ELEMENT', $arElm);

		return true;
	}
}

?>