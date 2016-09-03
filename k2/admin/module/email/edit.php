<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'EMAIL');
tab(array(array('Модули', '/module/'), array('Почта', '/module/email/', 1)));
if(!$arEmail = $MOD['EMAIL']->ID($_ID)){
	Redirect('/k2/admin/module/email/');
}
if($_POST){
	if($nID = $MOD['EMAIL']->Edit($_ID, $_POST)){
    	if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/module/email/');
		}
	}
}else{
	$_POST = $arEmail;
}
?><div class="content">
	<h1>Редактирование</h1>
    <form action="edit.php?id=<?=$_ID?>" method="post" class="form">
    	<?formError($MOD['EMAIL']->Error)?>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Тип<span class="star">*</span></div>
			<div class="field"><input type="text" name="TYPE" value="<?=html($_POST['TYPE'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Тема письма<span class="star">*</span></div>
			<div class="field"><input type="text" name="SUBJECT" value="<?=html($_POST['SUBJECT'])?>"></div>
		</div>
		<div class="item">
			<div class="name">E-mail отправителя<span class="star">*</span></div>
			<div class="field"><input type="text" name="FROM" value="<?=html($_POST['FROM'])?>"></div>
		</div>
		<div class="item">
			<div class="name">Шаблон письма</div>
			<div class="field"><textarea name="TEMPLATE" cols="40" rows="6"><?=html($_POST['TEMPLATE'])?></textarea></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/module/email/">отменить</a></p>
		</div>
		<br>
		<div class="warning">
		<b>Предопределенные мета-теги:</b><br>
		%SERVER_NAME% - Текущий домен<br>
		%SITE_NAME% - Название сайта<br>
		%FROM_EMAIL% - E-mail отправителя<br>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>