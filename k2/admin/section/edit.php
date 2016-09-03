<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/header.php');
permissionCheck('SECTION');

if (!$arSection = $LIB['SECTION']->ID($_SECTION, 1)) {
	Redirect('/k2/admin/');
}
if ($_POST) {
	if ($nID = $LIB['SECTION']->Edit($_SECTION, $_POST, 1)) {
		if ($_POST['BAPPLY_x']) {
			Redirect('edit.php?section=' . $_SECTION . '&complite=1');
		} else {
			Redirect('/k2/admin/section/');
		}
	}
} else {
	$_POST = $arSection;
}
$arDesign = $LIB['DESIGN']->Rows();
$arBlock = $LIB['BLOCK']->Rows();

tab(array(array('Раздел', '/section/edit.php?section=' . $_SECTION, 1), array('Наполнение', '/section/content/?section=' . $_SECTION)));
tab_(array(array('Настройки', '/section/edit.php?section=' . $_SECTION, 1), array('Функционал', '/section/block/?section=' . $_SECTION), array('Права доступа', '/section/permission.php?section=' . $_SECTION)));

$arSite = $LIB['SITE']->ID($arSection['SITE']);
$nInheritDegign = $arSite['DESIGN'];
$arBack = $LIB['SECTION']->Back($arSection['ID']);
if ($arBack[0]['ID'] != $arSection['ID'] && $arBack[0]['DESIGN']) {
	$nInheritDegign = $arBack[0]['DESIGN'];
}
for ($i = 0; $i < count($arDesign); $i++) {
	if ($nInheritDegign == $arDesign[$i]['ID']) {
		$sInheritDegign = $arDesign[$i]['ID'] . '.' . html($arDesign[$i]['NAME']);
	}
}

?>
	<div class="content">
	<h1>Редактирование</h1>

	<form method="post" enctype="multipart/form-data" class="form">
		<? formError($LIB['SECTION']->Error) ?>
		<div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>" id="transcription-from">
			</div>
		</div>
		<div class="item">
			<div class="name">Папка<span class="star">*</span></div>
			<div class="field"><input type="text" name="FOLDER" value="<?=html($_POST['FOLDER'])?>"
			                          id="transcription-to"><a href="#" onclick="return k2.transcription.lock(this)"
			                                                   id="transcription-icon" class="icon <?
				if ($_COOKIE['K2_TRANSCRIPTION']){
				?>lock" title="Автоматический перевод отключен" <?
			                                                   }else{
			                                                   ?>unlock" title="Автоматический перевод включен"<?
				}
				?>></a></div>
		</div>
		<div class="item">
			<div class="name">Макет дизайна<span class="star">*</span></div>
			<div class="field"><select name="DESIGN">
					<option value="0">Наследовать (<?=$sInheritDegign;?>)</option><?
					for ($i = 0; $i < count($arDesign); $i++) {
						?>
						<option value="<?=$arDesign[$i]['ID']?>"<?
						if ($_POST['DESIGN'] == $arDesign[$i]['ID']) {
							?> selected<?
						}
						?>><?=$arDesign[$i]['ID']?>. <?=html($arDesign[$i]['NAME'])?></option><?
					}
					?></select></div>
		</div><?
		$arField = array_merge($LIB['FIELD']->Rows('k2_section'), $LIB['FIELD_SEPARATOR']->Rows('k2_section'));
		for ($i = 0; $i < count($arField); $i++) {
			if (!$i) {
				usort($arField, 'sortArray');
			}
			if (!$arField[$i]['FIELD']) {
				?>
				<div class="fieldGroup"><?=$arField[$i]['NAME']?></div><?
			} else {
				echo $LIB['FORM']->Element($arField[$i]['ID'], '<div class="item"><div class="name">%NAME%</div><div class="field">%FIELD%</div></div>');
			}
		}
		?>
		<div class="moreField">
			<a class="link"><?=($_COOKIE['K2_MORE_FIELD'] ? 'Скрыть дополнительные поля' : 'Показать дополнительные поля')?></a>

			<div class="moreFieldBox"<? if ($_COOKIE['K2_MORE_FIELD']) { ?> style="display:block"<? } ?>>
				<div class="item">
					<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
						if ($_POST['ACTIVE']) {
							?> checked<?
						}
						?>>Активность</label>
				</div>
				<div class="item">
					<div class="name">Альтернативная ссылка</div>
					<div class="field"><input type="text" name="URL_ALTERNATIVE"
					                          value="<?=html($_POST['URL_ALTERNATIVE'])?>"></div>
				</div>
				<div class="item">
					<div class="name">Перенаправить по ссылке</div>
					<div class="field"><input type="text" name="URL_REDIRECT" value="<?=html($_POST['URL_REDIRECT'])?>">
					</div>
				</div>

				<div class="item">
					<div class="name">Заголовок окна</div>
					<div class="field">
						<input type="text" name="SEO_TITLE" value="<?=html($_POST['SEO_TITLE'])?>">

						<div class="note">Тег &lt;TITLE&gt;</div>
					</div>
				</div>
				<div class="item">
					<div class="name">Ключевые слова</div>
					<div class="field">
						<input type="text" name="SEO_KEYWORD" value="<?=html($_POST['SEO_KEYWORD'])?>">

						<div class="note">Тег &lt;KEYWORD&gt;</div>
					</div>
				</div>
				<div class="item">
					<div class="name">Описание страницы</div>
					<div class="field">
						<textarea name="SEO_DESCRIPTION" cols="40"
						          rows="2"><?=html($_POST['SEO_DESCRIPTION'])?></textarea>

						<div class="note">Тег &lt;DESCRIPTION&gt;</div>
					</div>
				</div>
			</div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>

			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/">отменить</a></p>
		</div>
		<script type="text/javascript">
			$(function () {
				k2.transcription.init();
			});
		</script>
	</form>
	</div><?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/footer.php');
?>