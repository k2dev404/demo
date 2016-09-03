<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/header.php');
permissionCheck('MODULE', $_REQUEST['module']);
$LIB['MODULE']->DeleteTemplate($_REQUEST['module'], $_REQUEST['template']);
Redirect('/k2/admin/module/' . strtolower($_REQUEST['module']) . '/template/');
?>