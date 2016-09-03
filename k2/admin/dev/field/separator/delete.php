<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('DEV');

if($arGroup = $LIB['FIELD_SEPARATOR']->ID($_ID)){
	$LIB['FIELD_SEPARATOR']->Delete($_ID);
	Redirect($_BACK);
}
Redirect('/k2/admin/');
?>