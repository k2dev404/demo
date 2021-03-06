<?

class FDir
{
	static function Scan($sPath, $bRecursive = false, $sCurrentDir = '/', $arFile = array())
	{
		if(!$sPath || !is_dir($sPath.$sCurrentDir)){
			return false;
		}
		$arDir = scandir($sPath.$sCurrentDir);
		for($i = 0; $i < count($arDir); $i++){
			if(in_array($arDir[$i], ['.', '..'])){
				continue;
			}

			if(is_dir($sPath.$sCurrentDir.$arDir[$i])){
				$arFile[] = $sCurrentDir.$arDir[$i].'/';
				if($bRecursive){
					$arFile = self::Scan($sPath, $bRecursive, $sCurrentDir.$arDir[$i].'/', $arFile);
				}
			}else{
				$arFile[] = $sCurrentDir.$arDir[$i];
			}
		}
		
		return $arFile;
	}

	static function Delete($sPath)
	{
		$sFullPath = $_SERVER['DOCUMENT_ROOT'].$sPath;

		if (!$sPath || ($sPath == '/') || !is_dir($sFullPath)) {
			return false;
		}

		$obRecursive = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sFullPath), RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($obRecursive as $sKey => $sFile) {
			is_dir($sFile) ? rmdir($sFile) : unlink($sFile);
		}

		return @rmdir($sFullPath);
	}


}


class FileDir
{
	function ID($nID)
	{
		global $DB, $LIB;

		if($arDir = $DB->Rows("SELECT * FROM `k2_file_dir` WHERE ID = '".(int)$nID."'")){
			return $arDir[0];
		}

		$this->Error = 'Папка не найдена';

		return false;
	}

	function Rows($nParent)
	{
		global $DB;
		$arDir = $DB->Rows("SELECT * FROM `k2_file_dir` WHERE `PARENT` = '".(int)$nParent."'");

		return $arDir;
	}

	function Add($arPar)
	{
		global $DB, $USER;

		$arPar['NAME'] = trim($arPar['NAME']);
		if($sError = formCheck(array('NAME' => 'Название'), $arPar)){
			$this->Error = $sError;

			return false;
		}
		if($DB->Rows("SELECT ID FROM `k2_file_dir` WHERE `PARENT` = '".$arPar['PARENT']."' `NAME` LIKE '".DBS($arPar['NAME'])."'")){
			$this->Error = 'Такая папка уже существует';

			return false;
		}
		if($nID = $DB->Insert("
		INSERT INTO `k2_file_dir`(
			`DATE_CREATED`,
			`USER`,
			`NAME`,
			`PARENT`
		)VALUES(
			NOW(), '".$USER['ID']."', '".DBS($arPar['NAME'])."', '".(int)$arPar['PARENT']."'
		)")
		){
			return $nID;
		}

		return false;
	}

	function Edit($nID, $arPar)
	{

	}

	function Delete($nID)
	{
		global $DB, $LIB;

		if(!$arDir = $this->ID($nID)){
			return false;
		}
		$arList = $this->Rows($nID);
		for($i = 0; $i < count($arList); $i++){
			$this->Delete($arList[$i]['ID']);
		}
		$arFile = $DB->Rows("SELECT ID FROM `k2_file` WHERE DIR = '".$nID."'");
		for($i = 0; $i < count($arFile); $i++){
			$LIB['FILE']->Delete($arFile[$i]['ID']);
		}
		$DB->Query("DELETE FROM `k2_file_dir` WHERE `ID` = '".(int)$nID."'");

		return true;
	}
}

?>