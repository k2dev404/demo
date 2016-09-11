<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/index.php');
permissionCheck('SETTING');

$CONTROLLER = new K2;

$CONTROLLER->Template('/design/layer/header.php');

if($_POST['action'] == 'upload' && $_FILES['FILE']['tmp_name']){
	if(FFile::Upload($_FILES['FILE'], urldecode($_GET['dir']))){
		$CONTROLLER->Complete = true;
	}else{
		$CONTROLLER->Error = true;
	}
}

$CONTROLLER->Template();
$CONTROLLER->Template('/design/layer/footer.php');
?>