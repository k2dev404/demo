<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SELECT');

$arSelectRow = $LIB['SELECT_OPTION']->ID($_ID);
$LIB['SELECT_OPTION']->Delete($_ID);
Redirect('/k2/admin/setting/select/option/?id='.$arSelectRow['SELECT']);
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>