<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION');

if($_REQUEST['session'] != $USER['SESSION']){
	exit();
}

$LIB['SECTION']->Delete($_ID);
Redirect('/k2/admin/section/');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>