<?

class Field
{
	function Rows($sTable)
	{
		global $DB;

		$arRows = $DB->Rows("SELECT * FROM `k2_field` WHERE `TABLE` = '".DBS($sTable)."' ORDER BY `SORT` ASC");
		for ($i = 0; $i < count($arRows); $i++) {
			$arRows[$i]['SETTING'] = unserialize($arRows[$i]['SETTING']);
		}

		return $arRows;
	}

	function ID($nID)
	{
		global $DB;
		if ($arRow = $DB->Row("SELECT * FROM `k2_field` WHERE `ID` = '".$nID."'")) {
			$arRow['SETTING'] = unserialize($arRow['SETTING']);

			return $arRow;
		}
		$this->Error = 'Поле не найдено';

		return false;
	}

	function Add($sTable, $arPar = array())
	{
		global $DB;

		if ($sError = formCheck(array('FIELD' => 'Название поля', 'NAME' => 'Описание'), $arPar)) {
			$this->Error = $sError;

			return false;
		}

		if (!preg_match("#^[a-z0-9_]+$#i", $arPar['FIELD'])) {
			$this->Error = 'Название поле может состоять из набора символов a-z0-9_';

			return false;
		}

		$arAllType = $this->Type();
		$arType = $arAllType[$arPar['TYPE']];

		$sFormat = 'VARCHAR(255)';

		if ($arType['FORMAT']) {
			$sFormat = $arType['FORMAT'];
		}
		if ($arPar['SETTING']['TYPE'] && $arType['SETTING']['TYPE'][$arPar['SETTING']['TYPE']]['FORMAT']) {
			$sFormat = $arType['SETTING']['TYPE'][$arPar['SETTING']['TYPE']]['FORMAT'];
		}

		if (!$DB->Query('ALTER TABLE `'.DBS($sTable).'` ADD `'.$arPar['FIELD'].'` '.$sFormat.' NOT NULL')) {
			$this->Error = 'Поле с таким названием уже существует';

			return false;
		}
		$nSort = 10;
		if ($arField = $DB->Rows("SELECT `SORT` FROM `k2_field` WHERE `TABLE` = '".DBS($sTable)."' ORDER BY `SORT` DESC LIMIT 1")) {
			$nSort = $arField[0]['SORT'] + 10;
		}

		if ($nID = $DB->Insert("
		INSERT INTO `k2_field`(
			`TABLE`,
			`NAME`,
			`FIELD`,
			`SORT`,
			`TYPE`,
			`REQUIRED`,
			`MULTIPLE`,
			`SETTING`
		)VALUES(
			'".DBS($sTable)."', '".DBS($arPar['NAME'])."', '".DBS($arPar['FIELD'])."', '".$nSort."', '".DBS($arPar['TYPE'])."', '".(int)$arPar['REQUIRED']."', '".(int)$arPar['MULTIPLE']."', '".DBS(serialize($arPar['SETTING']))."'
		)")
		) {
			return $nID;
		}

		return false;
	}

	function Edit($nID, $arPar = array(), $bFull = 0)
	{
		global $DB;

		if (!$arField = $this->ID($nID)) {
			return false;
		}
		if (!$bFull) {
			$arPar += $arField;
		}
		if ($sError = formCheck(array('NAME' => 'Описание'), $arPar)) {
			$this->Error = $sError;

			return false;
		}
		if ($DB->Query("UPDATE `k2_field`
	    SET
			`NAME` = '".DBS($arPar['NAME'])."',
			`REQUIRED` = '".(int)$arPar['REQUIRED']."',
			`MULTIPLE` = '".(int)$arPar['MULTIPLE']."',
			`SETTING` = '".DBS(serialize($arPar['SETTING']))."',
			`SORT` = '".(int)$arPar['SORT']."'
	    WHERE
	    	ID = '".$nID."';
	    ")
		) {
			return true;
		}

		return false;
	}

	function Delete($nID)
	{
		global $LIB, $DB;

		if (!$arField = $this->ID($nID)) {
			return true;
		}
		$LIB['FILE']->DeleteAll(array('TABLE' => 'k2_block'.$arField['ID']));
		$LIB['FILE']->DeleteAll(array('TABLE' => 'k2_block'.$arField['ID'].'category'));

		$DB->Query("ALTER TABLE `".$arField['TABLE']."` DROP `".$arField['FIELD']."`");
		$DB->Query("DELETE FROM `k2_field` WHERE `ID` = '".$nID."'");

		return true;
	}

	function DeleteContent($arPar)
	{
		global $DB, $LIB;

		$QB = new QueryBuilder;
		$QB->From('k2_field')->Select('`ID`, `FIELD`, `TYPE`')->Where("`TYPE` = 'FILE' AND `TABLE` = ?", $arPar['TABLE']);
		if ($arPar['FIELD_NAME']) {
			$QB->Where('ID = ?', $arPar['FIELD_NAME']);
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
				if ($arField[$j]['TYPE'] == 'FILE') {
					$LIB['FILE']->Delete($arElement[$i][$arField[$j]['FIELD']]);
				}
			}
		}
	}

	function Type()
	{
		$arType = array(
			'INPUT' => array('NAME' => 'Строка', 'SETTING' => array('TYPE' => array('TEXT' => array('NAME' => 'Текст'), 'INT' => array('NAME' => 'Целое число', 'FORMAT' => 'INT'), 'FLOAT' => array('NAME' => 'Дробное число', 'FORMAT' => 'DECIMAL(11, 2)'), 'EMAIL' => array('NAME' => 'E-mail',), 'DATE' => array('NAME' => 'Дата', 'FORMAT' => 'DATE'), 'DATE_TIME' => array('NAME' => 'Дата и время', 'FORMAT' => 'DATETIME'),))), 'TEXTAREA' => array('NAME' => 'Текстовая область', 'FORMAT' => 'MEDIUMTEXT'), 'CHECKBOX' => array('NAME' => 'Истина или ложь', 'FORMAT' => 'TINYINT(1)'), 'SELECT' => array('NAME' => 'Список'), 'FILE' => array('NAME' => 'Файл'),
			'REFERENCE' => array('NAME' => 'Связь', 'SETTING' => array('TYPE' => array('SECTION' => array('NAME' => 'Раздел'), 'USER' => array('NAME' => 'Пользователь'), 'CATEGORY' => array('NAME' => 'Категория'), 'ELEMENT' => array('NAME' => 'Элемент'), 'YANDEX_MAP' => array('NAME' => 'Я.Карта')))), 'HIDDEN' => array('NAME' => 'Скрытое', 'FORMAT' => 'TEXT')
		);

		return $arType;
	}

	function CheckAll($sTable, $arPar)
	{
		$arField = $this->Rows($sTable);
		for ($i = 0; $i < count($arField); $i++) {
			if ($sError = $this->Check($arField[$i], $arPar)) {
				return $sError;
			}
		}
	}

	function Check($arField, $arPar)
	{
		global $LIB;

		$arValue[0] = $arPar[$arField['FIELD']];
		if (!is_array($arValue[0])) {
			$arValue[0] = trim($arValue[0]);
		}

		if ($arField['TYPE'] == 'REFERENCE') {
			if ($arField['SETTING']['TYPE'] == 'YANDEX_MAP') {
				if ($arField['REQUIRED'] && !$arValue[0]['COORDS']) {
					return changeMessage($arField['NAME']);
				}
			}
		}

		if ($arField['MULTIPLE']) {
			$arValue = $arPar[$arField['FIELD']];
		}

		if ($arField['TYPE'] != 'FILE') {
			if ($arField['REQUIRED'] && !$arValue[0]) {
				return changeMessage($arField['NAME']);
			}
		}

		if ($arField['TYPE'] == 'INPUT') {
			if ($arValue[0]) {
				$arValue[0] = trim($arValue[0]);
				if ($arField['SETTING']['TYPE'] == 'INT') {
					if (!preg_match("#^[0-9]+$#", $arValue[0])) {
						return 'В поле "'.$arField['NAME'].'" введите число';
					}
				}
				if ($arField['SETTING']['TYPE'] == 'FLOAT') {
					if (!preg_match("#^[0-9]+(.[0-9]+)?$#", $arValue[0])) {
						return 'В поле "'.$arField['NAME'].'" введите число';
					}
				}
				if ($arField['SETTING']['TYPE'] == 'EMAIL') {
					if (!filter_var($arValue[0], FILTER_VALIDATE_EMAIL)) {
						return 'В поле "'.$arField['NAME'].'" введите корректный адрес эл. почты';
					}
				}
				if ($arField['SETTING']['TYPE'] == 'DATE') {
					if (!preg_match("#^\d{2}\.\d{2}\.\d{4}$#i", $arValue[0])) {
						return 'Неправильная дата в поле "'.$arField['NAME'].'"';
					}
				}
				if ($arField['SETTING']['TYPE'] == 'DATE_TIME') {
					if (!preg_match("#^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$#i", $arValue[0])) {
						return 'Неправильная дата в поле "'.$arField['NAME'].'"';
					}
				}
			}
		}

		if ($arField['TYPE'] == 'FILE') {
			if ($arField['MULTIPLE']) {
				$__FILES = $_FILES[$arField['FIELD']];
			} else {
				$__FILES = array('name' => array($_FILES[$arField['FIELD']]['name']), 'type' => array($_FILES[$arField['FIELD']]['type']), 'tmp_name' => array($_FILES[$arField['FIELD']]['tmp_name']), 'error' => array($_FILES[$arField['FIELD']]['error']), 'size' => array($_FILES[$arField['FIELD']]['size']),);
			}

			if ($arField['REQUIRED'] && !$__FILES['tmp_name'][0]) {
				if (count($_POST['FILE_OLD'][$arField['ID']]) <= count($_POST['FILE_DELETE'][$arField['ID']])) {
					return 'Загрузите файл в поле "'.$arField['NAME'].'"';
				}
			}

			for ($i = 0; $i < count($__FILES['tmp_name']); $i++) {
				if (!$__FILES['tmp_name'][$i]) {
					continue;
				}

				if ($arField['SETTING']['FILESIZE'] > 0 && $arField['SETTING']['FILESIZE'] < $__FILES['size'][$i]) {
					return 'В поле "'.$arField['NAME'].'" файл слишком большого размера';
				}
				preg_match("#.+\.(.+?)$#i", $__FILES['name'][$i], $arMath);
				$arMath[1] = strtolower($arMath[1]);
				if ($arField['SETTING']['FILE_EXT']) {
					$arExt = str_replace(' ', '', explode(',', $arField['SETTING']['FILE_EXT']));
					if (!in_array($arMath[1], $arExt)) {
						return 'В поле "'.$arField['NAME'].'" неверный тип файла';
					}
				}
				#Если указаны минимальные размеры загружаемой картинки запускаем механизм
				if ($arField['SETTING']['MIN']['WIDTH'] || $arField['SETTING']['MIN']['HEIGHT']) {
					$tmpName = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5(microtime()).'.'.$arMath[1];
					if (is_uploaded_file($__FILES['tmp_name'][$i]) && @copy($__FILES['tmp_name'][$i], $tmpName)) {
						$arPhotoProp = @getimagesize($tmpName);
						unlink($tmpName);
						if (($arPhotoProp[0] < $arField['SETTING']['MIN']['WIDTH'] && $arField['SETTING']['MIN']['WIDTH']) || ($arPhotoProp[1] < $arField['SETTING']['MIN']['HEIGHT'] && $arField['SETTING']['MIN']['HEIGHT'])) {
							if ($arField['SETTING']['MIN']['WIDTH'] && $arField['SETTING']['MIN']['HEIGHT']) {
								return 'Размеры файла в поле "'.$arField['NAME'].'" должны быть не менее '.$arField['SETTING']['MIN']['WIDTH'].'x'.$arField['SETTING']['MIN']['HEIGHT'].'px';
							} else {
								if ($arField['SETTING']['MIN']['WIDTH']) {
									return 'Размер файла в поле "'.$arField['SETTING']['NAME'].'" по ширине должны быть не менее '.$arField['SETTING']['MIN']['WIDTH'].'px';
								} else {
									if ($arField['SETTING']['MIN']['HEIGHT']) {
										return 'Размер файла в поле "'.$arField['SETTING']['NAME'].'" по высоте должны быть не менее '.$arField['SETTING']['MIN']['HEIGHT'].'px';
									}
								}
							}
						}
					}
				}
			}
		}
	}

	function Update($arList, $arPar)
	{
		global $DB, $LIB;

		$QB = new QueryBuilder;
		$QB->Update($arList['TABLE']);

		$arField = $this->Rows($arList['TABLE']);
		for ($i = 0; $i < count($arField); $i++) {
			$arSetting = $arField[$i]['SETTING'];

			$sValue = $arPar[$arField[$i]['FIELD']];
			if ($arField[$i]['MULTIPLE'] && is_array($sValue)) {
				$sValue = implode(',', $sValue);
				if ($sValue) {
					$sValue = ','.$sValue.',';
				}
			}

			if ($arField[$i]['TYPE'] == 'INPUT') {
				if ($arSetting['TYPE'] == 'FLOAT') {
					$sValue = str_replace(',', '.', $sValue);
				}
				if (in_array($arSetting['TYPE'], array('DATE', 'DATE_TIME'))) {
					$sValue = dateFormat($sValue, 'Y-m-d H:i:s');
				}
			}

			if ($arField[$i]['TYPE'] == 'FILE') {
				if ($arField[$i]['MULTIPLE']) {
					$__FILES = $_FILES[$arField[$i]['FIELD']];
				} else {
					$__FILES = array('name' => array($_FILES[$arField[$i]['FIELD']]['name']), 'type' => array($_FILES[$arField[$i]['FIELD']]['type']), 'tmp_name' => array($_FILES[$arField[$i]['FIELD']]['tmp_name']), 'error' => array($_FILES[$arField[$i]['FIELD']]['error']), 'size' => array($_FILES[$arField[$i]['FIELD']]['size']));
					if ($arPar['FILE_DELETE'][$arField[$i]['ID']]) {
						$arPar['FILE_DELETE'][$arField[$i]['ID']] = array($arPar['FILE_OLD'][$arField[$i]['ID']]);
					}
					if ($arPar['FILE_OLD'][$arField[$i]['ID']]) {
						$arPar['FILE_OLD'][$arField[$i]['ID']] = array($arPar['FILE_OLD'][$arField[$i]['ID']]);
					}
					if ($_FILES[$arField[$i]['FIELD']]['name'] && $arField[$i]['REQUIRED'] && $arPar['FILE_OLD'][$arField[$i]['ID']] && !$arPar['FILE_DELETE'][$arField[$i]['ID']]) {
						$arPar['FILE_DELETE'][$arField[$i]['ID']] = $arPar['FILE_OLD'][$arField[$i]['ID']];
					}
				}

				$arAllFile = array();
				for ($j = 0; $j < count($arPar['FILE_OLD'][$arField[$i]['ID']]); $j++) {
					if ($arPar['FILE_DELETE'][$arField[$i]['ID']][$j]) {
						$LIB['FILE']->Delete($arPar['FILE_DELETE'][$arField[$i]['ID']][$j]);
					} else {
						$arAllFile[] = $arPar['FILE_OLD'][$arField[$i]['ID']][$j];
					}
				}

				for ($j = 0; $j < count($__FILES['tmp_name']); $j++) {
					if (!$__FILES['tmp_name'][$j]) {
						continue;
					}
					$arParFile = array('name' => $__FILES['name'][$j], 'type' => $__FILES['type'][$j], 'tmp_name' => $__FILES['tmp_name'][$j], 'error' => $__FILES['error'][$j], 'size' => $__FILES['size'][$j]);
					$arParFile['PATH'] = $arList['PATH'];
					if ($arSetting['RESIZE']) {
						$arParFile['WIDTH'] = $arSetting['RESIZE']['WIDTH'];
						$arParFile['HEIGHT'] = $arSetting['RESIZE']['HEIGHT'];
						$arParFile['FIX'] = $arSetting['RESIZE']['FIX'];
					}
					if ($arSetting['PREVIEW']['SHOW']) {
						$arParFile['PREVIEW'] = $arSetting['PREVIEW'];
					}
					$arParFile['TRANSLATION'] = $arSetting['TRANSLATION'];
					if ($nFileID = $LIB['FILE']->Upload($arParFile)) {
						$arAllFile[] = $nFileID;
					}
				}
				$sValue = '';
				if ($arAllFile) {
					$sValue = implode(',', $arAllFile);
					if ($sValue && $arField[$i]['MULTIPLE']) {
						$sValue = ','.$sValue.',';
					}
				}
			}

			if ($arField[$i]['TYPE'] == 'REFERENCE') {
				if ($arField[$i]['SETTING']['TYPE'] == 'YANDEX_MAP') {
					if (is_array($sValue) && $sValue['COORDS'] && $sValue['ZOOM']) {
						$sValue = serialize(array('COORDS' => $sValue['COORDS'], 'ZOOM' => (int)$sValue['ZOOM']));
					} else {
						$sValue = '';
					}
				}
			}

			if ($arField[$i]['TYPE'] == 'FILE' && !$sValue && !$arPar['FILE_OLD'][$arField[$i]['ID']]) {

			} else {
				$QB->Set('`'.$arField[$i]['FIELD'].'` = ?', $sValue);
			}
		}
		$QB->Where('`ID` = ?', $arList['ID']);

		$DB->Query($QB->Build());
	}
}

?>