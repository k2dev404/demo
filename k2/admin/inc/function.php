<?

$_ID = (int)$_REQUEST['id'];
$_OBJ = (int)$_REQUEST['obj'];
$_SITE = (int)$_REQUEST['site'];
$_OBJECT = (int)$_REQUEST['object'];
$_OBJECT_ID = (int)$_REQUEST['object_id'];
$_TYPE = (int)$_REQUEST['type'];
$_BLOCK = (int)$_REQUEST['block'];
$_SBLOCK = (int)$_REQUEST['sblock'];
$_ACTION = $_REQUEST['action'];
$_PARENT = (int)$_REQUEST['parent'];
$_SECTION = (int)$_REQUEST['section'];
$_CATEGORY = (int)$_REQUEST['category'];
$_TEMPLATE = $_REQUEST['template'];
$_SORT = $_REQUEST['sort'];
$_SECTION_BLOCK = (int)$_REQUEST['section_block'];

$_FIELD = (int)$_REQUEST['field'];
$_COMPONENT = (int)$_REQUEST['component'];
$_PATH = $_REQUEST['path'];
$_COLLECTION = (int)$_REQUEST['collection'];

$_BACK = ($_REQUEST['back'] ? base64_decode($_REQUEST['back']) : '/');

$_PAGE = $_GET['page'] ? ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1) : 1;

if (!$USER['PERMISSION']['DEFAULT']['ADMIN']['INDEX']) {
	include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/auth.php');
	exit;
}

if ($_POST['AUTH_LOGIN']) {
	Redirect('/k2/admin/');
}

session_start();

$SYSTEM = $DB->Row("SELECT * FROM `k2_system`");

function _treeMap($nSiteID, $nParentID = 0, $nCounter = 0, $arSB = array())
{
	global $DB;
	if ($arRows = $DB->Rows("SELECT * FROM `k2_section` WHERE `SITE` = '" . (int)$nSiteID . "' AND `PARENT` = '" . (int)$nParentID . "' ORDER BY `SORT` ASC")) {
		$sRet .= '<ul class="filetree">';
		for ($i = 0; $i < count($arRows); $i++) {
			$sChild = _treeMap($nSiteID, $arRows[$i]['ID'], 1, $arSB);

			$arRows[$i]['URL'] = respectiveURL($arRows[$i], 'section');

			$sRet .= '<li><div class="tree-section"><a href="/k2/admin/section/content/?section=' . $arRows[$i]['ID'] . '"';
			if (!$arRows[$i]['ACTIVE']) {
				$sRet .= ' class="passive"';
			}

			$sRet .= ' section="' . $arRows[$i]['ID'] . '" url="' . $arRows[$i]['URL'] . '">' . html($arRows[$i]['NAME']) . '</a></div>' . $sChild . '</li>';
			$nCounter = 1;
		}
		$sRet .= '</ul>';
	}

	return $sRet;
}

function _treeMapMove($arPar)
{
	global $DB;
	if ($arRows = $DB->Rows("SELECT * FROM `k2_section` WHERE `SITE` = '" . (int)$arPar['SITE'] . "' AND `PARENT` = '" . (int)$arPar['PARENT'] . "' ORDER BY `SORT` ASC")) {
		$sRet .= '<ul class="filetree">';
		for ($i = 0; $i < count($arRows); $i++) {

			$arPar['PARENT'] = $arRows[$i]['ID'];
			$sChild = _treeMapMove($arPar);
			$sRet .= '<li><div><a class="';
			if (!$arRows[$i]['ACTIVE']) {
				$sRet .= 'passive';
			}
			if ($arSBRow) {
				$sRet .= 'sblock';
			}

			$sRet .= '">' . html($arRows[$i]['NAME']) . '</a>';

			if ($arSBlock = $DB->Rows("SELECT ID, NAME FROM `k2_section_block` WHERE SECTION = '" . $arRows[$i]['ID'] . "' AND BLOCK = '" . (int)$arPar['BLOCK'] . "'")) {
				#$sRet .= '[ href="javascript:void(0)" onclick="elementMoveInsert({\'ID\':'.$arPar['ID'].'})"';
				$sRet .= '<span class="sblock">[';
				for ($n = 0; $n < count($arSBlock); $n++) {
					if ($n) {
						$sRet .= ', ';
					}
					$sRet .= '<a href="element-move-insert.php?block=' . (int)$arPar['BLOCK'] . '&amp;id=' . (int)$arPar['ID'] . '&section_block_to=' . $arSBlock[$n]['ID'] . '" title="Перенести элемент сюда">' . $arSBlock[$n]['NAME'] . '</a>';
				}
				$sRet .= ']</span>';
			}


			$sRet .= '</div>' . $sChild . '</li>';
		}
		$sRet .= '</ul>';
	}

	return $sRet;
}

function navPage($nTotal, $nInPage, $sURL = '?')
{
	global $_PAGE;

	if (!$nTotal) {
		$nTotal = 1;
	}

	$nTotal = ceil($nTotal / $nInPage);

	if ($_PAGE > $nTotal) {
		$_PAGE = 1;
	}

	$nStart = 0;
	$nWeight = 9;

	$nEnd = $nWeight;
	$nMean = floor($nWeight / 2);
	if ($_PAGE > $nWeight - $nMean) {
		$nEnd = $_PAGE + $nMean;
		$nStart = $nEnd - $nWeight;
	}
	if ($nEnd + 1 > $nTotal) {
		$nEnd = $nTotal;
		$nStart = $nEnd - $nWeight;
	}

	if ($nStart < 0) {
		$nStart = 0;
	}

	$sRet = '<div class="navPage">';
	if ($_PAGE > 1) {
		$sRet .= '<a href="' . $sURL . 'page=' . ($_PAGE - 1) . '" class="action prev"></a>';
	} else {
		$sRet .= '<a class="action prev disabled"></a>';
	}
	$sRet .= '<div class="page under">';
	if ($_PAGE - $nWeight + $nMean > 0) {
		$sRet .= ' <a href="' . $sURL . 'page=1">1</a> ... ';
	}
	for ($i = $nStart; $i < $nEnd; $i++) {
		$sRet .= '<a href="' . $sURL . 'page=' . ($i + 1) . '"';
		if ($_PAGE - 1 == $i) {
			$sRet .= ' class="active"';
		}
		$sRet .= '>' . ($i + 1) . '</a> ';
	}
	if ($nTotal - $nEnd > 0) {
		$sRet .= ' ... <a href="' . $sURL . 'page=' . $nTotal . '">' . $nTotal . '</a> ';
	}
	$sRet .= '</div>';
	if ($_PAGE < $nTotal) {
		$sRet .= '<a href="' . $sURL . 'page=' . ($_PAGE + 1) . '" class="action next"></a>';
	} else {
		$sRet .= '<a class="action next disabled"></a>';
	}
	$sRet .= '</div>';

	return $sRet;
}

function tab($arPar = array())
{
	?>
	<div class="tab"><?
	for ($i = 0; $i < count($arPar); $i++) {
		?><a href="/k2/admin<?=$arPar[$i][1]?>"<?
		if ($arPar[$i][2]) {
			?> class="active"<?
		}
		?>><?=$arPar[$i][0]?></a><?
	}
	?></div><?
}

function tab_($arPar = array(), $arRight = array(), $sClass = 'subMenu')
{
	?>
	<div class="<?=$sClass?>">
	<div class="l"><?
		for ($i = 0; $i < count($arPar); $i++) {
			$arClass = array();
			if ($i) {
				?><span>|</span><?
			}
			if ($arPar[$i][2]) {
				$arClass[] = 'active';
			}
			if (isset($arPar[$i][3]) && !$arPar[$i][3]) {
				$arClass[] = 'passive';
			}
			?><a href="/k2/admin<?=$arPar[$i][1]?>"<?
			if ($arClass) {
				?> class="<?=implode(' ', $arClass)?>"<?
			}
			?>><?=$arPar[$i][0]?></a><?
		}
		?></div><?
	if ($arRight) {
		?>
		<div class="r"><?
		for ($i = 0; $i < count($arRight); $i++) {
			if ($i) {
				?><span>|</span><?
			}
			echo $arRight[$i];
		}
		?></div><?
	}
	?>
	<div class="clear"></div></div><?
}

function navBack($arPar = array())
{
	?>
	<div class="navBack"><?
	for ($i = 0, $c = count($arPar); $i < $c; $i++) {
		if ($i) {
			?> » <?
		}
		if ($i != $c - 1) {
			?><a href="/k2/admin<?=$arPar[$i][1]?>"><?=html($arPar[$i][0])?></a><?
		} else {
			echo html($arPar[$i][0]);
		}
	}
	?></div><?
}

if (!$USER['SETTING']['SITE_ACTIVE']) {
	$arSite = $LIB['SITE']->Rows();
	setSetting('SITE_ACTIVE', $arSite[0]['ID']);
}

function setSetting($sAction, $arData, $bSerelize = false)
{
	global $DB, $USER;

	$arDataInsert = ($bSerelize ? serialize($arData) : $arData);

	$DB->Query("DELETE FROM `k2_user_setting` WHERE `USER` = '" . $USER['ID'] . "' AND `ACTION` = '" . DBS($sAction) . "'");
	$DB->Query("INSERT INTO `k2_user_setting` (`USER`, `ACTION`, `DATA`) VALUES ('" . $USER['ID'] . "', '" . DBS($sAction) . "', '" . DBS($arDataInsert) . "');");
	$USER['SETTING'][$sAction] = $arData;
}

function JSSafe($sText)
{
	$sText = str_replace("'", '', $sText);

	return $sText;
}

function permissionDenied()
{
	?><br>
	<table id="warning" style="margin-left:16px;">
		<tr>
			<td>У вас нет прав доступа к этой странице</td>
		</tr>
	</table><?
	include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/footer.php');
	exit;
}

function permissionCheck($sType, $sKey = 'INDEX')
{
	global $DB, $LIB, $USER;

	if ($USER['USER_GROUP'] == 1) {
		return;
	}

	if ($sType == 'SECTION') {
		global $_SECTION;
		if (!$nPermission = $USER['PERMISSION']['SECTION'][$_SECTION]) {
			$arSection = $LIB['SECTION']->ID($_SECTION);
			$nPermission = $arSection['PERMISSION'];
			if (!$nPermission) {
				$arSite = $LIB['SITE']->ID($arSection['SITE']);
				if (!$nPermission = $USER['PERMISSION']['SITE'][$arSite['ID']]) {
					$nPermission = $arSite['PERMISSION'];
				}
			}
		}
		if ($nPermission != 3) {
			permissionDenied();
		}
	} else if ($sType == 'SECTION_CONTENT') {
		global $_SECTION;
		if (!$nPermission = $USER['PERMISSION']['SECTION'][$_SECTION]) {
			$arSection = $LIB['SECTION']->ID($_SECTION);
			$nPermission = $arSection['PERMISSION'];
			if (!$nPermission) {
				$arSite = $LIB['SITE']->ID($arSection['SITE']);
				if (!$nPermission = $USER['PERMISSION']['SITE'][$arSite['ID']]) {
					$nPermission = $arSite['PERMISSION'];
				}
			}
		}
		if ($nPermission != 3 && $nPermission != 2) {
			permissionDenied();
		}
	} else if (($sType == 'SECTION_PERMISSION') && ($USER['USER_GROUP'] != 1)) {
		permissionDenied();
	} else if (($sType == 'MODULE') && ($sKey == 'INDEX')) {
		if (!$USER['PERMISSION']['DEFAULT']['MODULE']) {
			permissionDenied();
		}
	} else if (!$USER['PERMISSION']['DEFAULT'][$sType][$sKey]) {
		permissionDenied();
	}
}

function templateList($sModule)
{
	$sModule = strtolower($sModule);
	$sDir = $_SERVER['DOCUMENT_ROOT'] . '/k2/module/' . $sModule . '/template/';

	$arDir = scandir($sDir);
	for ($i = 0; $i < count($arDir); $i++) {
		if ($arDir[$i] != '.' && $arDir[$i] != '..' && is_dir($sDir . $arDir[$i])) {
			include($sDir . $arDir[$i] . '/name.php');
			$arName[] = array('M' => filemtime($sDir . $arDir[$i] . '/name.php'), 'TEMPLATE' => $arDir[$i], 'NAME' => $sName);
		}
	}
	@sort($arName);

	return $arName;
}

function userSettingView($sAdd = false, $arPar)
{
	global $USER, $DB;

	if ($sAdd) {
		$DB->Query("DELETE FROM `k2_user_setting_view` WHERE `USER` = " . $USER['ID'] . " AND `TYPE` = " . (int)$arPar['TYPE'] . " AND `OBJECT` = " . (int)$arPar['OBJECT']);
		$DB->Insert("INSERT INTO `k2_user_setting_view` (`USER`, `TYPE`, `OBJECT`, `DEFAULT`, `PREVIEW`, `DATA`) VALUES (" . $USER['ID'] . ", " . (int)$arPar['TYPE'] . ", " . (int)$arPar['OBJECT'] . ", " . (int)$arPar['DEFAULT'] . ", " . (int)$arPar['PREVIEW'] . ", '" . DBS(serialize($arPar['DATA'])) . "');");
	} else {
		if (!($arRows = $DB->Row("SELECT `DEFAULT`, `PREVIEW`, `DATA` FROM k2_user_setting_view WHERE `TYPE` = " . (int)$arPar['TYPE'] . " AND `OBJECT` = " . (int)$arPar['OBJECT'] . " AND `USER` = " . $USER['ID'] . " LIMIT 1"))) {
			$arRows = $DB->Row("SELECT `USER`, `DEFAULT`, `PREVIEW`, `DATA` FROM k2_user_setting_view WHERE `TYPE` = " . (int)$arPar['TYPE'] . " AND `OBJECT` = " . (int)$arPar['OBJECT'] . " AND `DEFAULT` = 1 LIMIT 1");
		}

		return $arRows;
	}

	return false;
}

function userSettingSession($sAdd = false)
{
	global $_SORT;
	if ($sAdd) {
		if ($_SORT) {
			$arExp = explode('.', $_SORT);
			if (in_array($arExp[1], array('asc', 'desc'))) {
				$_SESSION[$_SERVER['PHP_SELF']]['PAGE_SORT'] = array('FIELD' => $arExp[0], 'METHOD' => $arExp[1]);
			}
		}
		if ($_GET['page_size'] > 0) {
			$_SESSION[$_SERVER['PHP_SELF']]['PAGE_SIZE'] = $_GET['page_size'];
		}

	}

	return $_SESSION[$_SERVER['PHP_SELF']];
}

function tableHead($arTableHead, $arSort = array())
{
	for ($i = 0; $i < count($arTableHead); $i++) {
		if ($arTableHead[$i]['HTML']) {
			echo $arTableHead[$i]['HTML'];
			continue;
		}
		if ($arTableHead[$i]['SORT']) {
			?>
			<th class="sort <?
			if ($arTableHead[$i]['FIELD'] == $arSort['FIELD']) {
				echo $arSort['METHOD'];
			}
			?>"><a href="<?
			$sURLMethod = 'asc';
			if ($arTableHead[$i]['FIELD'] == $arSort['FIELD']) {
				if ($arSort['METHOD'] == 'asc') {
					$sURLMethod = 'desc';
				} else {
					$sURLMethod = 'asc';
				}
			}
			echo urlQuery(array('sort' => $arTableHead[$i]['FIELD'] . '.' . $sURLMethod));
			?>"><?=html($arTableHead[$i]['NAME'])?></th><?
		} else {
			?>
			<th><?=html($arTableHead[$i]['NAME'])?></th><?
		}
	}
}

function tableBody($arPar)
{
	global $LIB, $DB;

	foreach ($arPar['CONTENT'] as $sKey => $sText) {
		$arField = $arPar['FIELD'][$sKey];

		if (!$arField['ACTIVE']) {
			continue;
		}
		$arValue = array();
		if ($arField['MULTIPLE']) {
			$arValue = clearArray(explode(',', $sText));
		} else {
			$arValue[0] = $sText;
		}

		?>
		<td<?
		if ($arField['ALIGN']) {
			?> align="<?=$arField['ALIGN']?>"<?
		}
		?>><?
		if (in_array($arField['FORMAT'], array('DATE', 'INPUT_DATE'))) {
			plus(dateFormat($sText, 'd.m.Y'));
		} else if (in_array($arField['FORMAT'], array('DATE_TIME', 'INPUT_DATE_TIME'))) {
			plus(dateFormat($sText));
		} else if (in_array($arField['FORMAT'], array('USER', 'REFERENCE_USER'))) {
			if ($arPar['USER_LOGIN'][$arValue[0]]) {
				?><a
				href="/k2/admin/user/edit.php?id=<?=$arValue[0]?>"><?=html($arPar['USER_LOGIN'][$arValue[0]])?></a><?
				if ($arValue[1]) {
					?> ...<?
				}
			} else {
				?>-<?
			}
		} else if ($arField['FORMAT'] == 'USER_GROUP') {
			if ($arPar['CONTENT']['_USER_GROUP_NAME']) {
				?><a
				href="/k2/admin/user/group/edit.php?id=<?=$sText?>"><?=html($arPar['CONTENT']['_USER_GROUP_NAME'])?></a><?
			} else {
				?>-<?
			}
		} else if ($arField['FORMAT'] == 'SELECT') {
			if ($arPar['CONTENT'][$sKey]) {
				echo html($arPar['CONTENT']['_' . $sKey . '_NAME']);
				if ($arValue[1]) {
					?> ...<?
				}
			} else {
				?>-<?
			}
		} else if ($arField['FORMAT'] == 'REFERENCE_SECTION') {
			if ($arPar['CONTENT'][$sKey]) {
				?><a
				href="/k2/admin/section/content/?section=<?=$arValue[0]?>"><?=html($arPar['CONTENT']['_' . $sKey . '_NAME'])?></a><?
				if ($arValue[1]) {
					?> ...<?
				}
			} else {
				?>-<?
			}
		} else if ($arField['FORMAT'] == 'REFERENCE_CATEGORY') {
			if ($arPar['CONTENT'][$sKey]) {
				?><a
				href="/k2/admin/section/content/category/edit.php?section=<?=$arPar['CONTENT']['_' . $sKey . '_SECTION']?>&section_block=<?=$arPar['CONTENT']['_' . $sKey . '_SECTION_BLOCK']?>&id=<?=$arPar['CONTENT']['_' . $sKey . '_ID']?>"><?=html($arPar['CONTENT']['_' . $sKey . '_NAME'])?></a><?
				if ($arValue[1]) {
					?> ...<?
				}
			} else {
				?>-<?
			}
		} else if ($arField['FORMAT'] == 'REFERENCE_ELEMENT') {
			if ($arPar['CONTENT'][$sKey]) {
				?><a
				href="/k2/admin/section/content/element/edit.php?section=<?=$arPar['CONTENT']['_' . $sKey . '_SECTION']?>&section_block=<?=$arPar['CONTENT']['_' . $sKey . '_SECTION_BLOCK']?>&id=<?=$arPar['CONTENT']['_' . $sKey . '_ID']?>"><?=html($arPar['CONTENT']['_' . $sKey . '_NAME'])?></a><?
				if ($arValue[1]) {
					?> ...<?
				}
			} else {
				?>-<?
			}
		} else if ($arField['FORMAT'] == 'FILE') {
			if ($arPar['CONTENT'][$sKey]) {
				if ($arPar['PREVIEW'] && $arPar['CONTENT']['_' . $sKey . '_WIDTH']) {
					if ($arPhoto = $LIB['PHOTO']->Preview($arValue[0], array('WIDTH' => 60, 'HEIGHT' => 60))) {
						?><a href="/files/original/<?=$arPar['CONTENT']['_' . $sKey . '_PATH']?>"
						     title="<?=$arPar['CONTENT']['_' . $sKey . '_NAME']?> (<?=$arPar['CONTENT']['_' . $sKey . '_WIDTH']?>x<?=$arPar['CONTENT']['_' . $sKey . '_HEIGHT']?> <?=fileByte($arPar['CONTENT']['_' . $sKey . '_SIZE'])?>)"
						     target="_blank"><img src="<?=$arPhoto['PATH']?>" width="<?=$arPhoto['WIDTH']?>"
						                          height="<?=$arPhoto['HEIGHT']?>" class="preview"></a><?
					}
				} else {
					?><a href="/files/original/<?=$arPar['CONTENT']['_' . $sKey . '_PATH']?>" title="<?
					if ($arPar['CONTENT']['_' . $sKey . '_WIDTH']) {
						echo $arPar['CONTENT']['_' . $sKey . '_WIDTH'] . 'x' . $arPar['CONTENT']['_' . $sKey . '_HEIGHT'] . ' ';
					}
					?><?=fileByte($arPar['CONTENT']['_' . $sKey . '_SIZE'])?>"
					     target="_blank"><?=$arPar['CONTENT']['_' . $sKey . '_NAME']?></a><?
					if ($arValue[1]) {
						?> ...<?
					}
				}
			} else {
				?>-<?
			}
		} else if ($arField['FORMAT'] == 'CATEGORY_NAME') {
			?><a href="<?=$arPar['SECTION']['URL']?>" class="sectionLink"><span
				class="icon section"></span><?=html($sText)?></a><?
		} else if ($arField['FORMAT'] == 'CHECKBOX') {
			echo($sText ? 'Да' : 'Нет');
		} else {
			$sText = strip_tags(str_replace(array("\r", "\n"), array(' ', ' '), $sText));
			if (mb_strlen($sText, 'UTF-8') > 100) {
				$sText = preg_replace("#(.+)\s.+?$#", "\\1", mb_substr($sText, 0, 100, 'UTF-8')) . ' ...';
			}
			echo $sText;
		}
		?></td><?
	}
}

function fieldTableHead($sTable, $QB, $arField, $arSort, $arTableHead)
{
	for ($i = 65; $i < 91; $i++) {
		$arAlias[] = chr($i) . chr($i);
	}
	$i = 0;
	foreach ($arField as $sKey => $arList) {
		if (!$arList['ACTIVE']) {
			continue;
		}
		if ($arList['FORMAT'] == 'REFERENCE_USER') {
			if ($arField[$sKey]['MULTIPLE']) {
				$QB->LeftJoin('k2_user ' . $arAlias[$i] . ' ON SUBSTR(' . $sTable . '.' . $sKey . ', 1, LENGTH(' . $arAlias[$i] . '.ID) + 2) = CONCAT(\',\', ' . $arAlias[$i] . '.ID, \',\')');
			} else {
				$QB->LeftJoin('k2_user ' . $arAlias[$i] . ' ON ' . $sTable . '.' . $sKey . ' = ' . $arAlias[$i] . '.ID');
			}
			$QB->Select($arAlias[$i] . '.LOGIN _' . $sKey . '_LOGIN');
			$QB->ConcatField($arAlias[$i] . '.LOGIN', 1);
			if ($arSort['FIELD'] == $arList['FIELD']) {
				$QB->OrderBy($arAlias[$i] . '._' . $sKey . '_LOGIN ' . $arSort['METHOD']);
			}
			$i++;
		} else if ($arList['FORMAT'] == 'USER_GROUP') {
			$QB->LeftJoin('k2_user_group ' . $arAlias[$i] . ' ON ' . $sTable . '.USER_GROUP = ' . $arAlias[$i] . '.ID');
			$QB->Select($arAlias[$i] . '.NAME _USER_GROUP_NAME');
			$QB->ConcatField($arAlias[$i] . '.NAME', 1);
			if ($arSort['FIELD'] == $arList['FIELD']) {
				$QB->OrderBy($arAlias[$i] . '.NAME ' . $arSort['METHOD']);
			}
			$i++;
		} else if ($arList['FORMAT'] == 'SELECT') {
			if ($arField[$sKey]['MULTIPLE']) {
				$QB->LeftJoin('k2_select_option ' . $arAlias[$i] . ' ON SUBSTR(' . $sTable . '.' . $sKey . ', 1, LENGTH(' . $arAlias[$i] . '.ID) + 2) = CONCAT(\',\', ' . $arAlias[$i] . '.ID, \',\')');
			} else {
				$QB->LeftJoin('k2_select_option ' . $arAlias[$i] . ' ON ' . $sTable . '.' . $sKey . ' = ' . $arAlias[$i] . '.ID');
			}
			$QB->Select($arAlias[$i] . '.NAME _' . $sKey . '_NAME');
			$QB->ConcatField($arAlias[$i] . '.NAME', 1);
			if ($arSort['FIELD'] == $arList['FIELD']) {
				$QB->OrderBy($arAlias[$i] . '._' . $sKey . '_NAME ' . $arSort['METHOD']);
			}
			$i++;
		} else if ($arList['FORMAT'] == 'REFERENCE_SECTION') {
			if ($arField[$sKey]['MULTIPLE']) {
				$QB->LeftJoin('k2_section ' . $arAlias[$i] . ' ON SUBSTR(' . $sTable . '.' . $sKey . ', 1, LENGTH(' . $arAlias[$i] . '.ID) + 2) = CONCAT(\',\', ' . $arAlias[$i] . '.ID, \',\')');
			} else {
				$QB->LeftJoin('k2_section ' . $arAlias[$i] . ' ON ' . $sTable . '.' . $sKey . ' = ' . $arAlias[$i] . '.ID');
			}
			$QB->Select($arAlias[$i] . '.NAME _' . $sKey . '_NAME');
			$QB->ConcatField($arAlias[$i] . '.NAME', 1);
			if ($arSort['FIELD'] == $arList['FIELD']) {
				$QB->OrderBy($arAlias[$i] . '._' . $sKey . '_NAME ' . $arSort['METHOD']);
			}
			$i++;
		} else if ($arList['FORMAT'] == 'REFERENCE_CATEGORY') {
			if ($arField[$sKey]['MULTIPLE']) {
				$QB->LeftJoin('k2_block' . $arField[$sKey]['BLOCK'] . 'category ' . $arAlias[$i] . ' ON SUBSTR(' . $sTable . '.' . $sKey . ', 1, LENGTH(' . $arAlias[$i] . '.ID) + 2) = CONCAT(\',\', ' . $arAlias[$i] . '.ID, \',\')');
			} else {
				$QB->LeftJoin('k2_block' . $arField[$sKey]['BLOCK'] . 'category ' . $arAlias[$i] . ' ON ' . $sTable . '.' . $sKey . ' = ' . $arAlias[$i] . '.ID');
			}
			$QB->Select($arAlias[$i] . '.NAME _' . $sKey . '_NAME');
			$QB->Select($arAlias[$i] . '.ID _' . $sKey . '_ID');
			$QB->Select($arAlias[$i] . '.SECTION_BLOCK _' . $sKey . '_SECTION_BLOCK');
			$QB->Select($arAlias[$i] . '.SECTION _' . $sKey . '_SECTION');
			$QB->ConcatField($arAlias[$i] . '.NAME', 1);
			if ($arSort['FIELD'] == $arList['FIELD']) {
				$QB->OrderBy($arAlias[$i] . '._' . $sKey . '_NAME ' . $arSort['METHOD']);
			}
			$i++;
		} else if ($arList['FORMAT'] == 'REFERENCE_ELEMENT') {
			if ($arField[$sKey]['MULTIPLE']) {
				$QB->LeftJoin('k2_block' . $arField[$sKey]['BLOCK'] . ' ' . $arAlias[$i] . ' ON SUBSTR(' . $sTable . '.' . $sKey . ', 1, LENGTH(' . $arAlias[$i] . '.ID) + 2) = CONCAT(\',\', ' . $arAlias[$i] . '.ID, \',\')');
			} else {
				$QB->LeftJoin('k2_block' . $arField[$sKey]['BLOCK'] . ' ' . $arAlias[$i] . ' ON ' . $sTable . '.' . $sKey . ' = ' . $arAlias[$i] . '.ID');
			}
			$QB->Select($arAlias[$i] . '.NAME _' . $sKey . '_NAME');
			$QB->Select($arAlias[$i] . '.ID _' . $sKey . '_ID');
			$QB->Select($arAlias[$i] . '.SECTION_BLOCK _' . $sKey . '_SECTION_BLOCK');
			$QB->Select($arAlias[$i] . '.SECTION _' . $sKey . '_SECTION');
			$QB->ConcatField($arAlias[$i] . '.NAME', 1);
			if ($arSort['FIELD'] == $arList['FIELD']) {
				$QB->OrderBy($arAlias[$i] . '._' . $sKey . '_NAME ' . $arSort['METHOD']);
			}
			$i++;
		} else if ($arList['FORMAT'] == 'FILE') {
			if ($arField[$sKey]['MULTIPLE']) {
				$QB->LeftJoin('k2_file ' . $arAlias[$i] . ' ON SUBSTR(' . $sTable . '.' . $sKey . ', 1, LENGTH(' . $arAlias[$i] . '.ID) + 2) = CONCAT(\',\', ' . $arAlias[$i] . '.ID, \',\')');
			} else {
				$QB->LeftJoin('k2_file ' . $arAlias[$i] . ' ON ' . $sTable . '.' . $sKey . ' = ' . $arAlias[$i] . '.ID');
			}
			$QB->Select($arAlias[$i] . '.NAME AS _' . $sKey . '_NAME');
			$QB->ConcatField($arAlias[$i] . '.NAME', 1);
			$QB->Select($arAlias[$i] . '.PATH AS _' . $sKey . '_PATH');
			$QB->Select($arAlias[$i] . '.SIZE AS _' . $sKey . '_SIZE');
			$QB->Select($arAlias[$i] . '.WIDTH AS _' . $sKey . '_WIDTH');
			$QB->Select($arAlias[$i] . '.HEIGHT AS _' . $sKey . '_HEIGHT');
			if ($arSort['FIELD'] == $arList['FIELD']) {
				$QB->OrderBy('_' . $sKey . '_NAME ' . $arSort['METHOD']);
			}
			$i++;
		}
		$QB->Select($sTable . '.' . $sKey);
		$QB->ConcatField($sTable . '.' . $sKey);

		$arTableHead[] = array('FIELD' => $sKey, 'NAME' => $arList['NAME'], 'SORT' => true);
	}

	return $arTableHead;
}

function fieldFormat($sTable, $arField)
{
	global $LIB;

	$arFieldFormat = array('', '', 'TORF', 'SELECT', 'FILE', 'RELATION');
	$arFields = $LIB['FIELD']->Rows($sTable);
	for ($i = 0; $i < count($arFields); $i++) {
		$sFormat = $arFields[$i]['TYPE'];
		if ($arFields[$i]['SETTING']['TYPE']) {
			$sFormat .= '_' . $arFields[$i]['SETTING']['TYPE'];
		}
		$arField[$arFields[$i]['FIELD']] = array('NAME' => $arFields[$i]['NAME'], 'FORMAT' => $sFormat, 'ALIGN' => 'left', 'ACTIVE' => 0, 'MULTIPLE' => $arFields[$i]['MULTIPLE']);
		if ($arFields[$i]['SETTING']['BLOCK']) {
			$arField[$arFields[$i]['FIELD']]['BLOCK'] = $arFields[$i]['SETTING']['BLOCK'];
		}
	}

	return $arField;
}

function plus($sVal)
{
	if ($sVal) {
		echo $sVal;
	} else {
		?>-<?
	}
}

function userAllLogin()
{
	global $DB;

	$arRows = $DB->Rows("SELECT `ID`, `LOGIN` FROM `k2_user`");
	for ($i = 0; $i < count($arRows); $i++) {
		$arUserLogin[$arRows[$i]['ID']] = $arRows[$i]['LOGIN'];
	}

	return $arUserLogin;
}

function formError($sError)
{
	if ($sError) {
		?>
		<div class="error"><?=$sError?></div><?
	} elseif ($_GET['complite']) {
		?>
		<div class="complite">Данные сохранены</div><?
	}
}

function fieldNote($sText = '')
{
	?>
	<div class="note"><?=$sText?></div><?
}

?>