<?
function fileIcon($sName)
{
	$sExt = 'empty.png';
	$sPath = '/k2/admin/i/ext/';

	$arMatrixExt = array(
		'excel' => array('xls', 'xlsx', 'xlsm', 'xlsb', 'xltm', 'xlam', 'xlt'), 'film' => array('avi', 'wmv', 'mpg', 'mpeg', 'mkv', 'm2ts', '3gp', 'dat', 'm4v', 'mov', 'rm', 'vob'), 'flash-movie' => array('flv'), 'flash' => array('swf', 'fla'), 'globe' => array('html', 'htm', 'xml', 'xls'), 'illustrator' => array('ai', 'eps'), 'image' => array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'tif', 'tiff'), 'music' => array('mp3', 'amr', 'ape', 'bin', 'flac', 'm4a', 'mdi', 'ram'), 'office' => array('ppsx', 'pptm', 'pptx', 'xlsm'), 'outlook' => array('eml', 'dbx', 'nch', 'ods', 'ost'), 'pdf' => array('pdf'), 'photoshop' => array('psd'), 'php' => array('php', 'php3', 'php4', 'php5', 'phtml'), 'powerpoint' => array('ppt', 'pot', 'pps'), 'text' => array('sql', 'db', 'rtf', 'ini', 'txt', 'log'),
		'word' => array('doc', 'docx', 'dotx', 'odt'), 'zipper' => array('zip', 'rar', '7z', 'z', 'tag', 'gz', 'tgz', 'arj', 'lha', 'uc2', 'ace', 'zix', 'w02', '7zip')
	);

	if (preg_match("#.*\.(.*)$#i", $sName, $arMath)) {
		$sFoundExt = strtolower($arMath[1]);
		foreach ($arMatrixExt as $sExtName => $arExt) {
			if (in_array($sFoundExt, $arExt)) {
				if (file_exists($_SERVER['DOCUMENT_ROOT'].$sPath.$sExtName.'.png')) {
					$sExt = $sExtName.'.png';
					break;
				}
			}
		}
	}

	return $sExt;
}

function fileByte($nByte)
{
	$arByte = array('b', 'Kb', 'Mb', 'Gb', 'Tb');
	for ($i = 0; $nByte >= 1024 && $i < 4; $i++) {
		$nByte /= 1024;
	}

	return round($nByte).' '.$arByte[$i];
}

function fileTranslation($sName)
{
	$arMatrix = array(
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => "'", 'ы' => 'y', 'ъ' => "'", 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => "'", 'Ы' => 'Y', 'Ъ' => "'", 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya'
	);
	$sName = strtr($sName, $arMatrix);
	for ($i = 0; $i < mb_strlen($sName); $i++) {
		$sSymbol = mb_substr($sName, $i, 1);
		if (preg_match("#[a-z0-9\._\-\_\(\)]#i", $sSymbol)) {
			$sNewName .= $sSymbol;
		}
	}

	return $sNewName;
}

function fileDirListBack($nID, $arList = array())
{
	global $DB, $LIB;
	if ($arDir = $DB->Rows("SELECT * FROM `k2_file_dir` WHERE `ID` = '".(int)$nID."'")) {
		$arList[] = $arDir[0];
		if ($arDir[0]['PARENT']) {
			$arList = fileDirListBack($arDir[0]['PARENT'], $arList);
		}
	} else {
		$arList = array_reverse($arList);
	}

	return $arList;
}

function unZip($sFile, $sPath)
{
	$bError = 1;
	if ($zip = zip_open($sFile)) {
		while ($zip_entry = zip_read($zip)) {
			if (zip_entry_open($zip, $zip_entry, "r")) {
				$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				$sFileName = zip_entry_name($zip_entry);
				if (substr($sFileName, -1, 1) == '/') {
					@mkdir($sPath.$sFileName, CHMOD_DIR, true);
				} else {
					if (!$fp = fopen($sPath.$sFileName, "w")) {
						$bError = 0;
					} else {
						if (fwrite($fp, $buf) === false) {
							$bError = 0;
						}
						@fclose($fp);
						@chmod($sPath.$sFileName, CHMOD_FILE);
						zip_entry_close($zip_entry);
					}
				}
			}
		}
		zip_close($zip);
	}
	return $bError;
}

function Zip($sFile, $sPath)
{
	$arFile = dirList($sPath);
	$zip = new ZipArchive;
	if ($zip->open($sFile, ZIPARCHIVE::CREATE) !== true) {
		return false;
	}
	for ($i = 0; $i < count($arFile); $i++) {
		if (is_dir($sPath.$arFile[$i])) {
			$zip->addEmptyDir($arFile[$i]);
		} else {
			$zip->addFile($sPath.$arFile[$i], $arFile[$i]);
		}
	}
	$zip->close();

	return true;
}

function dirList($sPath, $arPar = array(), $sCurrenDir = '', $arFile = array())
{
	if (!$sPath || !is_dir($sPath.$sCurrenDir)) {
		return false;
	}
	$arDir = scandir($sPath.$sCurrenDir);
	for ($i = 0; $i < count($arDir); $i++) {
		if (in_array($arDir[$i], array('.', '..'))) {
			continue;
		}
		if (is_dir($sPath.$sCurrenDir.$arDir[$i])) {
			$arFile[] = $sCurrenDir.$arDir[$i].'/';
			$arFile = dirList($sPath, $arPar, $sCurrenDir.$arDir[$i].'/', $arFile);
		} else {
			$arFile[] = $sCurrenDir.$arDir[$i];
		}
	}

	return $arFile;
}

function dirCopy($sPath, $sPath_)
{
	if (!is_dir($sPath)) {
		return false;
	}

	@mkdir($sPath_, CHMOD_DIR);
	if (!file_exists($sPath_)) {
		return false;
	}
	$arDir = dirList($sPath);
	for ($i = 0; $i < count($arDir); $i++) {
		if (is_dir($sPath.$arDir[$i])) {
			@mkdir($sPath_.$arDir[$i], CHMOD_DIR, true);
		} else {
			@copy($sPath.$arDir[$i], $sPath_.$arDir[$i]);
			@chmod($sPath_.$arDir[$i], CHMOD_FILE);
		}
	}

	return true;
}

function dirClear($sPath)
{
	if (!$sPath || ($sPath == '/') || !is_dir($sPath)) {
		return false;
	}
	$obRecursive = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sPath), RecursiveIteratorIterator::CHILD_FIRST);
	foreach ($obRecursive as $sKey => $sFile) {
		is_dir($sFile) ? rmdir($sFile) : unlink($sFile);
	}

	return true;
}

function dirDelete($sPath)
{
	if (!dirClear($sPath)) {
		return false;
	}

	return @rmdir($sPath);
}

function dirSize($sPath)
{
	$nSize = 0;
	foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sPath)) as $obFile) {
		$nSize += $obFile->getSize();
	}

	return $nSize;
}

?>
