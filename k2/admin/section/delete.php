<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/header.php');
permissionCheck('SECTION');

$LIB['SECTION']->Delete($_ID);
Redirect('/k2/admin/section/');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/footer.php');
?>