<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$LIB['DESIGN']->Delete($_ID);
Redirect('/k2/admin/dev/design/');
?>