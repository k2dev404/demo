<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$LIB['BLOCK']->Delete($_ID);
Redirect('/k2/admin/dev/block/');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>