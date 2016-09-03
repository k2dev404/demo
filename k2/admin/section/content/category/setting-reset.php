<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');

if(!$arBlock = $LIB['BLOCK']->ID($_BLOCK)){
	exit;
}

$DB->Query("DELETE FROM `k2_user_setting_view` WHERE `TYPE` = 2 AND `USER` = ".$USER['ID']." AND `OBJECT` = '".$_BLOCK."'");
Redirect($_BACK);
?>