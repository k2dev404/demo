<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SEO');
tab(array(array('Модули', '/module/'), array('SEO-инструменты', '/module/seo/', 1)));
tab_(array(
	array('Поисковое продвижение', '/module/seo/page/'),
	array('Перенаправление', '/module/seo/redirect/'),
	array('Генератор sitemap.xml', '/module/seo/sitemap/', 1),
	array('Настройки robot.txt', '/module/seo/robot/')
));

$sError = '';

if ($_POST) {
	foreach ($_POST as $sKey => $sValue) {

		if($sKey == 'SITEMAP_ADDRESS'){
			$arParse = parse_url($sValue);
			if(!$arParse['host'] || !$arParse['path']){
				$sError = 'Укажите правильный адрес карты сайта';
				break;
			}else{
				$DB->Query("UPDATE `k2_mod_seo_setting` SET `SETTING` = '".DBS($sValue)."' WHERE TYPE = 'SITEMAP_ADDRESS'");
				$DB->Query("UPDATE `k2_mod_seo_setting` SET `SETTING` = '".DBS($arParse['scheme'].'://'.$arParse['host'])."' WHERE TYPE = 'SITEMAP_DOMAIN'");
				$DB->Query("UPDATE `k2_mod_seo_setting` SET `SETTING` = '".DBS($arParse['path'])."' WHERE TYPE = 'SITEMAP_FILENAME'");
				continue;
			}
		}

		$DB->Query("UPDATE `k2_mod_seo_setting` SET `SETTING` = '".DBS($sValue)."' WHERE TYPE = '".DBS($sKey)."'");
	}

	if($sError){
		$MOD['SEO']->Error = $sError;
	}else{
		if ($_POST['action'] == 'Сохранить') {
			Redirect('?complite=1');
		}

		if ($_POST['action'] == 'Сохранить и cгенерировать Sitemap') {
			Redirect('?action=create');
		}
	}

}else{
	$_POST = $MOD['SEO']->Setting;
	if(!$_POST['SITEMAP_ADDRESS']){
		$_POST['SITEMAP_ADDRESS'] = 'http://'.$_SERVER['SERVER_NAME'].'/sitemap.xml';
	}
}

?>
	<div class="content">
		<h1>Генератор sitemap.xml</h1>
		<? formError($MOD['SEO']->Error) ?>
		<form action="" method="post" class="form">
			<?
			if ($_GET['action'] == 'create') {
				$MOD['SEO_SITEMAP']->clear();
				Redirect('?action=start');
			} else if ($_GET['action'] == 'start') {
				?>
				<iframe src="start.php" width="100%" height="700" frameborder="no" scrolling="no"></iframe>
				<?
			} else {
				?>
				<div class="item">
					<div class="name">Адрес карты сайта</div>
					<div class="field"><input type="text" name="SITEMAP_ADDRESS" value="<?=html($_POST['SITEMAP_ADDRESS'])?>"></div>
				</div>
				<div class="item">
					<input type="hidden" name="SITEMAP_ROBOT" value="0">
					<label><input type="checkbox" name="SITEMAP_ROBOT" value="1"<?
						if ($_POST['SITEMAP_ROBOT']) {
							?> checked<?
						}
						?>>Учитывать правила robot.txt при генерировании sitemap</label>
				</div>
				<div class="item">
					<input type="hidden" name="SITEMAP_ROBOT_LINK" value="0">
					<label><input type="checkbox" name="SITEMAP_ROBOT_LINK" value="1"<?
						if ($_POST['SITEMAP_ROBOT_LINK']) {
							?> checked<?
						}
						?>>После генерации добавить ссылку в robot.txt</label>
				</div>
				<div class="item">
					<div class="name">Кол-во обрабатываемых ссылок за шаг</div>
					<div class="field"><input type="text" name="SITEMAP_MAXLINK" value="<?=(int)$_POST['SITEMAP_MAXLINK']?>"></div>
				</div>
				<div class="item">
					<div class="name">Задержка в секундах перед следующим шагом</div>
					<div class="field"><input type="text" name="SITEMAP_DELAY" value="<?=(int)$_POST['SITEMAP_DELAY']?>"></div>
				</div>
				<div class="item">
					<div class="name">Настройки приоритетов</div>
					<div class="field">
						<textarea name="SITEMAP_PRIORITY" cols="40" rows="3"><?=html($_POST['SITEMAP_PRIORITY'])?></textarea>
						<div class="note">Формат: ссылка[пробел]значение приоритета. Пример: http://www.example.com/catalog/ 0.8
						</div>
					</div>
				</div>
				<div class="saveBlock">
					<p>
						<input type="submit" class="sub" name="action" value="Сохранить">
						<input type="submit" class="sub" name="action" value="Сохранить и cгенерировать Sitemap">
					</p>
				</div>
				<?
			}
			?>

		</form>
	</div>
<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>