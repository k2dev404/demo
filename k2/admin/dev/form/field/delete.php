<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

if($_REQUEST['session'] != $USER['SESSION']){
	exit();
}

if(!$arField = $LIB['FIELD']->ID($_ID)){
	Redirect('/k2/admin/dev/form/');
}
$nForm = preg_replace("#k2_form(\d+)#", "\\1", $arField['TABLE']);

$LIB['FIELD']->Delete($_ID);
Redirect('/k2/admin/dev/form/field/?id='.$nForm);
?>