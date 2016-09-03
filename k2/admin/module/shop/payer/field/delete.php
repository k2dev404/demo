<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

if(!$arField = $LIB['FIELD']->ID($_ID)){
	Redirect('/k2/admin/module/shop/payer/');
}

$nPayer = preg_replace("#k2_mod_shop_payer(\d+)#", "\\1", $arField['TABLE']);
$LIB['FIELD']->Delete($_ID);
Redirect('/k2/admin/module/shop/payer/field/?id='.$nPayer);
?>