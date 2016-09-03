<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/file-manager/header.php');
if($arFile = $LIB['FILE']->ID($_ID)){
	$LIB['FILE']->Delete($_ID);
	Redirect('/k2/admin/system/file-manager/manager.php?field='.$_REQUEST['field'].'&parent='.$arFile['DIR']);
}
?>