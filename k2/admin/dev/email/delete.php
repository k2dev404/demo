<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'EMAIL');

if($_REQUEST['session'] != $USER['SESSION']){
	exit();
}

$LIB['EMAIL']->Delete($_ID);
Redirect('/k2/admin/dev/email/');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>