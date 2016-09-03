<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('SECTION_CONTENT');

$DB->Query("DELETE FROM `k2_user_setting` WHERE `USER` = '".$USER['ID']."' AND (`ACTION` = 'CATEGORY_MOVE' OR `ACTION` = 'ELEMENT_MOVE');");

Redirect($_SERVER['HTTP_REFERER']);
?>