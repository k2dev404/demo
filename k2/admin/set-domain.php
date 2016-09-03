<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/inc/function.php');

$arSite = $LIB['SITE']->Rows();
$nSite = $arSite[0]['ID'];
if ($_ID) {
	$arSite = $LIB['SITE']->ID($_ID);
	$nSite = $arSite['ID'];
}
setSetting('SITE_ACTIVE', $nSite);
Redirect('/k2/admin/');
?>