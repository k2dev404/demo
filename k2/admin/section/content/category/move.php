<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('SECTION_CONTENT');

$DB->Query("DELETE FROM `k2_user_setting` WHERE `USER` = '".$USER['ID']."' AND `ACTION` = 'CATEGORY_MOVE';");

$DB->Query("
INSERT INTO `k2_user_setting` (
	`USER`,
	`ACTION`,
	`DATA`
)VALUES(
	'".$USER['ID']."', 'CATEGORY_MOVE', '".DBS(serialize(array('ID' => $_POST['ID'], 'SECTION' => $_SECTION, 'SECTION_BLOCK' => $_SECTION_BLOCK, 'BLOCK' => $_BLOCK, 'CATEGORY' => $_CATEGORY)))."'
);");

Redirect($_SERVER['HTTP_REFERER'].'&move_message=1');
?>