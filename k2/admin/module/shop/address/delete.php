<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

if(!$arField = $LIB['FIELD']->ID($_ID)){
	Redirect('/k2/admin/module/shop/address/');
}

$LIB['FIELD']->Delete($_ID);
Redirect('/k2/admin/module/shop/address/');
?>