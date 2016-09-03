<?

class File
{
	function ID($nID)
	{
		global $DB;

		if (!$this->FILE) {
			$arRows = $DB->Rows("SELECT * FROM `k2_file`");
			for ($i = 0; $i < count($arRows); $i++) {
				$arRows[$i]['PATH'] = '/files/original/'.$arRows[$i]['PATH'];
				if ($arRows[$i]['PREVIEW']) {
					$arRows[$i]['PREVIEW'] = unserialize($arRows[$i]['PREVIEW']);
				} else {
					$arRows[$i]['PREVIEW'] = array();
				}
				$this->FILE[$arRows[$i]['ID']] = $arRows[$i];
			}
		}
		if ($this->FILE[$nID]) {
			return $this->FILE[$nID];
		}
		$this->Error = 'Файл не найден';

		return false;
	}

	function Create($sPath, $sContent = '')
	{
		$sFullPath = $_SERVER['DOCUMENT_ROOT'].$sPath;
		$arExp = explode("/", $sPath);
		unset($arExp[count($arExp) - 1]);
		$sDirPath = implode("/", $arExp);
		@mkdir($_SERVER['DOCUMENT_ROOT'].$sDirPath, CHMOD_DIR, true);
		if (substr($sPath, -1, 1) != '/') {
			if (!$this->Edit($sPath, $sContent, 'w')) {
				$this->Error = changeMessage($sFullPath, 'FILE_WRITE');

				return false;
			}
			chmod($sFullPath, CHMOD_FILE);
		} else {
			if (file_exists($sFullPath)) {
				$this->Error = changeMessage($sFullPath, 'FILE_CREATED_DIR');

				return false;
			}
		}

		return true;
	}

	function Add($sPath, $arPar)
	{
		global $DB, $LIB, $USER;

		if (!$arPar['FULL_PATH']) {
			$sPath = $_SERVER['DOCUMENT_ROOT'].$sPath;
		}
		if (!file_exists($sPath)) {
			$this->Error = 'Укажите правильный путь к файлу';

			return false;
		}

		if(!$arPar['NAME']){
			$arPar['NAME'] = pathinfo($sPath, PATHINFO_BASENAME);
		}

		$arFileInfo = pathinfo($arPar['NAME']);

		if (!$arFileInfo['extension']) {
			return false;
		}

		$sFile = md5(microtime()).'.'.strtolower($arFileInfo['extension']);
		if ($arPar['TRANSLATION']) {
			$sFile = fileTranslation($arFileInfo['filename']).'.'.strtolower($arFileInfo['extension']);
		}

		for ($i = 0; $i < 1000; $i++) {
			if ($i) {
				$sDir = genPassword(3, true).'/';
			} else {
				$sDir = substr($sFile, 0, 3).'/';
			}
			$sDirOrig = '/files/original/'.$sDir;
			$sDirFileFull = $_SERVER['DOCUMENT_ROOT'].$sDirOrig.$sFile;

			if (!file_exists($sDirFileFull)) {
				break;
			}
		}

		@mkdir($_SERVER['DOCUMENT_ROOT'].$sDirOrig, CHMOD_DIR, true);
		if (copy($sPath, $sDirFileFull)) {
			chmod($sDirFileFull, CHMOD_FILE);
			if ($arPhotoProp = @getimagesize($sDirFileFull) && ($arPar['WIDTH'] || $arPar['HEIGHT'])) {
				$LIB['PHOTO']->Resize(array('PATH' => $sDirOrig.$sFile, 'WIDTH' => $arPar['WIDTH'], 'HEIGHT' => $arPar['HEIGHT'], 'FIX' => $arPar['FIX'], 'MARK' => $arPar['MARK']));
			}
			clearstatcache();
			$arFileProp = @getimagesize($sDirFileFull);
			if ($nID = $DB->Insert("INSERT INTO `k2_file` (
	        		`DATE_CREATED`,
	        		`USER`,
	        		`NAME`,
	        		`PATH`,
	        		`TYPE`,
	        		`SIZE`,
	        		`WIDTH`,
	        		`HEIGHT`,
	        		`DIR`
        		) VALUES (
		        	NOW(),
		        	'".$USER['ID']."',
		        	'".DBS($arPar['NAME'])."',
		        	'".DBS($sDir.$sFile)."',
		        	'".DBS($arFileProp['mime'])."',
		        	'".(int)filesize($sDirFileFull)."',
		        	'".(int)$arFileProp[0]."',
		        	'".(int)$arFileProp[1]."',
		        	'".(int)$arPar['DIR']."'
		        )")
			) {
				return $nID;
			}
		}

		return false;
	}

	function DeleteAll($arPar)
	{
		global $DB;

		$QB = new QueryBuilder;
		$QB->From('k2_field')->Select('FIELD')->Where('`TYPE` = 4 AND `TABLE` = ?', $arPar['TABLE']);
		if ($arPar['FIELD']) {
			$QB->Where('ID = ?', $arPar['FIELD']);
		}

		$arField = $DB->Rows($QB->Build());
		$QB = new QueryBuilder;
		$QB->From($arPar['TABLE']);

		if ($arPar['ELEMENT']) {
			$QB->Where('ID = ?', $arPar['ELEMENT']);
		}
		for ($i = 0; $i < count($arField); $i++) {
			$QB->Select($arField[$i]['FIELD']);
		}

		$arElement = $DB->Rows($QB->Build());
		for ($i = 0; $i < count($arElement); $i++) {
			for ($j = 0; $j < count($arField); $j++) {
				$this->Delete($arElement[$i][$arField[$j]['FIELD']]);
			}
		}
	}

	function Delete($mID)
	{
		global $DB;
		$arList = clearArray(explode(',', $mID));
		for ($i = 0; $i < count($arList); $i++) {
			if ($arFile = $this->ID($arList[$i])) {
				if ($arFile['PREVIEW']) {
					foreach ($arFile['PREVIEW'] as $sKey => $arArray) {
						unlink($_SERVER['DOCUMENT_ROOT'].$sKey);
						preg_match("#^(.+)/.+?\..+?$#i", $sKey, $arMath);
						//@rmdir($_SERVER['DOCUMENT_ROOT'].$arMath[1]);
					}
				}
				unlink($_SERVER['DOCUMENT_ROOT'].'/'.$arFile['PATH']);
				preg_match("#^(.+)/.+?\..+?$#i", $arFile['PATH'], $arMath);
				//@rmdir($_SERVER['DOCUMENT_ROOT'].$arMath[1]);
				$DB->Query("DELETE FROM `k2_file` WHERE `ID` = '".$arFile['ID']."'");
			}
		}
	}

	function Upload($arPar)
	{
		if (!is_uploaded_file($arPar['tmp_name']) || $arPar['error']) {
			$this->Error = 'Файл не загружен';

			return false;
		}
		$arPar['FULL_PATH'] = 1;
		$arPar['NAME'] = $arPar['name'];
		if ($nID = $this->Add($arPar['tmp_name'], $arPar)) {
			return $nID;
		}

		return false;
	}

	function Read($sPath)
	{
		$sFullPath = $_SERVER['DOCUMENT_ROOT'].$sPath;
		if ($sCont = @file_get_contents($sFullPath)) {
			return $sCont;
		} else {
			$arAnalytic = $this->Analytic($sFullPath);
			if (!$arAnalytic['EXISTS']) {
				$this->Error = changeMessage($sFullPath, 'FILE_EXIST');

				return false;
			} elseif (!$arAnalytic['READABLE']) {
				$this->Error = changeMessage($sFullPath, 'FILE_READABLE');

				return false;
			}
		}
	}

	function Edit($sPath, $sContent = '', $sKey = 'w')
	{
		$sFullPath = $_SERVER['DOCUMENT_ROOT'].$sPath;
		$rFile = @fopen($sFullPath, $sKey);
		if (@fwrite($rFile, $sContent) !== false) {
			return true;
		} else {
			$arAnalytic = $this->Analytic($sFullPath);
			if (!$arAnalytic['EXISTS']) {
				$this->Error = changeMessage($sFullPath, 'FILE_EXISTS');

				return false;
			} elseif (!$arAnalytic['WRITABLE']) {
				$this->Error = changeMessage($sFullPath, 'FILE_WRITABLE');

				return false;
			}

			return false;
		}
	}

	function Analytic($sFullPath)
	{
		return array('EXISTS' => file_exists($sFullPath), 'READABLE' => is_readable($sFullPath), 'WRITABLE' => is_writable($sFullPath),);
	}

	function Check($arFile, $arPar)
	{
		if (!$arFile['name'] || $arFile['error']) {
			return 'Загрузите файл в поле "'.$arPar['FIELD_NAME'].'""';
		}

		if ($arPar['TYPE']) {
			preg_match("#.+\.(.+?)$#i", $arFile['name'], $arMath);
			$sExt = strtolower($arMath[1]);

			if ($arPar['TYPE'] == 'IMAGE') {
				$arExt = array('jpg', 'jpeg', 'gif', 'png');
				if (!in_array($sExt, $arExt) || !preg_match("#^image/#", $arFile['type'])) {
					return 'В поле &laquo;'.$arPar['FIELD_NAME'].'&raquo; неверный тип файла';
				}
			}
		}
	}

	function IsPhoto($nID)
	{
		if (!$arFile = $this->ID($nID)) {
			return false;
		}
		preg_match("#.+\.(.+?)$#i", $arFile['NAME'], $arMath);
		if (!in_array(strtolower($arMath[1]), array('jpg', 'jpeg', 'gif', 'png')) || !preg_match("#^image/#", $arFile['TYPE'])) {
			return false;
		}

		return true;
	}
}

?>