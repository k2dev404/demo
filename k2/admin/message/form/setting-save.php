<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');

if(!$arForm = $LIB['BLOCK']->ID($_GET['form'])){
	Redirect();
}

$arField = array();
foreach($_POST['FIELD'] as $sKey=>$sValue)
{
	$arField[$sKey] = array(
	'NAME' => $_POST['NAME'][$sKey],
	'FORMAT' => $_POST['FORMAT'][$sKey],
	'ALIGN' => $_POST['ALIGN'][$sKey],
	'ACTIVE' => (int)$_POST['ACTIVE'][$sKey]);
}

userSettingView(1, array('TYPE' => 11, 'OBJECT' => $_GET['form'], 'DEFAULT' => $_POST['DEFAULT'], 'PREVIEW' => $_POST['PREVIEW'], 'DATA' => $arField));

Redirect($_BACK);
?>