<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/header.php');
permissionCheck('USER');
tab(array(array('Пользователи', '/user/', 1), array('Группы', '/user/group/')));
if ($_POST) {
	if ($nID = $LIB['USER']->Add($_POST)) {
		if ($_POST['BAPPLY_x']) {
			Redirect('edit.php?id=' . $nID . '&complite=1');
		} else {
			Redirect('/k2/admin/user/');
		}
	}
} else {
	$_POST['ACTIVE'] = 1;
	$_POST['USER_GROUP'] = 2;
}
?>
	<div class="content">
	<h1>Добавление</h1>

	<form action="add.php" method="post" enctype="multipart/form-data" class="form">
		<? formError($LIB['USER']->Error) ?>
		<div class="item">
			<input type="hidden" name="ACTIVE" value="0">
			<label><input type="checkbox" name="ACTIVE" value="1"<?
				if ($_POST['ACTIVE']) {
					?> checked="checked"<?
				}
				?>>Активность</label>
		</div>
		<div class="item">
			<div class="name">Логин<span class="star">*</span></div>
			<div class="field"><input type="text" name="LOGIN" value="<?=html($_POST['LOGIN'])?>" autocomplete="off">
			</div>
		</div>
		<div class="item">
			<div class="name">Пароль<span class="star">*</span></div>
			<div class="field"><input type="password" name="PASSWORD" value="<?=html($_POST['PASSWORD'])?>"
			                          autocomplete="off"></div>
		</div>
		<div class="item">
			<div class="name">Повтор пароля<span class="star">*</span></div>
			<div class="field"><input type="password" name="PASSWORD_RETRY" value="<?=html($_POST['PASSWORD_RETRY'])?>"
			                          autocomplete="off"></div>
		</div>
		<div class="item">
			<div class="name">E-mail<span class="star">*</span></div>
			<div class="field"><input type="text" name="EMAIL" value="<?=html($_POST['EMAIL'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Группа<span class="star">*</span></div>
			<div class="field"><select name="USER_GROUP"><?
					$arGroup = $LIB['USER_GROUP']->Rows();
					for ($i = 0; $i < count($arGroup); $i++) {
						?>
						<option value="<?=$arGroup[$i]['ID']?>"<?
						if ($_POST['USER_GROUP'] == $arGroup[$i]['ID']) {
							?> selected="selected"<?
						}
						?>><?=$arGroup[$i]['ID']?>. <?=html($arGroup[$i]['NAME'])?></option><?
					}
					?></select></div>
		</div><?
		$arField = array_merge($LIB['FIELD']->Rows('k2_user'), $LIB['FIELD_SEPARATOR']->Rows('k2_user'));
		for ($i = 0; $i < count($arField); $i++) {
			if (!$i) {
				usort($arField, 'sortArray');
			}
			if (!$arField[$i]['FIELD']) {
				?>
				<div class="group"><?=$arField[$i]['NAME']?></div><?
			} else {
				echo $LIB['FORM']->Element($arField[$i]['ID'], '<div class="item"><div class="name">%NAME%</div><div class="field">%FIELD%</div></div>');
			}
		}
		?>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>

			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/user/">отменить</a></p>
		</div>
	</form>
	</div><?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/footer.php');
?>