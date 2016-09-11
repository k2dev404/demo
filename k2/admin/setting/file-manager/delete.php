<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/index.php');
permissionCheck('SETTING');

if($_REQUEST['session'] != $USER['SESSION']){
	exit();
}

if($_POST['DIR'] && is_array($_POST['DIR'])){
	foreach($_POST['DIR'] as $sDir)
	{
		FDir::Delete(urldecode($sDir));
	}
}

if($_POST['FILE'] && is_array($_POST['FILE'])){
	foreach($_POST['FILE'] as $sDir)
	{
		FFile::Delete(urldecode($sDir));
	}
}

if($_GET['file']){
	FFile::Delete(urldecode($_GET['dir']).urldecode($_GET['file']));
}
else
if($_GET['dir']){
	FDir::Delete(urldecode($_GET['dir']));
}

Redirect($_SERVER['HTTP_REFERER']);
?>