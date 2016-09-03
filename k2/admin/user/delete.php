<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/header.php');
permissionCheck('USER');

if ($_ID) {
	$LIB['USER']->Delete($_ID);
}
if ($_POST['ID']) {
	for ($i = 0; $i < count($_POST['ID']); $i++) {
		$LIB['USER']->Delete($_POST['ID'][$i]);
	}
}
Redirect('/k2/admin/user');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/footer.php');
?>