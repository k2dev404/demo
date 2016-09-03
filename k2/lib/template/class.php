<?

class Template
{
	var $Object = array(1 => 'BLOCK', 2 => 'COMPONENT');

	function ID($nID)
	{
		global $LIB, $DB;

		if ($arTemplate = $DB->Row("SELECT * FROM `k2_template` WHERE `ID` = '".$nID."'")) {
			return $arTemplate;
		}

		$this->Error = 'Шаблон не найден';

		return false;
	}

	function Rows($nObject, $nObjectID)
	{
		global $DB;

		return $DB->Rows("SELECT * FROM `k2_template` WHERE `OBJECT` = '".$nObject."' AND `OBJECT_ID` = '".$nObjectID."' ORDER BY `ID` ASC");
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;

		if (!$this->Object[$arPar['OBJECT']]) {
			$this->Error = 'Неправильные параметры';

			return false;
		}
		if (!$LIB[$this->Object[$arPar['OBJECT']]]->ID($arPar['OBJECT_ID'])) {
			$this->Error = $LIB[$this->Object[$arPar['OBJECT']]]->Error;

			return false;
		}
		$arPar['FILE'] = preg_replace("#[^a-z0-9\.\+_\~\@\#\$\%\^\-\_\(\)\{\}\'\`]+#i", '', $arPar['FILE']);
		if ($sError = formCheck(array('NAME' => 'Название', 'FILE' => 'Файл'), $arPar)) {
			$this->Error = $sError;

			return false;
		}
		if (in_array($arPar['FILE'], array('template.php', 'template-full.php'))) {
			$this->Error = 'Задайте другое название файла';

			return false;
		}
		if ($DB->Rows("SELECT ID FROM `k2_template` WHERE `OBJECT` = '".$arPar['OBJECT']."' AND `OBJECT_ID` = '".$arPar['OBJECT_ID']."' AND `FILE` = '".DBS($arPar['FILE'])."'")) {
			$this->Error = 'Задайте другое название файла';

			return false;
		}
		if ($nID = $DB->Insert("
		INSERT INTO `k2_template`(
			`OBJECT`,
			`OBJECT_ID`,
			`NAME`,
			`FILE`
		)VALUES(
			'".$arPar['OBJECT']."', '".$arPar['OBJECT_ID']."', '".DBS($arPar['NAME'])."', '".DBS($arPar['FILE'])."'
		);
		")
		) {
			if (!$LIB['FILE']->Create('/k2/dev/'.strtolower($this->Object[$arPar['OBJECT']]).'/'.$arPar['OBJECT_ID'].'/'.$arPar['FILE'], $arPar['TEMPLATE'])) {
				$DB->Query("DELETE FROM `k2_template` WHERE `ID` = '".$nID."'");
				$this->Error = $LIB['FILE']->Error;

				return false;
			}

			return $nID;
		}

		return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB, $USER;

		if (!$arTemplate = $this->ID($nID)) {
			return false;
		}

		return $LIB['FILE']->Edit('/k2/dev/'.strtolower($this->Object[$arTemplate['OBJECT']]).'/'.$arTemplate['OBJECT_ID'].'/'.$arTemplate['FILE'], $arPar['TEMPLATE']);
	}

	function Delete($nID)
	{
		global $LIB, $DB, $USER;

		if (!$arTemplate = $this->ID($nID)) {
			return false;
		}

		unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev/'.strtolower($this->Object[$arTemplate['OBJECT']]).'/'.$arTemplate['OBJECT_ID'].'/'.$arTemplate['FILE']);
		$DB->Query("DELETE FROM `k2_template` WHERE `ID` = '".$arTemplate['ID']."'");

		return true;
	}

	function Export($nID)
	{
		global $LIB, $SYSTEM;

		if (!$arBlock = $this->ID($nID, true)) {
			return false;
		}
		$arExport['VERSION'] = $SYSTEM['VERSION'];
		$arExport['VERSION_KEY'] = $SYSTEM['VERSION_KEY'];
		$arExport['BLOCK'] = $arBlock;
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
		if (($sPar['FILE']['type'] != 'application/zip') || !file_exists($sPar['FILE']['tmp_name'])) {
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
			$arBlock['BLOCK']['ELEMENT_FIELD'][$i]['SETTING'] = unserialize($arBlock['BLOCK']['ELEMENT_FIELD'][$i]['SETTING']);
			unset($arBlock['BLOCK']['ELEMENT_FIELD'][$i]['TABLE']);
			if (!$LIB['FIELD']->Add(2, $nBlock, $arBlock['BLOCK']['ELEMENT_FIELD'][$i])) {
				$bError = true;
			}
		}
		for ($i = 0; $i < count($arBlock['BLOCK']['CATEGORY_FIELD']); $i++) {
			$arBlock['BLOCK']['CATEGORY_FIELD'][$i]['SETTING'] = unserialize($arBlock['BLOCK']['CATEGORY_FIELD'][$i]['SETTING']);
			unset($arBlock['BLOCK']['CATEGORY_FIELD'][$i]['TABLE']);
			if (!$LIB['FIELD']->Add(4, $nBlock, $arBlock['BLOCK']['CATEGORY_FIELD'][$i])) {
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