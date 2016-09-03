<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
tab(array(array('Настройки', '/setting/'), array('Списки', '/setting/select/'), array('Сайты', '/setting/site/'), array('Обновления', '/setting/update/'), array('Инструменты', '/setting/tool/', 1)));
tab_(array(array('Командная PHP-строка', '/setting/tool/', 1), array('Настройки PHP', '/setting/tool/phpinfo/')));
?><div class="content">
	<h1>Командная PHP-строка</h1>
    <form action="code.php" method="post" name="formCode" target="code" onselected="return false;" class="form">
    	<div class="item">
			<div class="name">PHP код<span class="star">*</span></div>
			<div class="field"><textarea name="CODE" cols="40" rows="10" data-code="true"><?=html($_POST['CODE'])?></textarea></div>
		</div>
		<div class="saveBlock">
			<p><input type="submit" class="sub" value="Отправить" onclick="window.open('', 'code', 'width=400, height=400, resizable=1'); document['forms']['formCode'].submit(); return false;"></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>