<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

if($_ID){
	$MOD['SHOP_ORDER']->Delete($_ID);
}
if($_POST['ID']){
	for($i=0; $i<count($_POST['ID']); $i++)
	{
		$MOD['SHOP_ORDER']->Delete($_POST['ID'][$i]);
	}
}
Redirect('/k2/admin/module/shop/');
?>