<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

if($_ID){
	$LIB['BLOCK_CATEGORY']->Delete($_ID, $_SECTION_BLOCK);
}
if($_POST['ID']){
	for($i=0; $i<count($_POST['ID']); $i++)
	{
		$LIB['BLOCK_CATEGORY']->Delete($_POST['ID'][$i], $_SECTION_BLOCK);
	}
}

Redirect('/k2/admin/section/content/?section='.$_SECTION."&section_block=".$_SECTION_BLOCK."&category=".$_CATEGORY);
?>