<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SEO');
tab(array(array('Модули', '/module/'), array('SEO-инструменты', '/module/seo/', 1)));
tab_(array(
	array('Поисковое продвижение', '/module/seo/page/'),
	array('Перенаправление', '/module/seo/redirect/'),
	array('Генератор sitemap.xml', '/module/seo/sitemap/'),
	array('Настройки robot.txt', '/module/seo/robot/', 1)
));

if($_POST){
	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt', $_POST['ROBOTS']);
}

?><div class="content">
	<h1>Настройки robot.txt</h1>
	<?formError($MOD['SEO']->Error)?>
	<form action="?complite=1" method="post" class="form">
		<div class="item">
			<div class="name">Содержимое</div>
			<div class="field"><textarea name="ROBOTS" cols="40" rows="6" data-code="true"><?=html(@file_get_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt'))?></textarea></div>
		</div>
		<div class="saveBlock">
			<p><input type="submit" class="sub" value="Сохранить"></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>