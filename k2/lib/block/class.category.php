<?

class BlockCategory
{
	function ID($nID, $nBlock)
	{
		global $DB;
		if ($arCategory = $DB->Row("SELECT * FROM `k2_block".(int)$nBlock."category` WHERE ID = '".(int)$nID."'")) {
			$arSection = $DB->Row("SELECT `URL_ORIGINAL` FROM `k2_section` WHERE `ID` = '".$arCategory['SECTION']."'");
			$arCategory['URL_ORIGINAL'] = $arSection['URL_ORIGINAL'].'c'.$arCategory['SECTION_BLOCK'].'/'.$arCategory['ID'].'/';
			$arCategory['URL'] = respectiveURL($arCategory, 'category');
			$arCategory['URL_BACK'] = $arSection['URL_ORIGINAL'];

			return $arCategory;
		}
		$this->Error = 'Категория не найдена';

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

		$arCFilter = array('FROM' => 'k2_block'.$nBlock.'category', 'WHERE' => $arFilter, 'ORDER_BY' => $arOrderBy, 'SELECT' => $arSelect, 'SIZE' => $nSize, 'LIMIT' => $nLimit);

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
				$arList[$i]['URL_ORIGINAL'] = $arSectionURL[$arList[$i]['SECTION']].'c'.$arList[$i]['SECTION_BLOCK'].'/'.$arList[$i]['ID'].'/';
				$arList[$i]['URL'] = respectiveURL($arList[$i], 'category');
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

		$arPar['SECTION'] = $arSection['ID'];
		$arPar['SECTION_BLOCK'] = $arSBlock['ID'];
		$arPar['BLOCK'] = $arSBlock['BLOCK'];
		$arPar['SITE'] = $arSection['SITE'];

		if ($sError = formCheck(array('NAME' => 'Название'), $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($arPar['URL_ALTERNATIVE'] && !$LIB['URL']->Check($arPar['URL_ALTERNATIVE'])) {
			$this->Error = $LIB['URL']->Error;

			return false;
		}

		if ($this->Error = $LIB['FIELD']->CheckAll('k2block'.$arBlock['ID'].'category', $arPar)) {
			return false;
		}

		if ($sError = $LIB['EVENT']->Execute('BEFORE_ADD_BLOCK_CATEGORY', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($nID = $DB->Insert("
	        INSERT INTO `k2_block".$arPar['BLOCK']."category` (
	        	`DATE_CREATED`,
	        	`USER_CREATED`,
	        	`SECTION`,
	        	`SECTION_BLOCK`,
	        	`PARENT`,
	        	`ACTIVE`,
	        	`SORT`,
	        	`NAME`,
	        	`SEO_TITLE`,
				`SEO_KEYWORD`,
				`SEO_DESCRIPTION`
	        ) VALUES (
	        	NOW(),
	        	'".$USER['ID']."',
	        	'".(int)$arPar['SECTION']."',
	        	'".(int)$arPar['SECTION_BLOCK']."',
	        	'".(int)$arPar['PARENT']."',
	        	'".(int)$arPar['ACTIVE']."',
	        	'".(int)$arPar['SORT']."',
	        	'".DBS($arPar['NAME'])."',
				'".DBS($arPar['SEO_TITLE'])."',
				'".DBS($arPar['SEO_KEYWORD'])."',
				'".DBS($arPar['SEO_DESCRIPTION'])."'
	        )")
		) {
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_block'.$arPar['BLOCK'].'category'), $arPar);

			$arPar['ID'] = $nID;
			$LIB['EVENT']->Execute('AFTER_ADD_BLOCK_CATEGORY', $arPar);

			return $nID;
		}

		return false;
	}

	function Edit($nID, $nSBlock, $arPar = array(), $bFull = false)
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
		if (!$arCategory = $this->ID($nID, $arSBlock['BLOCK'])) {
			return false;
		}

		if (!$bFull) {
			$arPar += $arCategory;
		}

		if (!$arPar['SECTION']) {
			$arPar['SECTION'] = $arSection['ID'];
		}
		if (!$arPar['SECTION_BLOCK']) {
			$arPar['SECTION_BLOCK'] = $arSBlock['ID'];
		}
		$arPar['BLOCK'] = $arSBlock['BLOCK'];
		$arPar['SITE'] = $arSection['SITE'];

		if ($sError = formCheck(array('NAME' => 'Название'), $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($arPar['URL_ALTERNATIVE'] && !$LIB['URL']->Check($arPar['URL_ALTERNATIVE'], array('CATEGORY' => $nID))) {
			$this->Error = $LIB['URL']->Error;

			return false;
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2block'.$arSBlock['BLOCK'].'category', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($sError = $LIB['EVENT']->Execute('BEFORE_EDIT_BLOCK_CATEGORY', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($DB->Query("UPDATE
        	`k2_block".$arSBlock['BLOCK']."category`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`SORT` = '".(int)$arPar['SORT']."',
			`DATE_CHANGE` = NOW(),
			`USER_CHANGE` = '".$USER['ID']."',
			`SECTION` = '".(int)$arPar['SECTION']."',
			`SECTION_BLOCK` = '".(int)$arPar['SECTION_BLOCK']."',
			`PARENT` = '".(int)$arPar['PARENT']."',
			`URL_ALTERNATIVE` = '".DBS($arPar['URL_ALTERNATIVE'])."',
			`SEO_TITLE` = '".DBS($arPar['SEO_TITLE'])."',
			`SEO_KEYWORD` = '".DBS($arPar['SEO_KEYWORD'])."',
			`SEO_DESCRIPTION` = '".DBS($arPar['SEO_DESCRIPTION'])."',
			`NAME` = '".DBS($arPar['NAME'])."'
        WHERE
        	ID = '".$nID."';
        ")
		) {
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_block'.$arSBlock['BLOCK'].'category'), $arPar);

			$arParCopy = $arPar;
			$arParCopy['CATEGORY'] = $nID;
			$arParCopy['URL'] = $arParCopy['URL_ALTERNATIVE'];
			$arParCopy['OLD_URL'] = $arCategory['URL_ALTERNATIVE'];
			$LIB['URL']->Add($arParCopy);

			$LIB['EVENT']->Execute('AFTER_EDIT_BLOCK_CATEGORY', $arPar);

			return true;
		}

		return false;
	}

	function Delete($nID, $nSBlock)
	{
		global $DB, $LIB;

		if (!$arSBlock = $LIB['SECTION_BLOCK']->ID($nSBlock)) {
			$this->Error = $LIB['SECTION_BLOCK']->Error;

			return false;
		}

		if (!$arCategory = $this->ID($nID, $arSBlock['BLOCK'])) {
			return false;
		}

		$LIB['EVENT']->Execute('BEFORE_DELETE_BLOCK_CATEGORY', $arCategory);

		$arCCategory = $this->Child($arSBlock['BLOCK'], $nID, true);
		$arCCategory[] = $arCategory;

		for ($i = 0; $i < count($arCCategory); $i++) {
			$arElement = $LIB['BLOCK_ELEMENT']->Rows($arSBlock['BLOCK'], array('SECTION_BLOCK' => $nSBlock, 'CATEGORY' => $arCCategory[$i]['ID']), false, array('ID'));
			for ($n = 0; $n < count($arElement); $n++) {
				$LIB['BLOCK_ELEMENT']->Delete($arElement[$n]['ID'], $nSBlock);
			}
			$LIB['FIELD']->DeleteContent(array('TABLE' => 'k2_block'.$arSBlock['BLOCK'].'category', 'ELEMENT' => $arCCategory[$i]['ID']));
			$DB->Query("DELETE FROM `k2_block".$arSBlock['BLOCK']."category` WHERE `ID` = '".$arCCategory[$i]['ID']."'");
		}

		$LIB['EVENT']->Execute('AFTER_DELETE_BLOCK_CATEGORY', $arCategory);

		return true;
	}

	function Back($nBlock, $nID, $arList = array())
	{
		global $DB;

		if ($arCategory = $DB->Row("SELECT * FROM `k2_block".(int)$nBlock."category` WHERE `ID` = '".(int)$nID."'")) {

			if (!$this->SectionURL) {
				$arRows = $DB->Rows("SELECT ID, URL_ORIGINAL, URL_ALTERNATIVE FROM k2_section");
				for ($i = 0; $i < count($arRows); $i++) {
					$this->SectionURL[$arRows[$i]['ID']] = $arRows[$i]['URL_ORIGINAL'];
				}
			}

			$arCategory['URL_ORIGINAL'] = $this->SectionURL[$arCategory['SECTION']].'c'.$arCategory['SECTION_BLOCK'].'/'.$arCategory['ID'].'/';
			$arCategory['URL'] = respectiveURL($arCategory, 'category');

			$arList[] = $arCategory;
			if ($arCategory['PARENT']) {
				$arList = $this->Back($nBlock, $arCategory['PARENT'], $arList);
			} else {
				$arList = array_reverse($arList);
			}
		}

		return $arList;
	}

	function Child($nBlock, $nID, $bRecursive = false, $arFilter = array(), $arList = array(), $nLevel = 0)
	{
		global $DB;

		$arFilter['PARENT'] = (int)$nID;

		$arCFilter = array('FROM' => 'k2_block'.$nBlock.'category', 'WHERE' => $arFilter, 'ORDER_BY' => array('SORT' => 'ASC'));

		if ($arCategory = $DB->Rows($DB->CSQL($arCFilter))) {
			if (!$this->SectionURL) {
				$arRows = $DB->Rows("SELECT ID, URL_ORIGINAL, URL_ALTERNATIVE FROM k2_section");
				for ($i = 0; $i < count($arRows); $i++) {
					$this->SectionURL[$arRows[$i]['ID']] = $arRows[$i]['URL_ORIGINAL'];
				}
			}
			for ($i = 0; $i < count($arCategory); $i++) {
				$arCategory[$i]['URL_ORIGINAL'] = $this->SectionURL[$arCategory[$i]['SECTION']].'c'.$arCategory[$i]['SECTION_BLOCK'].'/'.$arCategory[$i]['ID'].'/';
				$arCategory[$i]['URL'] = respectiveURL($arCategory[$i], 'category');
				$arCategory[$i]['LEVEL'] = $nLevel;

				$arList[] = $arCategory[$i];
				if ($bRecursive) {
					$arList = $this->Child($nBlock, $arCategory[$i]['ID'], $bRecursive, $arFilter, $arList, ($nLevel + 1));
				}
			}
		}

		return $arList;
	}

	function Map($nBlock, $arPar)
	{
		global $DB;

		$arCFilter = array('FROM' => 'k2_block'.(int)$nBlock.'category', 'WHERE' => $arPar, 'ORDER_BY' => array('SORT' => 'ASC'));

		if ($arCategory = $DB->Rows($DB->CSQL($arCFilter))) {
			if (!$this->SectionURL) {
				$arRows = $DB->Rows("SELECT ID, URL_ORIGINAL, URL_ALTERNATIVE FROM k2_section");
				for ($i = 0; $i < count($arRows); $i++) {
					$this->SectionURL[$arRows[$i]['ID']] = $arRows[$i]['URL_ORIGINAL'];
				}
			}
			for ($i = 0; $i < count($arCategory); $i++) {
				$arCategory[$i]['URL_ORIGINAL'] = $this->SectionURL[$arCategory[$i]['SECTION']].'c'.$arCategory[$i]['SECTION_BLOCK'].'/'.$arCategory[$i]['ID'].'/';
				$arCategory[$i]['URL'] = respectiveURL($arCategory[$i], 'category');

				$arList[] = $arCategory[$i];
			}

			return $this->MapChild($arCategory);
		}

		return array();
	}

	function MapChild($arAllCategory, $nParent = 0, $arChild = array(), $nLevel = 0)
	{
		if (!$arAllCategory) {
			return $arChild;
		}

		foreach ($arAllCategory as $arCategory) {
			if ($arCategory['PARENT'] != $nParent) {
				continue;
			}

			$arCategory['LEVEL'] = $nLevel;
			$arChild[] = $arCategory;
			$arChild = $this->MapChild($arAllCategory, $arCategory['ID'], $arChild, ($nLevel + 1));
		}

		return $arChild;
	}
}
?>