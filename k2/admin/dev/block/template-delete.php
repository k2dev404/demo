<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

if($_GET['path']){
	unlink($_SERVER['DOCUMENT_ROOT'].'/k2/dev'.$_GET['path']);
	Redirect($_SERVER['HTTP_REFERER']);
}

if(!$arTemplate = $LIB['TEMPLATE']->ID($_ID)){
	Redirect('/k2/admin/dev/block/');
}
$LIB['TEMPLATE']->Delete($_ID);
Redirect('/k2/admin/dev/block/edit.php?id='.$arTemplate['OBJECT_ID']);
?>