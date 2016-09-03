<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$LIB['NAV']->Delete($_ID);
Redirect('/k2/admin/dev/nav/');
?>