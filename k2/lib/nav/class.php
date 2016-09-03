<?
class Nav
{
	function Rows()
	{
		global $DB;
		$arNav = $DB->Rows("SELECT * FROM `k2_nav` ORDER BY ID ASC");

		return $arNav;
	}

	function ID($nID)
	{
		global $LIB, $DB;
		if ($arNav = $DB->Row("SELECT * FROM `k2_nav` WHERE `ID` = '".$nID."'")) {
			$arNav['TEMPLATE'] = $LIB['FILE']->Read('/k2/dev/nav/'.$nID.'/template.php');

			return $arNav;
		}
		$this->Error = 'Шаблон навигации не найден';

		return false;
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;

		if ($sError = formCheck(array('NAME' => 'Название'))) {
			$this->Error = $sError;

			return false;
		}

		if ($nID = $DB->Insert("
			INSERT INTO `k2_nav` (
				`NAME`
			)VALUES(
				'".DBS($arPar['NAME'])."'
			);
			")
		) {
			$arExs[] = $LIB['FILE']->Create('/k2/dev/nav/'.$nID.'/template.php', $arPar['TEMPLATE']);
			if (in_array('', $arExs)) {
				$DB->Query("DELETE FROM `k2_nav` WHERE ID = '".$nID."'");
				$this->Error = $LIB['FILE']->Error;
			} else {
				return $nID;
			}
		}

		return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB;

		if (!$arNav = $this->ID($nID)) {
			return false;
		}

		if ($sError = formCheck(array('NAME' => 'Название'))) {
			$this->Error = $sError;

			return false;
		}

		if ($DB->Query("UPDATE `k2_nav`
	        SET
				`NAME` = '".DBS($arPar['NAME'])."'
	        WHERE
	            `ID` = '".$nID."';
	        ")
		) {
			$arExs[] = $LIB['FILE']->Edit('/k2/dev/nav/'.$nID.'/template.php', $arPar['TEMPLATE']);
			if (in_array('', $arExs)) {
				$this->Error = $LIB['FILE']->Error;

				return false;
			} else {
				return $nID;
			}
		}

		return false;
	}

	function Delete($nID)
	{
		global $LIB, $DB;
		unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev/nav/'.$nID.'/template.php');
		rmdir($_SERVER['DOCUMENT_ROOT'].'/k2/dev/nav/'.$nID);
		$LIB['FILE']->Delete('/k2/dev/nav/'.$nID.'/');
		$DB->Query("DELETE FROM `k2_nav` WHERE ID = '".$nID."'");

		return true;
	}

	function Page($nTemplate, $bReturn = false)
	{
		global $LIB, $MOD, $DB, $CURRENT, $USER;

		$this->Setting['CURRENT'] = (int)$_GET['page'];
		if (!$this->Setting['CURRENT']) {
			$this->Setting['CURRENT'] = 1;
		}

		$arList = array();
		$bCurrent = 0;

		if ($this->Setting['TOTAL'] && $this->Setting['SIZE']) {
			for ($i = 0; $i < ceil(($this->Setting['TOTAL']) / $this->Setting['SIZE']); $i++) {
				if ($i) {
					$arList[$i]['URL'] = urlQuery(array('page' => ($i + 1)));
				} else {
					$arList[$i]['URL'] = urlQuery(array(), array('page'));
				}

				if ($this->Setting['CURRENT'] == $i + 1) {
					$arList[$i]['CURRENT'] = 1;
					$bCurrent = 1;
				}
			}
			if (!$bCurrent) {
				$arList[0]['CURRENT'] = 1;
			}
			if (count($arList) < 2) {
				$arList = array();
			}
		}

		$this->Setting['PAGES'] = 0;
		if ($arList) {
			$this->Setting['PAGES'] = count($arList);
			unset($nNum, $bCurrent);

			if ($bReturn) {
				ob_start();
				include($_SERVER['DOCUMENT_ROOT'].'/k2/dev/nav/'.(int)$nTemplate.'/template.php');
				$sCont = ob_get_contents();
				ob_clean();

				return $sCont;
			}
			include($_SERVER['DOCUMENT_ROOT'].'/k2/dev/nav/'.(int)$nTemplate.'/template.php');
		}
	}

	function Back($nTemplate)
	{
		?><!-- $NAV<?=(int)$nTemplate?>$ --><?
		$GLOBALS['NAV'][] = $nTemplate;
	}

	function BackAdd($nTemplate, $arPar)
	{
		$GLOBALS['NAV_ADD'][$nTemplate][] = $arPar;
	}

	function BackResult($nTemplate)
	{
		global $LIB, $MOD, $DB, $CURRENT;

		$arList = $LIB['SECTION']->Back($CURRENT['SECTION']['ID']);
		for ($i = 0; $i < count($GLOBALS['NAV_ADD'][$nTemplate]); $i++) {
			$arList[] = array('NAME' => $GLOBALS['NAV_ADD'][$nTemplate][$i][0], 'URL' => $GLOBALS['NAV_ADD'][$nTemplate][$i][1]);
		}

		for ($i = 0; $i < count($arList); $i++) {
			$arList[$i]['CURRENT'] = ($CURRENT['SECTION']['URL'] == $arList[$i]['URL']);
		}

		ob_start();
		include($_SERVER['DOCUMENT_ROOT'].'/k2/dev/nav/'.(int)$nTemplate.'/template.php');
		$sCont = ob_get_contents();
		ob_end_clean();

		return $sCont;
	}

	function Menu($nTemplate, $arFilter = array(), $bChild = false)
	{
		global $LIB, $MOD, $DB, $CURRENT, $USER;

		if ($bChild) {
			$arCFilter = array('FROM' => 'k2_section', 'WHERE' => array('+SQL' => "`PARENT` > 0"), 'ORDER_BY' => array('SORT' => 'ASC'));
			$arPopList = $DB->Rows($DB->CSQL($arCFilter));
			for ($i = 0; $i < count($arPopList); $i++) {
				$arPopList[$i]['URL'] = respectiveURL($arPopList[$i], 'section');
				$arPopList[$i]['CURRENT'] = (strpos($CURRENT['SECTION']['URL'], $arPopList[$i]['URL']) !== false);
				$arChild[$arPopList[$i]['PARENT']][] = $arPopList[$i];
				if ($arPopList[$i]['CURRENT']) {
					$nChildActive = $arPopList[$i]['PARENT'];
				}
			}
		}

		if (!$arFilter['PARENT']) {
			$arPar['PARENT'] = 0;
		}

		$arBackID = array();
		$arBack = $LIB['SECTION']->Back($CURRENT['SECTION']['ID']);
		for ($i = 0; $i < count($arBack); $i++) {
			$arBackID[] = $arBack[$i]['ID'];
		}

		$arFilter = array('FROM' => 'k2_section', 'WHERE' => $arFilter, 'ORDER_BY' => array('SORT' => 'ASC'));
		$arList = $DB->Rows($DB->CSQL($arFilter));
		for ($i = 0; $i < count($arList); $i++) {
			$arList[$i]['URL'] = respectiveURL($arList[$i], 'section');
			$arList[$i]['CURRENT'] = in_array($arList[$i]['ID'], $arBackID);
			if ($bChild && $arChild[$arList[$i]['ID']]) {
				$arList[$i]['CHILDREN'] = $arChild[$arList[$i]['ID']];
				if ($nChildActive == $arList[$i]['ID']) {
					$arList[$i]['CURRENT'] = 1;
				}
			}
		}

		include($_SERVER['DOCUMENT_ROOT'].'/k2/dev/nav/'.(int)$nTemplate.'/template.php');
	}
}
?>