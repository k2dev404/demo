<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('DEV');

$arGroup = explode(',', $_POST['GROUP']);
$arField = explode(',', $_POST['FIELD']);
for($i=0; $i<count($arGroup); $i++)
{
	$sName = ($arGroup[$i] == 'true'?'FIELD_SEPARATOR':'FIELD');
	if($arObj = $LIB[$sName]->ID($arField[$i])){
		$arObj['SORT'] = ($i+1)*10;
		$LIB[$sName]->Edit($arField[$i], $arObj);
	}
}
?>