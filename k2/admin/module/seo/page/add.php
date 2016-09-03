<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SEO');
tab(array(array('Модули', '/module/'), array('SEO-инструменты', '/module/seo/', 1)));
tab_(array(
	array('Поисковое продвижение', '/module/seo/page/', 1),
	array('Перенаправление', '/module/seo/redirect/'),
	array('Генератор sitemap.xml', '/module/seo/sitemap/'),
	array('Настройки robot.txt', '/module/seo/robot/')
));

if($_POST){
	if($nID = $MOD['SEO_PAGE']->Add($_POST)){
    	if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/module/seo/');
		}
	}
}
?><div class="content">
	<h1>Добавление</h1>
    <form action="add.php" method="post" class="form">
    	<?formError($MOD['SEO_PAGE']->Error)?>
        <div class="item">
			<div class="name">Путь<span class="star">*</span></div>
			<div class="field"><input type="text" name="PAGE" value="<?=html($_POST['PAGE'])?>"><?fieldNote('Если необходимо включить подразделы добавьте символ * в конце пути')?></div>
		</div>
		<div class="item">
			<div class="name">Заголовок окна браузера</div>
			<div class="field"><input type="text" name="TITLE" value="<?=html($_POST['TITLE'])?>"><?fieldNote('Тег &lt;TITLE&gt;')?></div>
		</div>
		<div class="item">
			<div class="name">Заголовок страницы</div>
			<div class="field"><input type="text" name="H1" value="<?=html($_POST['H1'])?>"><?fieldNote('Тег &lt;H1&gt;')?></div>
		</div>
		<div class="item">
			<div class="name">Ключевые слова</div>
			<div class="field"><input type="text" name="KEYWORD" value="<?=html($_POST['KEYWORD'])?>"><?fieldNote('Тег &lt;KEYWORD&gt;')?></div>
		</div>
		<div class="item">
			<div class="name">Описание страницы</div>
			<div class="field"><textarea name="DESCRIPTION" cols="40" rows="2"><?=html($_POST['DESCRIPTION'])?></textarea><?fieldNote('Тег &lt;DESCRIPTION&gt;')?></div>
		</div>
		<div class="item">
			<div class="name">Текст после контента</div>
			<div class="field"><textarea name="TEXT" cols="40" rows="6"><?=html($_POST['TEXT'])?></textarea></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/module/seo/page/">отменить</a></p>
		</div>
		<br>
		<div class="warning">
			<b>Замена текста по маске:</b><br>
			Для замены строки в текущем тексте используйте маску %MASK%<br><br>
			<b>Отложенные переменные:</b><br>
			&lt;!-- $TITLE$ --&gt; - Заголовок окна браузера<br>
			&lt;!-- $H1$ --&gt; - Заголовок страницы<br>
			&lt;!-- $KEYWORD$ --&gt; - Ключевые слова<br>
			&lt;!-- $DESCRIPTION$ --&gt; - Описание страницы<br>
			&lt;!-- $TEXT$ --&gt; - Текст после контента<br>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>