<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');

$arTable = array('block', 'component');

if($arGroup = $LIB[strtoupper($arTable[$_OBJECT]).'_GROUP']->ID($_ID)){
	$LIB[strtoupper($arTable[$_OBJECT]).'_GROUP']->Delete($_ID);
	Redirect('/k2/admin/dev/'.$arTable[$_OBJECT].'/');
}
Redirect('/k2/admin/');
?>