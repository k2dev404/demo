<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/inc/function.php');
permissionCheck('SECTION');

if (!$_GET['code']) {
	exit;
}

if (!$arSectionFrom = $LIB['SECTION']->ID($_GET['section_from'])) {
	exit;
}
if (!$arSectionTo = $LIB['SECTION']->ID($_GET['section_to'])) {
	exit;
}

if ($_GET['code'] == 1) {
	$nSort = 0;
	if ($arRow = $DB->Row("SELECT `SORT` FROM `k2_section` WHERE `PARENT` = '" . $arSectionTo['ID'] . "' ORDER BY `SORT` DESC LIMIT 1")) {
		$nSort = $arRow['SORT'] + 9;
	}
	$arSectionFrom['PARENT'] = $arSectionTo['ID'];
	$arSectionFrom['SORT'] = $nSort;
	$LIB['SECTION']->Edit($arSectionFrom['ID'], $arSectionFrom);
}

if ($_GET['code'] == 2) {
	$nSortSection = 0;
	$arSectionChild = $LIB['SECTION']->Child($arSectionTo['PARENT']);
	for ($i = 0, $j = 10; $i < count($arSectionChild); $i++) {
		$nParent = $arSectionChild[$i]['PARENT'];
		if ($arSectionChild[$i]['ID'] == $arSectionTo['ID']) {
			$nSortSection = $j + 9;
		}
		$LIB['SECTION']->Edit($arSectionChild[$i]['ID'], array('SORT' => $j));
		$j += 10;
	}

	$LIB['SECTION']->Edit($arSectionFrom['ID'], array('SORT' => $nSortSection, 'PARENT' => $arSectionTo['PARENT']));
}

Redirect($_SERVER['HTTP_REFERER']);
?>