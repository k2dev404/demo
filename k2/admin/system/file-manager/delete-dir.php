<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/file-manager/header.php');
if($arDir = $LIB['FILE_DIR']->ID($_ID)){
	$LIB['FILE_DIR']->Delete($_ID);
	Redirect('/k2/admin/system/file-manager/manager.php?field='.$_REQUEST['field'].'&parent='.$arDir['PARENT']);
}
?>