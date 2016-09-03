<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/k2/admin/inc/function.php');
permissionCheck('MODULE', $_REQUEST['module']);

if ($_POST['NAME'] && $_POST['FOLDER']) {
	$LIB['MODULE']->CopyTemplate($_REQUEST['module'], $_POST);
	Redirect('/k2/admin/module/' . strtolower($_REQUEST['module']) . '/template/?template=' . strtolower($_POST['FOLDER']));
}
?>
<form action="/k2/admin/module/template-copy.php?module=<?=$_REQUEST['module']?>" method="post" id="template-form"
      class="form">
	<div class="item">
		<div class="name">Название<span class="star">*</star></div>
		<div class="field"><input type="text" name="NAME" value="" id="transcription-from" required autofocus></div>
	</div>
	<div class="item">
		<div class="name">Папка<span class="star">*</star></div>
		<div class="field"><input type="text" name="FOLDER" value="" id="transcription-to" required></div>
	</div>
	<div style="height:30px;">
		<input type="submit" class="sub rightSub" value="Сохранить">
	</div>
</form>