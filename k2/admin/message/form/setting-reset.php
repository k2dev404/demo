<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');

if(!$arForm = $LIB['BLOCK']->ID($_GET['form'])){
	exit;
}

$DB->Query("DELETE FROM `k2_user_setting_view` WHERE `TYPE` = 11 AND `USER` = ".$USER['ID']." AND `OBJECT` = '".$arForm['ID']."'");
Redirect($_BACK);
?>