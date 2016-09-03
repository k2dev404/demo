<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SELECT');

if($_REQUEST['session'] != $USER['SESSION']){
	exit();
}

$LIB['SELECT']->Delete($_ID);
Redirect('/k2/admin/setting/select/');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>