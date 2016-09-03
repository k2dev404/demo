<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

$MOD['SHOP_DELIVERY']->Delete($_ID);
Redirect('/k2/admin/module/shop/delivery/');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>