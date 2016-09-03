<?

class Photo
{
	function Resize($arPar = array())
	{
		if (!$arPar['WIDTH'] && !$arPar['HEIGHT']) {
			$this->Error = 'Задайте необходимые размеры';

			return false;
		}
		$sFullPath = $_SERVER['DOCUMENT_ROOT'].$arPar['PATH'];
		if (!file_exists($sFullPath)) {
			$this->Error = 'Укажите верный путь';

			return false;
		}
		if ($arPar['MARK']) {
			$sFullPathMark = $_SERVER['DOCUMENT_ROOT'].$arPar['MARK'];
			if (!file_exists($sFullPathMark)) {
				$this->Error = 'Укажите верный путь к файлу WaterMark';

				return false;
			}
		}
		$arPhotoProp = @getimagesize($sFullPath);
		if (!in_array($arPhotoProp['mime'], array('image/jpeg', 'image/gif', 'image/png'))) {
			$this->Error = 'Неправильный формат файла';

			return false;
		}
		if ($arPar['WIDHT'] && $arPar['HEIGHT'] && $arPhotoProp[0] <= $arPar['WIDHT'] && $arPhotoProp[1] <= $arPar['HEIGHT']) {
			return $arPar;
		}

		$arProp = array('PATH' => $sFullPath, 'MIME' => $arPhotoProp['mime'], 'REAL_WIDTH' => $arPhotoProp[0], 'REAL_HEIGHT' => $arPhotoProp[1], 'SET_WIDTH' => (int)$arPar['WIDTH'], 'SET_HEIGHT' => (int)$arPar['HEIGHT'], 'FIX' => $arPar['FIX'], 'MARK' => $sFullPathMark, 'QUALITY' => (empty($arPar['QUALITY']) ? 95 : (int)$arPar['QUALITY']));

		$ob = new PhotoGD;

		if ($arResize = $ob->Resize($arProp)) {
			return $arResize;
		} else {
			$this->Error = $ob->Error;
		}

		return false;
	}

	function Preview($nFile, $arPar)
	{
		global $LIB, $DB;

		if (!$arFile = $LIB['FILE']->ID($nFile)) {
			$this->Error = $LIB['FILE']->Error;

			return false;
		}

		$arFileInfo = pathinfo($arFile['PATH']);

		$sFile = md5($arPar['WIDTH'].$arPar['HEIGHT'].$arPar['FIX'].$arPar['MARK'].$arFileInfo['filename']).'.'.strtolower($arFileInfo['extension']);
		$sDir = '/files/preview/'.substr($sFile, 0, 3).'/';
		$sDirFile = $sDir.$sFile;
		$sDirFileFull = $_SERVER['DOCUMENT_ROOT'].$sDirFile;

		if ($arFile['PREVIEW'][$sDirFile]) {
			return array('PATH' => $sDirFile, 'WIDTH' => $arFile['PREVIEW'][$sDir.$sFile]['WIDTH'], 'HEIGHT' => $arFile['PREVIEW'][$sDir.$sFile]['HEIGHT']);
		}

		$sDirTmp = '/tmp/'.$sFile;
		$sDirTmpFull = $_SERVER['DOCUMENT_ROOT'].$sDirTmp;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].$arFile['PATH'])) {
			unset($this->Error, $this->Prop);
			if (copy($_SERVER['DOCUMENT_ROOT'].$arFile['PATH'], $sDirTmpFull)) {
				$arPhoto = $this->Resize(array('PATH' => $sDirTmp, 'WIDTH' => $arPar['WIDTH'], 'HEIGHT' => $arPar['HEIGHT'], 'FIX' => $arPar['FIX'], 'MARK' => $arPar['MARK']));
				if (!$this->Error) {
					@mkdir($_SERVER['DOCUMENT_ROOT'].$sDir, CHMOD_DIR, true);
					if (copy($sDirTmpFull, $sDirFileFull)) {
						unlink($sDirTmpFull);
						chmod($sDirFileFull, CHMOD_FILE);

						$arFile['PREVIEW'][$sDirFile] = array('WIDTH' => $arPhoto['UPDATE_WIDTH'], 'HEIGHT' => $arPhoto['UPDATE_HEIGHT']);
						$DB->Query("UPDATE `k2_file` SET `PREVIEW` = '".DBS(serialize($arFile['PREVIEW']))."' WHERE `ID` = '".$arFile['ID']."';");

						return array('PATH' => $sDirFile, 'WIDTH' => $arPhoto['UPDATE_WIDTH'], 'HEIGHT' => $arPhoto['UPDATE_HEIGHT']);
					}
				}
			}
		}

		return false;
	}
}

?>
