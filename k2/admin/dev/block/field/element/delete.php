<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

if($_REQUEST['session'] != $USER['SESSION']){
	exit();
}

if(!$arField = $LIB['FIELD']->ID($_ID)){
	Redirect('/k2/admin/dev/block/');
}
$nBlock = preg_replace("#k2_block(\d+)#", "\\1", $arField['TABLE']);

$LIB['FIELD']->Delete($_ID);
Redirect('/k2/admin/dev/block/field/element/?id='.$nBlock);
?>