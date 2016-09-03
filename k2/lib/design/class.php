<?

class Design
{
	function Rows()
	{
		global $DB;
		$arDesign = $DB->Rows("SELECT * FROM `k2_design`");

		return $arDesign;
	}

	function ID($nID, $bFullInfo = 0)
	{
		global $LIB, $DB;
		if ($arDesign = $DB->Rows("SELECT * FROM `k2_design` WHERE `ID` = '".$nID."'")) {
			if ($bFullInfo) {
				$arDesign[0]['HEADER'] = $LIB['FILE']->Read('/k2/dev/design/'.$nID.'/header.php');
				$arDesign[0]['FOOTER'] = $LIB['FILE']->Read('/k2/dev/design/'.$nID.'/footer.php');
			}

			return $arDesign[0];
		}
		$this->Error = 'Макет дизайна не найден';

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
		INSERT INTO `k2_design`(
			`NAME`
		)VALUES(
			'".DBS($arPar['NAME'])."'
		);
		")
		) {
			$arExs[] = $LIB['FILE']->Create('/k2/dev/design/'.$nID.'/header.php', $arPar['HEADER']);
			$arExs[] = $LIB['FILE']->Create('/k2/dev/design/'.$nID.'/footer.php', $arPar['FOOTER']);
			if (in_array('', $arExs)) {
				$DB->Query("DELETE FROM `k2_design` WHERE ID = '".$nID."'");
				$this->Error = $LIB['FILE']->Error;

				return false;
			} else {
				return $nID;
			}
		}

		return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB;

		if (!$arDesign = $this->ID($nID)) {
			return false;
		}

		if ($sError = formCheck(array('NAME' => 'Название'))) {
			$this->Error = $sError;

			return false;
		}

		if ($DB->Query("UPDATE `k2_design`
        SET
			`NAME` = '".DBS($arPar['NAME'])."'
        WHERE
        	`ID` = '".$nID."';
        ")
		) {
			$arExs[] = $LIB['FILE']->Edit('/k2/dev/design/'.$nID.'/header.php', $arPar['HEADER']);
			$arExs[] = $LIB['FILE']->Edit('/k2/dev/design/'.$nID.'/footer.php', $arPar['FOOTER']);
			if (in_array(false, $arExs)) {
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
		global $LIB, $DB, $USER;
		unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev/design/'.$nID.'/header.php');
		unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev/design/'.$nID.'/footer.php');
		rmdir($_SERVER['DOCUMENT_ROOT'].'/k2/dev/design/'.$nID);
		$DB->Query("DELETE FROM `k2_design` WHERE ID = '".$nID."'");

		return true;
	}
}

?>