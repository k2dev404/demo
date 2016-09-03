<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SEO');
tab(array(array('Модули', '/module/'), array('SEO-инструменты', '/module/seo/', 1)));
tab_(array(
	array('Поисковое продвижение', '/module/seo/page/'),
	array('Перенаправление', '/module/seo/redirect/', 1),
	array('Генератор sitemap.xml', '/module/seo/sitemap/'),
	array('Настройки robot.txt', '/module/seo/robot/')
));

if(!$arRedirect = $MOD['SEO_REDIRECT']->ID($_ID)){
	Redirect('/k2/admin/module/seo/redirect/');
}
if($_POST){
	if($nID = $MOD['SEO_REDIRECT']->Edit($_ID, $_POST)){
    	if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/module/seo/redirect/');
		}
	}
}else{
	$_POST = $arRedirect;
}

?><div class="content">
	<h1>Редактирование</h1>
    <form action="edit.php?id=<?=$_ID?>" method="post" class="form">
    	<?formError($MOD['SEO_REDIRECT']->Error)?>
        <div class="item">
			<div class="name">Путь<span class="star">*</span></div>
			<div class="field"><input type="text" name="PATH" value="<?=html($_POST['PATH'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Перенаправить<span class="star">*</span></div>
			<div class="field"><input type="text" name="REDIRECT" value="<?=html($_POST['REDIRECT'])?>"></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/module/seo/redirect/">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>