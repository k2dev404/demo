<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$LIB['FORM']->Delete($_ID);
Redirect('/k2/admin/dev/form/');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>