<?

class Module
{
	function Rows()
	{
		global $DB;

		return $DB->Rows("SELECT * FROM `k2_module` ORDER BY `SORT` ASC");
	}

	function ID($sModule)
	{
		global $LIB, $DB;

		if ($arModule = $DB->Row("SELECT * FROM `k2_module` WHERE `MODULE` = '".DBS($sModule)."'")) {
			$arModule['SETTING'] = unserialize($arModule['SETTING']);
			$arModule['PERMISSION'] = unserialize($arModule['PERMISSION']);

			return $arModule;
		}
		$this->Error = 'Модуль не найден';

		return false;
	}

	function Edit($sModule, $arPar = array(), $bFull = 1)
	{
		global $LIB, $DB, $USER;

		if (!$arModule = $this->ID($sModule)) {
			return false;
		}
		if ($bFull) {
			$arPar += $arModule;
		}

		if ($DB->Query("UPDATE k2_module
        SET
			`ACTIVE` = '".(int)$arPar['ACTIVE']."',
			`SETTING` = '".DBS(serialize($arPar['SETTING']))."',
			`PERMISSION` = '".DBS(serialize($arPar['PERMISSION']))."'
        WHERE
        	`MODULE` = '".$arModule['MODULE']."';
        ")
		) {
			return true;
		}

		return false;
	}

	function Delete($sModule)
	{
		global $DB, $LIB;

		if (!$arModule = $this->ID($sModule)) {
			return false;
		}

		$sModule = strtolower($arModule['MODULE']);

		$DB->Query("DELETE FROM `k2_module` WHERE `MODULE` = '".$arModule['MODULE']."'");
		$DB->Query("DELETE FROM `k2_permission_default` WHERE `TYPE` = 'MODULE' AND `KEY` = '".$arModule['MODULE']."'");

		dirDelete($_SERVER['DOCUMENT_ROOT'].'/k2/module/'.$sModule.'/');
		dirDelete($_SERVER['DOCUMENT_ROOT'].'/k2/admin/module/'.$sModule.'/');

		return false;
	}

	function Import($sPar)
	{
		global $LIB, $DB, $SYSTEM;

		if (($sPar['FILE']['type'] != 'application/zip') || !file_exists($sPar['FILE']['tmp_name'])) {
			$this->Error = 'Загрузите файл';

			return false;
		}
		$sDir = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5(rand()).'/';
		if (!mkdir($sDir, CHMOD_DIR)) {
			$this->Error = 'Не удалось создать временную папку '.$sDir;

			return false;
		}
		if (!unZip($sPar['FILE']['tmp_name'], $sDir)) {
			$this->Error = 'Не удается распаковать архив';
			dirDelete($sDir);

			return false;
		}

		include_once($sDir.'before.php');
		include_once($sDir.'module.php');

		if ($SYSTEM['VERSION_KEY'] != $arModule['VERSION_KEY']) {
			$this->Error = 'Модуль не поддерживается этой версией системы';
			dirDelete($sDir);

			return false;
		}
		if ($DB->Row("SELECT * FROM `k2_module` WHERE `MODULE` = '".DBS($arModule['NAME'])."'")) {
			$this->Error = 'Модуль уже установлен';
			dirDelete($sDir);

			return false;
		}

		dirCopy($sDir.'file/', $_SERVER['DOCUMENT_ROOT'].'/');

		if (!unZip($sDir.'/file.zip', $_SERVER['DOCUMENT_ROOT'].'/')) {
			$this->Error = 'Не удается распаковать архив';
			dirDelete($sDir);

			return false;
		}
		if (!$DB->Dump($sDir.'db.sql')) {
			$this->Error = 'Не удалось установить модуль';
			dirDelete($sDir);

			return false;
		}
		include_once($sDir.'after.php');
		dirDelete($sDir);

		return $arModule['NAME'];
	}

	function CopyTemplate($sModule, $arPar)
	{
		global $DB, $LIB;

		if (!$arModule = $this->ID($sModule)) {
			return false;
		}
		if ($sError = formCheck(array('NAME' => 'Название', 'FOLDER' => 'Папка'))) {
			$this->Error = $sError;

			return false;
		}
		$sModule = strtolower($arModule['MODULE']);
		$arPar['FOLDER'] = strtolower($arPar['FOLDER']);
		if (!dirCopy($_SERVER['DOCUMENT_ROOT'].'/k2/module/'.$sModule.'/template/default/', $_SERVER['DOCUMENT_ROOT'].'/k2/module/'.$sModule.'/template/'.$arPar['FOLDER'].'/')) {
			$this->Error = 'Не удалось скопировать шаблон';

			return false;
		}
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/k2/module/'.$sModule.'/template/'.$arPar['FOLDER'].'/name.php', "<?\r\n\$sName = '".str_replace("'", '', $arPar['NAME'])."';\r\n?>");

		return true;
	}

	function DeleteTemplate($sModule, $sTemplate)
	{
		global $DB, $LIB;

		if (!$arModule = $this->ID($sModule)) {
			return false;
		}
		$sModule = strtolower($arModule['MODULE']);
		$sTemplate = strtolower($sTemplate);

		if (!dirDelete($_SERVER['DOCUMENT_ROOT'].'/k2/module/'.$sModule.'/template/'.$sTemplate.'/')) {
			$this->Error = 'Не удалось удалить шаблон';

			return false;
		}

		return true;
	}
}

?>