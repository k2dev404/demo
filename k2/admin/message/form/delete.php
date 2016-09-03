<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

if($_ID){
	$LIB['FORM_MESSAGE']->Delete($_ID, $_GET['form']);
}

if($_POST['ID']){
	for($i=0; $i<count($_POST['ID']); $i++)
	{
		$LIB['FORM_MESSAGE']->Delete($_POST['ID'][$i], $_REQUEST['form']);
	}
}

Redirect('/k2/admin/message/form/?form='.(int)$_REQUEST['form']);
?>