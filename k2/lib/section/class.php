<?

class Section
{
	function ID($nID, $bFullInfo = 0)
	{
		global $LIB, $DB;
		if ($arSection = $DB->Row("SELECT * FROM `k2_section` WHERE `ID` = '".$nID."'")) {
			if ($bFullInfo) {
				$arSectionBlock = $LIB['SECTION_BLOCK']->Rows($nID);
				for ($i = 0; $i < count($arSectionBlock); $i++) {
					unset($arSectionBlock[$i]['SECTION']);
					$arSection['BLOCK'][] = $arSectionBlock[$i];
				}
			}

			$arSection['URL'] = $arSection['URL_ORIGINAL'];
			$arSection['URL'] = respectiveURL($arSection, 'section');

			return $arSection;
		}
		$this->Error = 'Раздел не найден';

		return false;
	}

	function Rows1($nSite)
	{
		global $DB;
		$arRows = $DB->Rows("SELECT * FROM `k2_section` WHERE `SITE` = '".$nSite."' ORDER BY `SORT` ASC");

		return $arRows;
	}

	function Rows($arFilter = array(), $arOrderBy = array(), $arSelect = array())
	{
		global $DB, $LIB;

		if (!$arOrderBy['SORT']) {
			$arOrderBy['SORT'] = 'ASC';
		}

		$arCFilter = array('FROM' => 'k2_section', 'WHERE' => $arFilter, 'ORDER_BY' => $arOrderBy, 'SELECT' => $arSelect);

		$arList = $DB->Rows($DB->CSQL($arCFilter));
		for ($i = 0; $i < count($arList); $i++) {
			$arList[$i]['URL'] = respectiveURL($arList[$i], 'section');
		}

		return $arList;
	}

	function Correct($arPar, $arSection = array())
	{
		global $DB, $LIB;
		$arPar += $arSection;
		if (isset($arPar['NAME']) && !strlen($arPar['NAME'])) {
			$this->Error = changeMessage('Название');

			return false;
		}
		if (isset($arPar['FOLDER'])) {
			if (!strlen($arPar['FOLDER'])) {
				$this->Error = changeMessage('Папка');

				return false;
			}
			if (!preg_match("#^[a-z0-9\-_]+$#", $arPar['FOLDER'])) {
				$this->Error = 'В названии папки допускаются латинские буквы, цифры, тире и нижнее подчеркивание';

				return false;
			}
		}
		$arPar['URL_ORIGINAL'] = '/'.$arPar['FOLDER'].'/';
		if ($arPar['ID']) {
			$arBackPath = $this->Back($arPar['ID']);
			$arPar['URL_ORIGINAL'] = '/'.$arPar['FOLDER'].'/';
			if ($arBackPath[count($arBackPath) - 2]['URL_ORIGINAL']) {
				$arPar['URL_ORIGINAL'] = $arBackPath[count($arBackPath) - 2]['URL_ORIGINAL'].$arPar['FOLDER'].'/';
			}
		} else {
			if ($arPar['PARENT']) {
				if ($arParentSection = $this->ID($arPar['PARENT'])) {
					$arPar['URL_ORIGINAL'] = $arParentSection['URL_ORIGINAL'].$arPar['FOLDER'].'/';
				}
			}
		}
		if ($DB->Rows("SELECT * FROM `k2_section` WHERE `SITE` = '".(int)$arPar['SITE']."' AND `URL_ORIGINAL` = '".DBS($arPar['URL_ORIGINAL'])."'".($arPar['ID'] ? " AND ID != '".$arPar['ID']."'" : ""))) {
			$this->Error = 'Раздел с такой папкой уже существует';

			return false;
		}

		return $arPar;
	}

	function Add($nSite, $arPar = array())
	{
		global $LIB, $DB, $USER;

		if (!$arSite = $LIB['SITE']->ID($nSite)) {
			return false;
		}
		$arPar['SITE'] = $arSite['ID'];

		if (empty($arPar['NAME'])) {
			$this->Error = changeMessage('Название');

			return false;
		}
		if (empty($arPar['FOLDER'])) {
			$this->Error = changeMessage('Папка');

			return false;
		}
		$arPar['FOLDER'] = strtolower(trim($arPar['FOLDER']));
		if (!preg_match("#^[a-z0-9\-_]+$#", $arPar['FOLDER'])) {
			$this->Error = 'В названии папки допускаются латинские буквы, цифры, тире и нижнее подчеркивание';

			return false;
		}

		$sFullPath = '/'.$arPar['FOLDER'].'/';

		if ($arPar['PARENT']) {
			if ($arParentSection = $this->ID($arPar['PARENT'])) {
				$arPar['PERMISSION'] = $arParentSection['PERMISSION'];
				$arPar['DESIGN_SHOW'] = $arParentSection['DESIGN_SHOW'];
				$sFullPath = $arParentSection['URL_ORIGINAL'].$arPar['FOLDER'].'/';
			}
		} elseif (!$arPar['DESIGN']) {
			$arPar['DESIGN_SHOW'] = $arSite['DESIGN'];
		}

		if (!$arPar['DESIGN_SHOW']) {
			$arPar['DESIGN_SHOW'] = $arPar['DESIGN'];
		}

		if ($DB->Rows("SELECT `ID` FROM `k2_section` WHERE `SITE` = '".(int)$nSite."' AND `URL_ORIGINAL` = '".DBS($sFullPath)."'")) {
			$this->Error = 'Такая папка уже существует';

			return false;
		}

		if ($arPar['URL_ALTERNATIVE'] && !$LIB['URL']->Check($arPar['URL_ALTERNATIVE'])) {
			$this->Error = $LIB['URL']->Error;

			return false;
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2_section', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($sError = $LIB['EVENT']->Execute('BEFORE_ADD_SECTION', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		$nLevel = count(explode('/', $sFullPath)) - 3;
		$nSort = 10;
		if ($arSection = $DB->Rows("SELECT `SORT` FROM `k2_section` WHERE `PARENT` = '".(int)$arPar['PARENT']."' ORDER BY `SORT` DESC LIMIT 1")) {
			$nSort = $arSection[0]['SORT'] + 10;
		}
		if ($nID = $DB->Insert("
		INSERT INTO `k2_section` (
			`ACTIVE`,
			`NAME`,
			`SORT`,
			`SITE`,
			`PARENT`,
			`FOLDER`,
			`URL_ORIGINAL`,
			`URL_REDIRECT`,
			`URL_ALTERNATIVE`,
			`LEVEL`,
			`DESIGN`,
			`DESIGN_SHOW`,
			`PERMISSION`,
			`SEO_TITLE`,
			`SEO_KEYWORD`,
			`SEO_DESCRIPTION`
		) VALUES (
			'".(int)$arPar['ACTIVE']."',
			'".DBS($arPar['NAME'])."',
			'".(int)$nSort."',
			'".(int)$arPar['SITE']."',
			'".(int)$arPar['PARENT']."',
			'".DBS($arPar['FOLDER'])."',
			'".DBS($sFullPath)."',
			'".DBS($arPar['URL_REDIRECT'])."',
			'".DBS($arPar['URL_ALTERNATIVE'])."',
			'".$nLevel."',
			'".(int)$arPar['DESIGN']."',
			'".(int)$arPar['DESIGN_SHOW']."',
			'".(int)$arPar['PERMISSION']."',
			'".DBS($arPar['SEO_TITLE'])."',
			'".DBS($arPar['SEO_KEYWORD'])."',
			'".DBS($arPar['SEO_DESCRIPTION'])."'
		)")
		) {

			if ($USER['USER_GROUP'] != 1) {
				$arPermission = $USER['PERMISSION']['SECTION'];
				if ($USER['PERMISSION']['SECTION'][$arPar['PARENT']]) {
					$arPermission[$nID] = $USER['PERMISSION']['SECTION'][$arPar['PARENT']];
				}
				$LIB['USER_GROUP']->Edit($USER['USER_GROUP'], array('PERMISSION_SECTION' => $arPermission));
			}

			$arPar['ID'] = $nID;

			$LIB['URL']->Add(array('URL' => $arPar['URL_ALTERNATIVE'], 'SITE' => $arPar['SITE'], 'SECTION' => $arPar['ID']));
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_section'), $arPar);
			$LIB['EVENT']->Execute('AFTER_ADD_SECTION', $arPar);

			return $nID;
		}

		return false;
	}

	function Edit($nID, $arPar = array(), $bFull = 0)
	{
		global $LIB, $DB;

		if (!$arSection = $this->ID($nID)) {
			return false;
		}
		if (!$arSite = $LIB['SITE']->ID($arSection['SITE'])) {
			return false;
		}

		if (!$bFull) {
			$arPar += $arSection;
		}

		if (!$arPar = $this->Correct($arPar, $arSection)) {
			return false;
		}

		if ($arPar['URL_ALTERNATIVE'] && !$LIB['URL']->Check($arPar['URL_ALTERNATIVE'], array('SECTION' => $nID))) {
			$this->Error = $LIB['URL']->Error;

			return false;
		}

		$arPar['URL_ORIGINAL'] = '/'.$arPar['FOLDER'].'/';

		if ($arPar['PARENT'] && $arParentSection = $this->ID($arPar['PARENT'])) {
			$arPar['URL_ORIGINAL'] = $arParentSection['URL_ORIGINAL'].$arPar['FOLDER'].'/';
			$arPar['LEVEL'] = count(explode('/', $arPar['URL_ORIGINAL'])) - 3;
			if ($DB->Rows("SELECT `ID` FROM `k2_section` WHERE `ID` != '".$nID."' AND `SITE` = '".$arPar['SITE']."' AND `URL_ORIGINAL` = '".DBS($sFullPath)."'")) {
				$this->Error = 'Такая папка уже существует';

				return false;
			}
			$arPar['DESIGN_SHOW'] = $arParentSection['DESIGN_SHOW'];
		} else {
			$arSite = $LIB['SITE']->ID($arSection['SITE']);
			$arPar['DESIGN_SHOW'] = $arSite['DESIGN'];
		}

		if ($arPar['DESIGN']) {
			$arPar['DESIGN_SHOW'] = $arPar['DESIGN'];
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2_section', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($sError = $LIB['EVENT']->Execute('BEFORE_EDIT_SECTION', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($DB->Query("UPDATE
        	`k2_section`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`NAME` = '".DBS($arPar['NAME'])."',
			`SORT` = '".(int)$arPar['SORT']."',
			`PARENT` = '".(int)$arPar['PARENT']."',
			`FOLDER` = '".DBS($arPar['FOLDER'])."',
			`URL_ORIGINAL` = '".DBS($arPar['URL_ORIGINAL'])."',
			`URL_REDIRECT` = '".DBS($arPar['URL_REDIRECT'])."',
			`URL_ALTERNATIVE` = '".DBS($arPar['URL_ALTERNATIVE'])."',
			`LEVEL` = '".(int)$arPar['LEVEL']."',
			`DESIGN` = '".(int)$arPar['DESIGN']."',
			`DESIGN_SHOW` = '".(int)$arPar['DESIGN_SHOW']."',
			`PERMISSION` = '".(int)$arPar['PERMISSION']."',
			`SEO_TITLE` = '".DBS($arPar['SEO_TITLE'])."',
			`SEO_KEYWORD` = '".DBS($arPar['SEO_KEYWORD'])."',
			`SEO_DESCRIPTION` = '".DBS($arPar['SEO_DESCRIPTION'])."'
        WHERE
        	ID = '".$nID."';
        ")
		) {
			$LIB['FIELD']->Update(array('ID' => $nID, 'TABLE' => 'k2_section'), $arPar);
			unset($_FILES);

			$arList = $this->Child($nID);
			for ($i = 0; $i < count($arList); $i++) {
				$this->Edit($arList[$i]['ID'], $arList[$i]);
			}

			$arParCopy = $arPar;
			$arParCopy['SECTION'] = $nID;
			$arParCopy['URL'] = $arParCopy['URL_ALTERNATIVE'];
			$arParCopy['OLD_URL'] = $arSection['URL_ALTERNATIVE'];
			$LIB['URL']->Add($arParCopy);

			$LIB['EVENT']->Execute('AFTER_EDIT_SECTION', $arPar);

			return $nID;
		}

		return false;
	}

	function Delete($nID)
	{
		global $LIB, $DB;

		if (!$arSection = $this->ID($nID)) {
			return false;
		}

		$LIB['EVENT']->Execute('BEFORE_DELETE_SECTION', $arSection);

		$arList = $this->Child($nID, 1);
		$arList[] = $arSection;

		for ($i = 0; $i < count($arList); $i++) {
			$arSBlock = $LIB['SECTION_BLOCK']->Rows($arList[$i]['ID']);
			for ($n = 0; $n < count($arSBlock); $n++) {
				$LIB['SECTION_BLOCK']->Delete($arSBlock[$n]['ID']);
			}
			$LIB['FIELD']->DeleteContent(array('TABLE' => 'k2_section', 'ELEMENT' => $arList[$i]['ID']));
			$LIB['URL']->Delete($arList[$i]['URL_ALTERNATIVE']);
			$DB->Query("DELETE FROM `k2_section` WHERE `ID` = '".$arList[$i]['ID']."'");
			rmdir($_SERVER['DOCUMENT_ROOT'].'/files/section/'.$nID);
		}

		$LIB['EVENT']->Execute('AFTER_DELETE_SECTION', $arSection);

		return true;
	}

	function Child($nParent, $bRecursive = false, $arList = array())
	{
		global $DB, $LIB;

		$arCFilter = array('FROM' => 'k2_section', 'WHERE' => array('PARENT' => $nParent), 'ORDER_BY' => array('SORT' => 'ASC'));

		if ($arSection = $DB->Rows($DB->CSQL($arCFilter))) {
			for ($i = 0; $i < count($arSection); $i++) {
				$arSection[$i]['URL'] = respectiveURL($arSection[$i], 'section');

				$arList[] = $arSection[$i];
				if ($bRecursive) {
					$arList = $this->Child($arSection[$i]['ID'], $bRecursive, $arList);
				}
			}
		}

		return $arList;
	}

	function Back($nID, $arList = array())
	{
		global $DB, $LIB;
		if ($arSection = $DB->Row("SELECT * FROM `k2_section` WHERE `ID` = '".(int)$nID."'")) {
			$arSection['URL'] = respectiveURL($arSection, 'section');
			$arList[] = $arSection;
			if ($arSection['PARENT']) {
				$arList = $this->Back($arSection['PARENT'], $arList);
			} else {
				$arList = array_reverse($arList);
			}
		}

		return $arList;
	}

	function Map($arPar)
	{
		global $DB;

		$arCFilter = array('FROM' => 'k2_section', 'WHERE' => $arPar, 'ORDER_BY' => array('SORT' => 'ASC'));

		return $this->MapChild($DB->Rows($DB->CSQL($arCFilter)));
	}

	function MapChild($arSections, $nParent = 0, $arChild = array())
	{
		if (!$arSections) {
			return $arChild;
		}

		foreach ($arSections as $arSection) {
			if ($arSection['PARENT'] != $nParent) {
				//$arNewList[] = $arSection;
				continue;
			}

			$arSection['URL'] = respectiveURL($arSection, 'section');
			$arChild[] = $arSection;

			$arChild = $this->MapChild($arSections, $arSection['ID'], $arChild);
		}

		return $arChild;
	}
}

?>