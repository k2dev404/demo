<?

class URL
{
	function ID($sURL)
	{
		global $DB;

		if ($arURL = $DB->Row("SELECT * FROM `k2_url` WHERE `URL` = '".DBS($sURL)."'")) {
			return $arURL;
		}
		$this->Error = 'Ссылка не найдена';

		return false;
	}

	function Add($arPar = array())
	{
		global $DB, $LIB;

		if($arPar['URL']){
			if (substr($arPar['URL'], 0, 1) != '/') {
				$arURL = array();
				if($arPar['ELEMENT']){
					if($arPar['SECTION']){
						foreach($LIB['SECTION']->Back($arPar['SECTION']) as $arSection)
						{
							$arURL[] = $arSection['FOLDER'];
						}
					}

					if($arPar['CATEGORY']){
						$arBack = $LIB['BLOCK_CATEGORY']->Back($arPar['BLOCK'], $arPar['CATEGORY']);
						foreach($arBack as $arCategory)
						{
							if($arCategory['URL_ALTERNATIVE'] && substr($arCategory['URL_ALTERNATIVE'], 0, 1) != '/'){
								$arURL[] = $arCategory['URL_ALTERNATIVE'];
							}
						}
					}

					$arURL[] = $arPar['URL'];
					$arPar['URL'] = '/'.implode('/', $arURL).'/';
				}
				else
				if($arPar['CATEGORY']){
					if($arPar['SECTION']){
						foreach($LIB['SECTION']->Back($arPar['SECTION']) as $arSection)
						{
							$arURL[] = $arSection['FOLDER'];
						}
					}

					$arBack = $LIB['BLOCK_CATEGORY']->Back($arPar['BLOCK'], $arPar['CATEGORY']);
					foreach($arBack as $arCategory)
					{
						if($arCategory['URL_ALTERNATIVE'] && substr($arCategory['URL_ALTERNATIVE'], 0, 1) != '/'){
							$arURL[] = $arCategory['URL_ALTERNATIVE'];
						}
					}

					$arPar['URL'] = '/'.implode('/', $arURL).'/';
				}
			}
		}

		if($arPar['OLD_URL']){
			if (substr($arPar['OLD_URL'], 0, 1) != '/') {
				$arPar['OLD_URL'] = $arPar['URL_BACK'].$arPar['OLD_URL'].'/';
			}
		}

		$this->Delete($arPar['URL']);
		$this->Delete($arPar['OLD_URL']);

		$DB->Query("DELETE FROM `k2_url` WHERE
		`SITE` = '".(int)$arPar['SITE']."' AND
		`SECTION` = '".(int)$arPar['SECTION']."' AND
		`SECTION_BLOCK` = '".(int)$arPar['SECTION_BLOCK']."' AND
		`CATEGORY` = '".(int)$arPar['CATEGORY']."' AND
		`ELEMENT` = '".(int)$arPar['ELEMENT']."'
		");

		if (!$arPar['URL']) {
			$this->Error = 'Не указана ссылка';

			return false;
		}

		if ($DB->Insert("
		INSERT INTO `k2_url` (
			`URL`,
			`SITE`,
			`SECTION`,
			`SECTION_BLOCK`,
			`CATEGORY`,
			`ELEMENT`
		) VALUES (
			'".DBS($arPar['URL'])."',
			".(int)$arPar['SITE'].",
			".(int)$arPar['SECTION'].",
			".(int)$arPar['SECTION_BLOCK'].",
			".(int)$arPar['CATEGORY'].",
			".(int)$arPar['ELEMENT']."
		);")
		) {
			return true;
		}

		return false;
	}

	function Delete($sURL)
	{
		global $LIB, $DB;

		$DB->Query("DELETE FROM `k2_url` WHERE `URL` = '".DBS($sURL)."'");
	}

	function Check($sURL, $arPar = array())
	{
		global $DB, $LIB;

		if (substr($sURL, 0, 1) != '/') {
			$arURL = array();
			if($arPar['ELEMENT']){
				if($arPar['SECTION']){
					foreach($LIB['SECTION']->Back($arPar['SECTION']) as $arSection)
					{
						$arURL[] = $arSection['FOLDER'];
					}
				}

				if($arPar['CATEGORY']){
					$arBack = $LIB['BLOCK_CATEGORY']->Back($arPar['BLOCK'], $arPar['CATEGORY']);
					foreach($arBack as $arCategory)
					{
						if($arCategory['URL_ALTERNATIVE'] && substr($arCategory['URL_ALTERNATIVE'], 0, 1) != '/'){
							$arURL[] = $arCategory['URL_ALTERNATIVE'];
						}
					}
				}

				$arURL[] = $arPar['URL_ALTERNATIVE'];
				$sURL = '/'.implode('/', $arURL).'/';
			}
			else
			if($arPar['CATEGORY']){
				if($arPar['SECTION']){
					foreach($LIB['SECTION']->Back($arPar['SECTION']) as $arSection)
					{
						$arURL[] = $arSection['FOLDER'];
					}
				}

				$arBack = $LIB['BLOCK_CATEGORY']->Back($arPar['BLOCK'], $arPar['CATEGORY']);
				foreach($arBack as $arCategory)
				{
					if($arCategory['URL_ALTERNATIVE'] && substr($arCategory['URL_ALTERNATIVE'], 0, 1) != '/'){
						$arURL[] = $arCategory['URL_ALTERNATIVE'];
					}
				}

				$sURL = '/'.implode('/', $arURL).'/';
			}
		}

		$arRow = $DB->Row("SELECT * FROM `k2_url` WHERE `URL` = '".DBS($sURL)."'");
		if (!$arRow) {
			return true;
		}

		if (!isset($arPar['SECTION'])) {
			$arPar['SECTION'] = 0;
		} else {
			if (!isset($arPar['CATEGORY']) || !isset($arPar['ELEMENT'])) {
				$arPar['ELEMENT'] = 0;
			}
		}

		$arField = array('SITE', 'SECTION', 'SECTION_BLOCK', 'CATEGORY', 'ELEMENT');
		foreach ($arField as $sField) {
			if (isset($arPar[$sField])) {
				if ($arRow[$sField] != $arPar[$sField]) {
					$this->Error = 'Такая альтернативная ссылка уже существует';

					return false;
				}
			}
		}

		return true;
	}

	function RowsCache()
	{
		global $DB;

		if(!isset($this->ListCache)){
			$this->ListCache = $DB->Rows("SELECT * FROM `k2_url`");
		}

		return $this->ListCache;
	}
}

?>