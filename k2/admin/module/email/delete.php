<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'EMAIL');

$MOD['EMAIL']->Delete($_ID);
Redirect('/k2/admin/module/email/');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>