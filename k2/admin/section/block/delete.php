<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

$arBlockSection = $LIB['SECTION_BLOCK']->ID($_ID);
$LIB['SECTION_BLOCK']->Delete($_ID);
Redirect('/k2/admin/section/block/?section='.$arBlockSection['SECTION']);
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>