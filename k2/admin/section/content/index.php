<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

$arSection = $LIB['SECTION']->ID($_SECTION);
if(!$arSection){
	Redirect('/k2/admin/section/');
}
$arSBlock = $LIB['SECTION_BLOCK']->Rows($_SECTION);
$arSBNow = array();
for($i=0; $i<count($arSBlock); $i++)
{
	if($arSBlock[$i]['ID'] == $_SECTION_BLOCK){
		$arSBNow = $arSBlock[$i];
		break;
	}
}
if($arSBlock && !$arSBNow){
	Redirect('/k2/admin/section/content/?section='.$_SECTION.'&section_block='.$arSBlock[0]['ID']);
}
tab(array(array('Раздел', '/section/edit.php?section='.$_SECTION), array('Наполнение', '/section/content/?section='.$_SECTION, 1)));
if($arSBNow){
	for($i=0; $i<count($arSBlock); $i++)
	{
		$arTab[] = array($arSBlock[$i]['NAME'], '/section/content/?section='.$_SECTION.'&amp;section_block='.$arSBlock[$i]['ID'], ($_SECTION_BLOCK == $arSBlock[$i]['ID']), $arSBlock[$i]['ACTIVE']);
	}
	$arBlock = $LIB['BLOCK']->ID($arSBNow['BLOCK'], 1);
	tab_($arTab);
}
?>
<div class="content">
	<h1><?=html($arSection['NAME'])?></h1>
    <?
    if($arSBNow){
	    if(!$arBlock['ELEMENT_FIELD'] && !$arBlock['CATEGORY_FIELD']){
		    ?><p>Функциональный раздел</p><?
	    }else{
		    define('INCLUDE_FILES', true);
			if($DB->Row("SELECT 1 FROM `k2_block".$arBlock['ID']."category` WHERE `SECTION_BLOCK` = '".$_SECTION_BLOCK."' AND `PARENT` = '".$_CATEGORY."' LIMIT 1") && $arBlock['CATEGORY']){
		    	Redirect('/k2/admin/section/content/category/?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&category='.$_CATEGORY);
			}else{
				Redirect('/k2/admin/section/content/element/?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&category='.$_CATEGORY);
			}
	    }
	}else{
		?><p>Пустой раздел, в который можно <a href="/k2/admin/section/block/add.php?section=<?=$_SECTION?>">добавить функционал</a></p><?
	}
    ?>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>