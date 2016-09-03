<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

$arSBlock = $LIB['SECTION_BLOCK']->ID($_SECTION_BLOCK);
for($i=0; $i<count($_POST['ID']); $i++)
{
	$DB->Query("UPDATE `k2_block".$arSBlock['BLOCK']."` SET `ACTIVE` = 1, `DATE_CHANGE` = NOW() WHERE `ID` = '".$_POST['ID'][$i]."';");
}

Redirect($_SERVER['HTTP_REFERER']);
?>