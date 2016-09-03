<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

if($_REQUEST['session'] != $USER['SESSION']){
	exit();
}

$LIB['NAV']->Delete($_ID);
Redirect('/k2/admin/dev/nav/');
?>