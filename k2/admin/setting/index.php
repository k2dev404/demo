<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SETTING');
$K2->Menu('TAB');

if ($_POST) {
	foreach ($_POST as $sKey => $sValue) {
		if ($sKey == 'AUTH_TIME' && $sValue < 1) {
			$sValue = $_POST['AUTH_TIME'] = 1;
		}
		if ($sValue) {
			if(in_array($sKey, array('EMAIL_FROM', 'EMAIL_TO')) && !filter_var($sValue, FILTER_VALIDATE_EMAIL)){
				$sError = 'Укажите верный E-mail';
			}
		}

		$DB->Query("UPDATE `k2_setting` SET `SETTING` = '".DBS($sValue)."' WHERE TYPE = '".DBS($sKey)."'");
	}
} else {
	$_POST = $SETTING;
}
?>
	<div class="content">
	<h1>Системные настройки</h1><?
	if($sError){
		?><div class="error"><?=$sError?></div><?
	}else if ($_GET['complite']) {
		?><div class="complite">Данные сохранены</div><?
	}
	?>
	<form action="?complite=1" method="post" enctype="multipart/form-data" class="form">
		<div class="fieldGroup">Почта</div>
		<div class="item">
			<div class="name">Отправитель</div>
			<div class="field"><input type="text" name="EMAIL_FROM" value="<?=html($_POST['EMAIL_FROM'])?>">
			</div>
		</div>
		<div class="item">
			<div class="name">Получатель</div>
			<div class="field"><input type="text" name="EMAIL_TO" value="<?=html($_POST['EMAIL_TO'])?>">
			</div>
		</div>
		<div class="fieldGroup">Авторизация</div>
		<div class="item">
			<div class="name">Время действия авторизации(в минутах)<span class="star">*</span></div>
			<div class="field"><input type="text" name="AUTH_TIME" value="<?=html($_POST['AUTH_TIME'])?>">
			</div>
		</div>
		<div class="item">
			<input type="hidden" name="AUTH_UNUQ_EMAIL" value="0"><label><input type="checkbox" name="AUTH_UNUQ_EMAIL" value="1"<?
				if ($_POST['AUTH_UNUQ_EMAIL']) {
					?> checked<?
				}
				?>>Регистрировать пользователей только с уникальными E-mail</label>
		</div>

		<div class="fieldGroup">Яндекс.карта</div>
		<div class="item">
			<div class="name">Координаты<span class="star">*</span></div>
			<div class="field"><input type="text" name="YANDEX_MAP_COORDS" value="<?=html($_POST['YANDEX_MAP_COORDS'])?>">
			</div>
		</div>
		<div class="item">
			<div class="name">Зум<span class="star">*</span></div>
			<div class="field"><input type="text" name="YANDEX_MAP_ZOOM" value="<?=html($_POST['YANDEX_MAP_ZOOM'])?>">
			</div>
		</div>
		<div class="fieldGroup">Разное</div>
		<div class="item">
			<input type="hidden" name="DEBUG_PANEL" value="0"><label><input type="checkbox" name="DEBUG_PANEL" value="1"<?
				if ($_POST['DEBUG_PANEL']) {
					?> checked<?
				}
				?>>Отображать на сайте панель отладки</label>
		</div>
		<div class="item">
			<input type="hidden" name="CODE_HIGHLIGHTER" value="0"><label><input type="checkbox" name="CODE_HIGHLIGHTER" value="1"<?
				if ($_POST['CODE_HIGHLIGHTER']) {
					?> checked<?
				}
				?>>Включить подсветку кода</label>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"></p>
		</div>
	</form>
	</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>