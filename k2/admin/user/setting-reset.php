<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/inc/function.php');

$DB->Query("DELETE FROM `k2_user_setting_view` WHERE `TYPE` = 10 AND `USER` = " . $USER['ID']);
Redirect('/k2/admin/user/');
?>