<?
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/block/lang/ru.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/block/class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/block/class.group.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/block/class.category.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/block/class.element.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/block/function.php');
$LIB['BLOCK'] = new Block();
$LIB['BLOCK_GROUP'] = new BlockGroup();
$LIB['BLOCK_ELEMENT'] = new BlockElement();
$LIB['BLOCK_CATEGORY'] = new BlockCategory();
?>