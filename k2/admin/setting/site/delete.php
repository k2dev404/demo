<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SETTING');

if($_REQUEST['session'] != $USER['SESSION']){
	exit();
}

$LIB['SITE']->Delete($_ID);
Redirect('/k2/admin/setting/site/');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>