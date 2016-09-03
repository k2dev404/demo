<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

if($_REQUEST['session'] != $USER['SESSION']){
	exit();
}

$LIB['BLOCK']->Delete($_ID);
Redirect('/k2/admin/dev/block/');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>