<?

class Block
{
	function ID($nID, $bField = false, $bTemplate = false)
	{
		global $LIB, $DB;

		if ($arBlock = $DB->Row("SELECT * FROM `k2_block` WHERE `ID` = '".$nID."'")) {
			if ($bField) {
				$arBlock['ELEMENT_FIELD'] = $LIB['FIELD']->Rows('k2_block'.$nID);
				$arBlock['CATEGORY_FIELD'] = $LIB['FIELD']->Rows('k2_block'.$nID.'category');
			}
			if ($bTemplate) {
				$arBlock['CONTROLLER'] = $LIB['FILE']->Read('/k2/dev/block/'.$nID.'/controller.php');
				$arBlock['TEMPLATE'] = $LIB['FILE']->Read('/k2/dev/block/'.$nID.'/template.php');
				$arBlock['TEMPLATE_FULL'] = $LIB['FILE']->Read('/k2/dev/block/'.$nID.'/template-full.php');
				$arTemplate = $LIB['TEMPLATE']->Rows(1, $nID);
				for ($i = 0; $i < count($arTemplate); $i++) {
					$arBlock['TEMPLATE_OPHEN'][] = $arTemplate[$i];
					$arBlock['TEMPLATE_OPHEN'][count($arBlock['TEMPLATE_OPHEN']) - 1]['TEMPLATE'] = $LIB['FILE']->Read('/k2/dev/block/'.$arBlock['ID'].'/'.$arTemplate[$i]['FILE']);
				}
			}

			return $arBlock;
		}

		$this->Error = Lang('BLOCK_NOT_FOUND');

		return false;
	}

	function Rows($nGroup = 0)
	{
		global $DB;

		if ($nGroup) {
			return $DB->Rows("SELECT * FROM `k2_block` WHERE `BLOCK_GROUP` = '".(int)$nGroup."' ORDER BY `ID` ASC");
		} else {
			return $DB->Rows("SELECT * FROM `k2_block` ORDER BY `ID` ASC");
		}
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;

		if ($sError = formCheck(array('NAME' => 'Название', 'BLOCK_GROUP' => 'Группа'), $arPar)) {
			$this->Error = $sError;

			return false;
		}
		if ($nID = $DB->Insert("
		INSERT INTO `k2_block`(
			`NAME`,
			`BLOCK_GROUP`,
			`CATEGORY`,
			`FORM_EDIT_ELEMENT`
		)VALUES(
			'".DBS($arPar['NAME'])."', '".(int)$arPar['BLOCK_GROUP']."', '".(int)$arPar['CATEGORY']."', '".DBS($arPar['FORM_EDIT_ELEMENT'])."'
		);
		")
		) {
			$arExs[] = $LIB['FILE']->Create('/k2/dev/block/'.$nID.'/controller.php', $arPar['CONTROLLER']);
			$arExs[] = $LIB['FILE']->Create('/k2/dev/block/'.$nID.'/template.php', $arPar['TEMPLATE']);
			$arExs[] = $LIB['FILE']->Create('/k2/dev/block/'.$nID.'/template-full.php', $arPar['TEMPLATE_FULL']);
			if (in_array('', $arExs)) {
				$DB->Query("DELETE FROM `k2_block` WHERE ID = '".$nID."'");
				$this->Error = $LIB['FILE']->Error;
			} else {
				if ($DB->Query("CREATE TABLE `k2_block".$nID."` (
					`ID` int(11) NOT NULL AUTO_INCREMENT,
					`DATE_CREATED` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					`DATE_CHANGE` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					`USER_CREATED` int(11) NOT NULL,
					`USER_CHANGE` int(11) NOT NULL,
					`ACTIVE` tinyint(1) NOT NULL DEFAULT '0',
					`SORT` int(11) NOT NULL,
					`SECTION` int(11) NOT NULL,
					`SECTION_BLOCK` int(11) NOT NULL,
					`CATEGORY` int(11) NOT NULL,
					`URL_ALTERNATIVE` varchar(255) NOT NULL,
					`SEO_TITLE` varchar(255) NOT NULL,
					`SEO_KEYWORD` varchar(255) NOT NULL,
					`SEO_DESCRIPTION` text NOT NULL,
					PRIMARY KEY (`ID`)
				)ENGINE=InnoDB DEFAULT CHARSET=utf8;")
					&& $DB->Query("CREATE TABLE `k2_block".$nID."category` (
					`ID` int(11) NOT NULL auto_increment,
					`DATE_CREATED` datetime NOT NULL default '0000-00-00 00:00:00',
					`DATE_CHANGE` datetime NOT NULL default '0000-00-00 00:00:00',
					`USER_CREATED` int(11) NOT NULL,
					`USER_CHANGE` int(11) NOT NULL,
					`SECTION` int(11) NOT NULL,
					`SECTION_BLOCK` int(11) NOT NULL,
					`PARENT` int(11) NOT NULL,
					`ACTIVE` tinyint(1) NOT NULL,
					`SORT` int(11) NOT NULL,
					`NAME` varchar(255) NOT NULL,
					`URL_ALTERNATIVE` varchar(255) NOT NULL,
					`SEO_TITLE` varchar(255) NOT NULL,
					`SEO_KEYWORD` varchar(255) NOT NULL,
					`SEO_DESCRIPTION` text NOT NULL,
					PRIMARY KEY  (`ID`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8")
				) {
					for ($i = 0; $i < count($arPar['TEMPLATE_OPHEN']); $i++) {
						$arPar['TEMPLATE_OPHEN'][$i]['OBJECT'] = 1;
						$arPar['TEMPLATE_OPHEN'][$i]['OBJECT_ID'] = $nID;
						$LIB['TEMPLATE']->Add($arPar['TEMPLATE_OPHEN'][$i]);
					}

					return $nID;
				}
			}
		}

		return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB, $USER;

		if (!$arBlock = $this->ID($nID)) {
			return false;
		}
		if ($sError = formCheck(array('NAME' => 'Название', 'BLOCK_GROUP' => 'Группа'))) {
			$this->Error = $sError;

			return false;
		}
		if ($DB->Query("UPDATE k2_block
        SET
			`NAME` = '".DBS($arPar['NAME'])."',
			`BLOCK_GROUP` = '".(int)$arPar['BLOCK_GROUP']."',
			`CATEGORY` = '".DBS($arPar['CATEGORY'])."',
			`FORM_EDIT_ELEMENT` = '".DBS($arPar['FORM_EDIT_ELEMENT'])."'
        WHERE
        	`ID` = '".$nID."';
        ")) {
			$arExs[] = $LIB['FILE']->Edit('/k2/dev/block/'.$nID.'/controller.php', $arPar['CONTROLLER']);
			$arExs[] = $LIB['FILE']->Edit('/k2/dev/block/'.$nID.'/template.php', $arPar['TEMPLATE']);
			$arExs[] = $LIB['FILE']->Edit('/k2/dev/block/'.$nID.'/template-full.php', $arPar['TEMPLATE_FULL']);
			if (in_array('', $arExs)) {
				$this->Error = $LIB['FILE']->Error;

				return false;
			} else {
				for ($i = 0; $i < count($arPar['TEMPLATE_OPHEN']); $i++) {
					$LIB['TEMPLATE']->Edit($arPar['TEMPLATE_OPHEN'][$i]['ID'], array('TEMPLATE' => $arPar['TEMPLATE_OPHEN'][$i]['TEMPLATE']));
				}

				return $nID;
			}
		}

		return false;
	}

	function Delete($nID)
	{
		global $LIB, $DB, $USER;

		if (!$arBlock = $this->ID($nID, false, true)) {
			return false;
		}

		$arSBlock = $DB->Rows("SELECT ID FROM `k2_section_block` WHERE `BLOCK` = '".$arBlock['ID']."'");
		for ($i = 0; $i < count($arSBlock); $i++) {
			$LIB['SECTION_BLOCK']->Delete($arSBlock[$i]['ID']);
		}

		for ($i = 0; $i < count($arBlock['TEMPLATE_OPHEN']); $i++) {
			$LIB['TEMPLATE']->Delete($arBlock['TEMPLATE_OPHEN'][$i]['ID']);
		}

		unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev/block/'.$arBlock['ID'].'/controller.php');
		unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev/block/'.$arBlock['ID'].'/template.php');
		unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev/block/'.$arBlock['ID'].'/template-full.php');
		$DB->Query("DELETE FROM `k2_block` WHERE `ID` = '".$arBlock['ID']."'");
		$DB->Query("DROP TABLE `k2_block".$arBlock['ID']."`");
		$DB->Query("DROP TABLE `k2_block".$arBlock['ID']."category`");
		$DB->Query("DROP TABLE `k2_section_setting` WHERE `BLOCK` = '".$arBlock['ID']."'");
		$DB->Query("DELETE FROM `k2_field` WHERE `TABLE` = 'k2_block".$arBlock['ID']."' OR `TABLE` = 'k2_block".$arBlock['ID']."category'");
		rmdir($_SERVER['DOCUMENT_ROOT'].'/k2/dev/block/'.$arBlock['ID']);

		return true;
	}

	function Export($nID)
	{
		global $LIB, $SYSTEM;

		if (!$arBlock = $this->ID($nID, true, true)) {
			return false;
		}
		$arExport['VERSION'] = $SYSTEM['VERSION'];
		$arExport['VERSION_KEY'] = $SYSTEM['VERSION_KEY'];
		$arExport['BLOCK'] = $arBlock;

		unset($arExport['BLOCK']['ID'], $arExport['BLOCK']['TEMPLATE'], $arExport['BLOCK']['TEMPLATE_FULL'], $arExport['BLOCK']['BLOCK_GROUP']);
		for ($i = 0; $i < count($arExport['BLOCK']['TEMPLATE_OPHEN']); $i++) {
			unset($arExport['BLOCK']['TEMPLATE_OPHEN'][$i]['ID'], $arExport['BLOCK']['TEMPLATE_OPHEN'][$i]['TEMPLATE'], $arExport['BLOCK']['TEMPLATE_OPHEN'][$i]['OBJECT'], $arExport['BLOCK']['TEMPLATE_OPHEN'][$i]['OBJECT_ID']);
		}
		$sContent = serialize($arExport);

		$sPath = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/block/'.$arBlock['ID'].'/';
		$sZipFile = $_SERVER['DOCUMENT_ROOT'].'/tmp/k2block'.md5(microtime()).'.zip';

		@unlink($sZipFile);
		$arFile = dirList($sPath);
		$zip = new ZipArchive;
		if ($zip->open($sZipFile, ZIPARCHIVE::CREATE) !== true) {
			$this->Error = 'Не удалось экспортировать компонент';

			return false;
		}
		$zip->addEmptyDir('file');
		for ($i = 0; $i < count($arFile); $i++) {
			if (is_dir($sPath.$arFile[$i])) {
				$zip->addEmptyDir('file/'.$arFile[$i]);
			} else {
				$zip->addFile($sPath.$arFile[$i], 'file/'.$arFile[$i]);
			}
		}
		$zip->addFromString('block.php', $sContent);
		$zip->close();

		return $sZipFile;
	}

	function Import($sPar)
	{
		global $LIB, $SYSTEM;

		if ($sError = formCheck(array('BLOCK_GROUP' => 'Группа'), $sPar)) {
			$this->Error = $sError;

			return false;
		}
		if (!file_exists($sPar['FILE']['tmp_name'])) {
			$this->Error = 'Загрузите файл';

			return false;
		}
		$sDir = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5(microtime()).'/';
		if (!mkdir($sDir, CHMOD_DIR)) {
			$this->Error = 'Не удалось создать временную папку '.$sDir;

			return false;
		}
		if (!unZip($sPar['FILE']['tmp_name'], $sDir)) {
			$this->Error = 'Не удается распаковать архив';
			dirDelete($sDir);

			return false;
		}
		$arBlock = unserialize(file_get_contents($sDir.'block.php'));
		if ($SYSTEM['VERSION_KEY'] != $arBlock['VERSION_KEY']) {
			$this->Error = 'Файл импорта не поддерживается этой версией системы';
			dirDelete($sDir);

			return false;
		}
		$arBlock['BLOCK']['BLOCK_GROUP'] = $sPar['BLOCK_GROUP'];
		if (!($nBlock = $LIB['BLOCK']->Add($arBlock['BLOCK']))) {
			$this->Error = 'Не удалось импортировать функциональный блок';
			dirDelete($sDir);

			return false;
		}
		for ($i = 0; $i < count($arBlock['BLOCK']['ELEMENT_FIELD']); $i++) {
			unset($arBlock['BLOCK']['ELEMENT_FIELD'][$i]['TABLE']);
			if (!$LIB['FIELD']->Add('k2_block'.$nBlock, $arBlock['BLOCK']['ELEMENT_FIELD'][$i])) {
				$bError = true;
			}
		}
		for ($i = 0; $i < count($arBlock['BLOCK']['CATEGORY_FIELD']); $i++) {
			unset($arBlock['BLOCK']['CATEGORY_FIELD'][$i]['TABLE']);
			if (!$LIB['FIELD']->Add('k2_block'.$nBlock.'category', $arBlock['BLOCK']['CATEGORY_FIELD'][$i])) {
				$bError = true;
			}
		}
		if ($bError) {
			$this->Delete($nBlock);
			$this->Error = 'Не удалось обработать файл';
			dirDelete($sDir);

			return false;
		}
		dirCopy($sDir.'file/', $_SERVER['DOCUMENT_ROOT'].'/k2/dev/block/'.$nBlock.'/');
		dirDelete($sDir);

		return $nBlock;
	}
}

?>