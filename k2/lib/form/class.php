<?

class Form
{
	function Show($nID)
	{
		global $CURRENT;

		$CURRENT['FORM']['ID'] = $nID;

		$ob = new SandBox;
		$ob->Dir = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/form/'.$nID.'/';
		$ob->Template('controller.php');

		unset($CURRENT['FORM']['ID']);
	}

	function ID($nID, $bField = false, $bTemplate = false)
	{
		global $LIB, $DB;

		if ($arForm = $DB->Row("SELECT * FROM `k2_form` WHERE `ID` = '".$nID."'")) {
			if ($bField) {
				$arForm['FIELD'] = $LIB['FIELD']->Rows('k2_form'.$nID);
			}
			if ($bTemplate) {
				$arForm['CONTROLLER'] = $LIB['FILE']->Read('/k2/dev/form/'.$nID.'/controller.php');
				$arForm['TEMPLATE'] = $LIB['FILE']->Read('/k2/dev/form/'.$nID.'/template.php');
				$arTemplate = $LIB['TEMPLATE']->Rows(2, $nID);
				for ($i = 0; $i < count($arTemplate); $i++) {
					$arForm['TEMPLATE_OPHEN'][] = $arTemplate[$i];
					$arForm['TEMPLATE_OPHEN'][count($arForm['TEMPLATE_OPHEN']) - 1]['TEMPLATE'] = $LIB['FILE']->Read('/k2/dev/form/'.$arForm['ID'].'/'.$arTemplate[$i]['FILE']);
				}
			}

			return $arForm;
		}

		$this->Error = 'Форма не найдена';

		return false;
	}

	function Rows()
	{
		global $DB;

		return $DB->Rows("SELECT * FROM `k2_form` ORDER BY `ID` ASC");
	}

	function Add($arPar = array())
	{
		global $LIB, $DB;

		if ($sError = formCheck(array('NAME' => 'Название'), $arPar)) {
			$this->Error = $sError;

			return false;
		}
		if ($nID = $DB->Insert("
		INSERT INTO `k2_form`(
			`NAME`,
			`CAPTCHA`
		)VALUES(
			'".DBS($arPar['NAME'])."',
			'".(int)$arPar['CAPTCHA']."'
		);
		")
		) {
			$arExs[] = $LIB['FILE']->Create('/k2/dev/form/'.$nID.'/controller.php', $arPar['CONTROLLER']);
			$arExs[] = $LIB['FILE']->Create('/k2/dev/form/'.$nID.'/template.php', $arPar['TEMPLATE']);
			if (in_array('', $arExs)) {
				$DB->Query("DELETE FROM `k2_form` WHERE ID = '".$nID."'");
				$this->Error = $LIB['FILE']->Error;
			} else {
				if ($DB->Query("CREATE TABLE `k2_form".$nID."` (
					`ID` int(11) NOT NULL AUTO_INCREMENT,
					`DATE_CREATED` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					`DATE_CHANGE` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
					`USER_CREATED` int(11) NOT NULL,
					`USER_CHANGE` int(11) NOT NULL,
					PRIMARY KEY (`ID`)
				)ENGINE=InnoDB DEFAULT CHARSET=utf8;")
				) {
					return $nID;
				}
			}
		}

		return false;
	}

	function Edit($nID, $arPar = array())
	{
		global $LIB, $DB;

		if (!$arForm = $this->ID($nID)) {
			return false;
		}
		if ($sError = formCheck(array('NAME' => 'Название'), $arPar)) {
			$this->Error = $sError;

			return false;
		}
		if ($DB->Query("UPDATE k2_form
        SET
			`NAME` = '".DBS($arPar['NAME'])."',
			`CAPTCHA` = '".(int)$arPar['CAPTCHA']."'
        WHERE
        	`ID` = '".$nID."';
        ")
		) {
			$arExs[] = $LIB['FILE']->Edit('/k2/dev/form/'.$nID.'/controller.php', $arPar['CONTROLLER']);
			$arExs[] = $LIB['FILE']->Edit('/k2/dev/form/'.$nID.'/template.php', $arPar['TEMPLATE']);
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
		global $LIB, $DB;

		if (!$arForm = $this->ID($nID, false, true)) {
			return false;
		}

		for ($i = 0; $i < count($arForm['TEMPLATE_OPHEN']); $i++) {
			$LIB['TEMPLATE']->Delete($arForm['TEMPLATE_OPHEN'][$i]['ID']);
		}

		unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev/form/'.$arForm['ID'].'/controller.php');
		unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev/form/'.$arForm['ID'].'/template.php');

		$DB->Query("DELETE FROM `k2_form` WHERE `ID` = '".$arForm['ID']."'");
		$DB->Query("DROP TABLE `k2_form".$arForm['ID']."`");

		$DB->Query("DELETE FROM `k2_field` WHERE `TABLE` = 'k2_form".$arForm['ID']."'");
		rmdir($_SERVER['DOCUMENT_ROOT'].'/k2/dev/form/'.$arForm['ID']);

		return true;
	}

	function Element($nID, $sTemplate = '%FIELD%')
	{
		global $LIB;

		if (!$arField = $LIB['FIELD']->ID($nID)) {
			return false;
		}

		$sID = 'f'.$nID;

		if (isset($_POST[$arField['FIELD']])) {
			$arField['VALUE'] = $_POST[$arField['FIELD']];
			if (!is_array($arField['VALUE'])) {
				$arField['VALUE'] = trim($arField['VALUE']);
			}
		}

		if ($arField['TYPE'] == 'INPUT') {
			if ($arField['SETTING']['TYPE'] == 'INT') {
				if ($arField['VALUE'] == '0') {
					$arField['VALUE'] = '';
				}
			}

			if ($arField['SETTING']['TYPE'] == 'FLOAT') {
				if ($arField['VALUE'] == '0.00') {
					$arField['VALUE'] = '';
				}
			}

			if ($arField['SETTING']['TYPE'] == 'DATE') {
				if ($arField['SETTING']['DEFAULT_DATE'] && !isset($_POST[$arField['FIELD']])) {
					$arField['VALUE'] = date('d.m.Y');
				}
				$arField['VALUE'] = dateFormat($arField['VALUE'], 'd.m.Y');
			}

			if ($arField['SETTING']['TYPE'] == 'DATE_TIME') {
				if ($arField['SETTING']['DEFAULT_DATE'] && !isset($_POST[$arField['FIELD']])) {
					$arField['VALUE'] = date('d.m.Y H:i');
				}
				$arField['VALUE'] = dateFormat($arField['VALUE'], 'd.m.Y H:s');
			}

			$sField = '<input type="text" name="'.$arField['FIELD'].'" value="'.html($arField['VALUE']).'" id="'.$sID.'">';

			if (in_array($arField['SETTING']['TYPE'], array('DATE', 'DATE_TIME'))) {
				if (!$GLOBALS['FILE_DATE_INCUDE']) {
					$sField
						.= '
        			<script type="text/javascript" src="/k2/admin/calendar/js/jscal2.js"></script>
	        		<script type="text/javascript" src="/k2/admin/calendar/js/lang/ru.js"></script>
	        		<link type="text/css" rel="stylesheet" href="/k2/admin/calendar/css/jscal2.css">
        			';
				}
				$GLOBALS['FILE_DATE_INCUDE'] = true;

				$sField .= '<img src="/k2/admin/i/icon/calendar.gif" width="16" height="16" class="calendar" title="Выбрать дату" id="f'.$sID.'_trigger">';

				$sCalendarFormatDate = '%d.%m.%Y';
				$sCalendarShowTime = 0;
				if ($arField['SETTING']['TYPE'] == 'DATE_TIME') {
					$sCalendarFormatDate = '%d.%m.%Y %H:%I';
					$sCalendarShowTime = 1;
				}
				$sField
					.= '<script type="text/javascript">
	            new Calendar({
	            	inputField:"'.$sID.'",
					dateFormat:"'.$sCalendarFormatDate.'",
					trigger:"f'.$sID.'_trigger",
					bottomBar:false,
					showTime:'.$sCalendarShowTime.',
					animation:false,
					onSelect:function(){
						this.hide();
					}
				});
				</script>';
			}
		}

		if ($arField['TYPE'] == 'TEXTAREA') {
			$sField = '<textarea name="'.$arField['FIELD'].'" rows="6"';
			if ($arField['SETTING']['WYSIWYG']) {
				$sField .= ' class="tinymce"';
			}
			if (defined('ADMIN_MODE')) {
				$sField .= ' field_id="'.$arField['ID'].'"';
			}
			$sField .= ' id="'.$sID.'">'.html($arField['VALUE']).'</textarea>';
		}

		if ($arField['TYPE'] == 'CHECKBOX') {
			if ($arField['SETTING']['VIEW']) {
				$sField .= '<label><input type="radio" name="'.$arField['FIELD'].'" value="1" checked="checked"';
				if ($arField['SETTING']['DEFAULT'] || $arField['VALUE']) {
					$sField .= ' checked="checked"';
				}
				$sField
					.= '>Да</label>
					<label><input type="radio" name="'.$arField['FIELD'].'" value="0"';
				if (!$arField['SETTING']['DEFAULT'] && !$arField['VALUE']) {
					$sField .= ' checked="checked"';
				}
				$sField .= '>Нет</label>';
			} else {
				$sField = '<input type="hidden" name="'.$arField['FIELD'].'" value="0"><label>
	            	<input type="checkbox" name="'.$arField['FIELD'].'" value="1"';
				if ($arField['SETTING']['DEFAULT'] || $arField['VALUE']) {
					$sField .= ' checked="checked"';
				}
				$sField .= '>'.$arField['NAME'].'</label>';
				$arField['NAME'] = '';
			}
		}

		if ($arField['TYPE'] == 'SELECT') {
			if (!$arSelect = $LIB['SELECT']->ID($arField['SETTING']['SELECT'])) {
				return false;
			}
			if ($arField['MULTIPLE']) {
				$arValue = $arField['VALUE'];
				if (!is_array($arField['VALUE'])) {
					$arValue = clearArray(explode(',', $arField['VALUE']));
				}
			} else {
				$arValue[0] = $arField['VALUE'];
			}
			$sField .= '<input type="hidden" name="'.$arField['FIELD'].'" value="">';
			if ($arField['MULTIPLE']) {
				$sField .= '<select name="'.$arField['FIELD'].'[]" multiple size="8">';
			} else {
				$sField .= '<select name="'.$arField['FIELD'].'"><option value="0">Выбрать</option>';
			}
			for ($i = 0; $i < count($arSelect['OPTION']); $i++) {
				$sField .= '<option value="'.$arSelect['OPTION'][$i]['ID'].'"';
				if (in_array($arSelect['OPTION'][$i]['ID'], $arValue)) {
					$sField .= ' selected';
				}
				$sField .= '>'.html($arSelect['OPTION'][$i]['NAME']).'</option>';
			}
			$sField .= '</select>';
		}

		if ($arField['TYPE'] == 'FILE') {
			if ($arField['MULTIPLE']) {
				$sField .= '<input type="file" name="'.html($arField['FIELD']).'[]" multiple>';

				$arValue = clearArray(explode(',', $arField['VALUE']));
				if ($_POST['FILE_OLD'][$arField['ID']]) {
					$arValue = $_POST['FILE_OLD'][$arField['ID']];
				}

				for ($i = 0; $i < count($arValue); $i++) {
					if ($arFile = $LIB['FILE']->ID($arValue[$i])) {
						$sField .= '<div><input type="hidden" name="FILE_OLD['.$arField['ID'].']['.$i.']" value="'.$arValue[$i].'">
					    <input type="checkbox" name="FILE_DELETE['.$arField['ID'].']['.$i.']" value="'.$arValue[$i].'" ';
						if ($_POST['FILE_DELETE'][$arField['ID']][$i] == $arValue[$i]) {
							$sField .= ' checked';
						}
						$sField .= ' id="'.$sID.'_'.$i.'" title="Удалить"> <a href="'.$arFile['PATH'].'" target="_blank">'.html($arFile['NAME']).'</a> (';
						if ($arFile['WIDTH']) {
							$sField .= $arFile['WIDTH'].'x'.$arFile['HEIGHT'].', ';
						}
						$sField .= fileByte($arFile['SIZE']).')</div>';
					}
				}
			} else {
				if ($_POST['FILE_OLD'][$arField['ID']]) {
					$arField['VALUE'] = $_POST['FILE_OLD'][$arField['ID']];
				}
				$sField .= '<input type="file" name="'.html($arField['FIELD']).'">';
				if ($arField['VALUE']) {
					if ($arFile = $LIB['FILE']->ID($arField['VALUE'])) {
						$sField .= '<div><input type="hidden" name="FILE_OLD['.$arField['ID'].']" value="'.$arField['VALUE'].'">
						<input type="checkbox" name="FILE_DELETE['.$arField['ID'].']" value="1" ';
						if ($_POST['FILE_DELETE'][$arField['ID']]) {
							$sField .= ' checked="checked"';
						}
						$sField .= ' id="'.$sID.'_'.$i.'" title="Удалить"> <a href="'.$arFile['PATH'].'" target="_blank">'.html($arFile['NAME']).'</a> (';
						if ($arFile['WIDTH']) {
							$sField .= $arFile['WIDTH'].'x'.$arFile['HEIGHT'].', ';
						}
						$sField .= fileByte($arFile['SIZE']).')</div>';
					}
				}
			}
		}

		if ($arField['TYPE'] == 'REFERENCE') {
			if ($arField['MULTIPLE']) {
				$arValue = $arField['VALUE'];
				if (!is_array($arField['VALUE'])) {
					$arValue = clearArray(explode(',', $arField['VALUE']));
				}
			} else {
				$arValue[0] = $arField['VALUE'];
			}

			$sFieldType = '';
			if (defined('ADMIN_MODE')) {
				$sFieldType .= ' type="'.$arField['TYPE'].'_'.$arField['SETTING']['TYPE'].'"';
			}

			$sField .= '<input type="hidden" name="'.$arField['FIELD'].'" value=""'.$sFieldType.'>';
			if ($arField['MULTIPLE']) {
				$sField .= '<select name="'.$arField['FIELD'].'[]" multiple size="8"'.$sFieldType.'>';
			} else {
				$sField .= '<select name="'.$arField['FIELD'].'"'.$sFieldType.'><option value="0">Выбрать</option>';
			}
			if ($arField['SETTING']['TYPE'] == 'SECTION') {
				$arSection = $LIB['SECTION']->Child(0, true);
				for ($i = 0; $i < count($arSection); $i++) {
					$sField .= '<option value="'.$arSection[$i]['ID'].'"';
					if (in_array($arSection[$i]['ID'], $arValue)) {
						$sField .= ' selected';
					}
					$sField .= '>'.str_repeat('&mdash;', $arSection[$i]['LEVEL'] + 1).' '.html($arSection[$i]['NAME']).'</option>';
				}
			}
			if ($arField['SETTING']['TYPE'] == 'USER') {
				$arUser = $LIB['USER']->Rows();
				for ($i = 0; $i < count($arUser); $i++) {
					$sField .= '<option value="'.$arUser[$i]['ID'].'"';
					if (in_array($arUser[$i]['ID'], $arValue)) {
						$sField .= ' selected';
					}
					$sField .= '>'.html($arUser[$i]['LOGIN']).' ['.$arUser[$i]['ID'].']</option>';
				}
			}
			if ($arField['SETTING']['TYPE'] == 'CATEGORY') {
				$arRows = $LIB['BLOCK_CATEGORY']->Child($arField['SETTING']['BLOCK'], 0, true);
				for ($i = 0; $i < count($arRows); $i++) {
					$sField .= '<option value="'.$arRows[$i]['ID'].'"';
					if (in_array($arRows[$i]['ID'], $arValue)) {
						$sField .= ' selected';
					}
					$sField .= '>'.str_repeat('&mdash;', $arRows[$i]['LEVEL']).' '.html($arRows[$i][$arField['SETTING']['FIELD']]).' ['.$arRows[$i]['ID'].']</option>';
				}
			}
			if ($arField['SETTING']['TYPE'] == 'ELEMENT') {
				$arRows = $LIB['BLOCK_ELEMENT']->Rows($arField['SETTING']['BLOCK'], array(), array($arField['SETTING']['FIELD'] => 'ASC'), array($arField['SETTING']['FIELD']));
				for ($i = 0; $i < count($arRows); $i++) {
					$sField .= '<option value="'.$arRows[$i]['ID'].'"';
					if (in_array($arRows[$i]['ID'], $arValue)) {
						$sField .= ' selected';
					}
					$sField .= '>'.html($arRows[$i][$arField['SETTING']['FIELD']]).' ['.$arRows[$i]['ID'].']</option>';
				}
			}
			$sField .= '</select>';

			if ($arField['SETTING']['TYPE'] == 'YANDEX_MAP') {
				global $SETTING;
				if (defined('ADMIN_MODE')) {
					$arYaMap['COORDS'] = $SETTING['YANDEX_MAP_COORDS'];
					$arYaMap['ZOOM'] = $SETTING['YANDEX_MAP_ZOOM'];

					if ($_POST[$arField['FIELD']] && !is_array($_POST[$arField['FIELD']])) {
						$_POST[$arField['FIELD']] = unserialize($_POST[$arField['FIELD']]);
					}

					if ($_POST[$arField['FIELD']]['COORDS']) {
						$arYaMap['COORDS'] = $_POST[$arField['FIELD']]['COORDS'];
					}
					if ($_POST[$arField['FIELD']]['ZOOM']) {
						$arYaMap['ZOOM'] = (int)$_POST[$arField['FIELD']]['ZOOM'] = $_POST[$arField['FIELD']]['ZOOM'];
					}

					$sField
						= '
	        			<script src="http://api-maps.yandex.ru/2.0/?load=package.standard&mode=debug&lang=ru-RU&ver=4"></script>
	        			<input type="hidden" name="'.$arField['FIELD'].'[COORDS]" value="'.html($_POST[$arField['FIELD']]['COORDS']).'">
	        			<input type="hidden" name="'.$arField['FIELD'].'[ZOOM]" value="'.$_POST[$arField['FIELD']]['ZOOM'].'">
	        			<div class="yaMap" id="yaMap'.$sID.'" style="width: 100%; height:300px;"></div>
	        			<script type="text/javascript">
						jQuery(function() {
							ymaps.ready(function() {
								var placeCoords = ['.html($arYaMap['COORDS']).'];
								var placeMap = new ymaps.Map(\'yaMap'.$sID.'\', {
									center: placeCoords,
									zoom: '.$arYaMap['ZOOM'].'
								});
								var cursorMark = new ymaps.Placemark(
									placeMap.getCenter(), {
										balloonContent: \'\'
									}, {
										draggable: false,
										iconImageHref: \'/k2/admin/i/icon/map-cursor.png\',
										iconImageSize: [150, 150],
										iconImageOffset: [-75, -75]
									}
								);
								placeMap.geoObjects.add(cursorMark);
								placeMap.controls.add(\'zoomControl\');
								placeMap.controls.add(\'mapTools\');
								placeMap.events.add(\'boundschange\', function (event) {

									$(\'input[name='.$arField['FIELD'].'\\\[COORDS\\\]]\').val(event.get(\'newCenter\'));
									$(\'input[name='.$arField['FIELD'].'\\\[ZOOM\\\]]\').val(event.get(\'newZoom\'));

									cursorMark.geometry.setCoordinates(placeMap.getCenter());
									return true;
								});
								placeMap.events.add(\'actiontick\', function (event) {
									var projection = placeMap.options.get(\'projection\');
									var tick = event.get(\'tick\');
									var center = projection.fromGlobalPixels(tick.globalPixelCenter, tick.zoom);
									cursorMark.geometry.setCoordinates(center);
									return true;
								});

							});
						});
						</script>
	        		';
				}
			}
		}

		if ($arField['TYPE'] == 'HIDDEN') {
			$sField = '<input type="hidden" name="'.$arField['FIELD'].'" value="'.html($arField['VALUE']).'">';
		}

		if ($arField['TYPE'] != 'HIDDEN') {
			$sName = html($arField['NAME']);
			if ($arField['REQUIRED']) {
				$sName .= '<span class="star">*</span>';
			}
			$sField = str_replace("%FIELD%", $sField, str_replace("%NAME%", $sName, $sTemplate));
		}

		return $sField;
	}

	function Value($arField, $arObj = array(), $arPar = array())
	{
		global $DB, $LIB;

		$sValue = $_POST[$arField['FIELD']];
		if ($arObj) {
			$sValue = $arObj[$arField['FIELD']];
		}

		if ($arField['TYPE'] == 'CHECKBOX') {
			$sValue = ($sValue ? 'Да' : 'Нет');
		}

		if (!$sValue) {
			return;
		}

		if ($arField['TYPE'] == 'FILE' && $arObj[$arField['FIELD']] && ($arFile = $LIB['FILE']->ID($arObj[$arField['FIELD']]))) {
			if ($arPar['FILE_TYPE'] == 'ID') {
				$sValue = $arFile['ID'];
			} else {
				if ($arPar['FILE_TYPE'] == 'PATH') {
					$sValue = $arFile['PATH'];
				} else {
					$sValue = 'http://'.$_SERVER['SERVER_NAME'].$arFile['PATH'];
				}
			}
		}

		if ($arField['TYPE'] == 'SELECT') {
			if ($arField['MULTIPLE']) {
				if (!is_array($sValue)) {
					$sValue = toArray($sValue);
				}
				if ($arRows = $DB->Rows("SELECT NAME FROM `k2_select_option` WHERE ID IN(".implode(',', $sValue).")")) {
					$arVal = array();
					for ($i = 0; $i < count($arRows); $i++) {
						$arVal[] = $arRows[$i]['NAME'];
					}
					$sValue = implode(', ', $arVal);
				}
			} else {
				if ($arRow = $DB->Row("SELECT NAME FROM `k2_select_option` WHERE ID = '".$sValue."'")) {
					$sValue = $arRow['NAME'];
				}
			}
		}

		return $sValue;
	}
}

?>