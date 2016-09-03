<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
permissionCheck('SECTION_CONTENT');

if(!$USER['SETTING']['ELEMENT_MOVE']){
	Redirect();
}

for($i=0; $i<count($USER['SETTING']['ELEMENT_MOVE']['ID']); $i++)
{
	$arElm = $DB->Row("SELECT ID, SECTION_BLOCK FROM `k2_block".$USER['SETTING']['ELEMENT_MOVE']['BLOCK']."` WHERE `ID` = '".$USER['SETTING']['ELEMENT_MOVE']['ID'][$i]."'");
	$LIB['BLOCK_ELEMENT']->Edit($arElm['ID'], $arElm['SECTION_BLOCK'], array(
		'SECTION' => $_SECTION,
		'SECTION_BLOCK' => $_SECTION_BLOCK,
		'CATEGORY' => $_CATEGORY
	));
}
$DB->Query("DELETE FROM `k2_user_setting` WHERE `USER` = '".$USER['ID']."' AND `ACTION` = 'ELEMENT_MOVE';");

Redirect($_SERVER['HTTP_REFERER']);
?>