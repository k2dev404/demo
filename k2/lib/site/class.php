<?

class Site
{
	function Rows()
	{
		global $DB;

		return $DB->Rows("SELECT * FROM `k2_site` ORDER BY `ID` ASC");
	}

	function ID($nID)
	{
		global $DB;
		if ($arSite = $DB->Row("SELECT * FROM `k2_site` WHERE `ID` = '".(int)$nID."'")) {
			return $arSite;
		}
		$this->Error = 'Сайт не найден';

		return false;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;

		if ($sError = formCheck(array('NAME' => 'Название', 'DESIGN' => 'Шаблон дизайна'), $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($arPar['DOMAIN']) {
			if ($DB->Rows("SELECT ID FROM `k2_site` WHERE `DOMAIN` LIKE '".DBS($arPar['DOMAIN'])."'")) {
				$this->Error = 'Сайт с таким доменом уже существует';

				return false;
			}
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2_site', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($nID = $DB->Insert("
		INSERT INTO `k2_site`(
			`ACTIVE`,
			`NAME`,
			`DOMAIN`,
			`ALIAS`,
			`DESIGN`
		)VALUES(
			".(int)$arPar['ACTIVE'].", '".DBS($arPar['NAME'])."', '".DBS($arPar['DOMAIN'])."', '".DBS($arPar['ALIAS'])."', '".(int)$arPar['DESIGN']."'
		)")
		) {
			$arDesign = $LIB['DESIGN']->Rows();
			$LIB['FIELD']->Update(array('TABLE' => 'k2_site', 'ID' => $nID), $arPar);
			$arPar = $this->ID($nID);
			$arPar['SECTION_INDEX'] = $LIB['SECTION']->Add($nID, array('NAME' => 'Начальная страница', 'FOLDER' => 'index', 'DESIGN' => $arDesign[0]['ID']));
			$arPar['SECTION_NOT_FOUND'] = $LIB['SECTION']->Add($nID, array('NAME' => 'Страница не найдена', 'FOLDER' => 'page-not-found', 'DESIGN' => $arDesign[0]['ID']));
			$this->Edit($nID, $arPar);

			return $nID;
		}

		return false;
	}

	function Edit($nID, $arPar = array(), $bFull = 0)
	{
		global $LIB, $DB;

		if (!$arSite = $this->ID($nID)) {
			return false;
		}

		if (!$bFull) {
			$arPar += $arSite;
		}

		if ($sError = formCheck(array('NAME' => 'Название', 'DESIGN' => 'Шаблон дизайна', 'SECTION_INDEX' => 'Начальная страница', 'SECTION_NOT_FOUND' => 'Страница 404'), $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($arPar['DOMAIN']) {
			if ($DB->Rows("SELECT ID FROM `k2_site` WHERE `DOMAIN` LIKE '".DBS($arPar['DOMAIN'])."' AND ID != '".(int)$nID."'")) {
				$this->Error = 'Сайт с таким доменом уже существует';

				return false;
			}
		}

		if ($sError = $LIB['FIELD']->CheckAll('k2_site', $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if ($DB->Query("UPDATE `k2_site`
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`NAME` = '".DBS($arPar['NAME'])."',
			`DOMAIN` = '".DBS($arPar['DOMAIN'])."',
			`ALIAS` = '".DBS($arPar['ALIAS'])."',
			`DESIGN` = '".(int)$arPar['DESIGN']."',
			`SECTION_INDEX` = '".(int)$arPar['SECTION_INDEX']."',
			`SECTION_NOT_FOUND` = '".(int)$arPar['SECTION_NOT_FOUND']."',
			`PERMISSION` = '".(int)$arPar['PERMISSION']."'
        WHERE
        	ID = '".$nID."';
        ")
		) {
			$LIB['FIELD']->Update(array('TABLE' => 'k2_site', 'ID' => $nID), $arPar);
			$arSelect = $DB->Rows("SELECT ID FROM `k2_section` WHERE `LEVEL` = 0");
			for ($i = 0; $i < count($arSelect); $i++) {
				$LIB['SECTION']->Edit($arSelect[$i]['ID'], array());
			}

			return true;
		}

		return false;
	}

	function Delete($nID)
	{
		global $LIB, $DB, $USER;
		$DB->Query("DELETE FROM `k2_site` WHERE `ID` = '".$nID."'");

		return true;
	}
}

?>