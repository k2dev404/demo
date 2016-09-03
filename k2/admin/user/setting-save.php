<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/inc/function.php');

$arField = array();
foreach ($_POST['FIELD'] as $sKey => $sValue) {
	$arField[$sKey] = array('NAME' => $_POST['NAME'][$sKey], 'FORMAT' => $_POST['FORMAT'][$sKey], 'ALIGN' => $_POST['ALIGN'][$sKey], 'ACTIVE' => (int)$_POST['ACTIVE'][$sKey]);
}

userSettingView(1, array('TYPE' => 10, 'DEFAULT' => $_POST['DEFAULT'], 'PREVIEW' => $_POST['PREVIEW'], 'DATA' => $arField));
Redirect('/k2/admin/user/');
?>