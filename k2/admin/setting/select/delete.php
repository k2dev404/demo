<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SELECT');

$LIB['SELECT']->Delete($_ID);
Redirect('/k2/admin/setting/select/');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>