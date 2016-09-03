<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

$arExp = explode(',', $_POST['ROW']);
for($i=0; $i<count($arExp); $i++)
{
	if($arField = $LIB['SECTION_BLOCK']->ID($arExp[$i])){
		$arField['SORT'] = ($i+1)*10;
		$LIB['SECTION_BLOCK']->Edit($arExp[$i], $arField);
 	}
}
?>