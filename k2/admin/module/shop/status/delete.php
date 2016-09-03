<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

$MOD['SHOP_STATUS']->Delete($_ID);
Redirect('/k2/admin/module/shop/status/');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>