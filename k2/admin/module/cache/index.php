<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'CACHE');

tab(array(array('Модули', '/module/'), array('Кеширование', '/module/cache/', 1)));

if($_GET['action'] == 'clear_all'){
	$MOD['CACHE']->ClearAll();
	if(defined('CACHE_MEMCACHE')){
		$MOD['CACHE_MEMCACHE']->Clear();
 	}
	Redirect('?complite=1');
}
if($_GET['action'] == 'clear'){
	$MOD['CACHE']->Clear();
	Redirect('?complite=1');
}

?><div class="content">
	<h1>Настройки</h1><?
    if($_GET['complite']){
		?><div class="complite">Кэш отчищен</div><?
	}
	$sSize = dirSize($_SERVER['DOCUMENT_ROOT'].'/k2/cache/');
 	?><p>Текущий размер кэша: <?=fileByte($sSize)?></p>
 	<p>
 		<a href="?action=clear" class="button">Удалить устаревший кэш</a>
 		<a href="?action=clear_all" class="button" style="margin-left:20px">Отчистить весь кэш</a>
 	</p>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>