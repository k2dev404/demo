<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('SECTION_CONTENT');

if(!$USER['SETTING']['CATEGORY_MOVE']){
	Redirect();
}

for($i=0; $i<count($USER['SETTING']['CATEGORY_MOVE']['ID']); $i++)
{
	$DB->Query("UPDATE `k2_block".$USER['SETTING']['CATEGORY_MOVE']['BLOCK']."category` SET `SECTION` = '".$_SECTION."', `SECTION_BLOCK` = '".$_SECTION_BLOCK."', `PARENT` = '".$_CATEGORY."' WHERE `ID` = '".$USER['SETTING']['CATEGORY_MOVE']['ID'][$i]."';");
}
$DB->Query("DELETE FROM `k2_user_setting` WHERE `USER` = '".$USER['ID']."' AND `ACTION` = 'CATEGORY_MOVE';");

Redirect($_SERVER['HTTP_REFERER']);
?>