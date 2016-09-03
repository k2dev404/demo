<?

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/function.php');

permissionCheck('SECTION_CONTENT');

if(!$USER['SETTING']['ELEMENT_MOVE']){
	Redirect();
}

$DB->Query("UPDATE `k2_block".$USER['SETTING']['ELEMENT_MOVE']['BLOCK']."` SET `SECTION_BLOCK` = '".$_SECTION_BLOCK."', `CATEGORY` = '".$_CATEGORY."', `DATE_CHANGE` = NOW() WHERE `ID` = '".$USER['SETTING']['ELEMENT_MOVE']['ID']."';");
$DB->Query("DELETE FROM `k2_user_setting` WHERE `USER` = '".$USER['ID']."' AND `ACTION` = 'ELEMENT_MOVE';");

Redirect('/k2/admin/section/content/?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&category='.$_CATEGORY);
?>