<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('USER', 'GROUP');

if($_REQUEST['session'] != $USER['SESSION']){
	exit();
}

$LIB['USER_GROUP']->Delete($_ID);
Redirect('/k2/admin/user/group/');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>