<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SEO');

$MOD['SEO_REDIRECT']->Delete($_ID);
Redirect('/k2/admin/module/seo/redirect/');

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>