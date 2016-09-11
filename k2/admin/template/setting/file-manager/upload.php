<?
if($this->Complete){
	?>
	<script>
		top.location.reload();
	</script>
	<?
	return false;
}

if($this->Error){
	?>
	<script>
		alert('Не удалось загрузить файл');
	</script>
	<?
}
?>
<div class="file-manager-upload">
	<form method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="upload">
		<div class="title">Выберите файл</div>
		<input name="FILE" type="file" class="file-manager-upload-input" multiple>
		<input class="sub file-manager-upload-sub" type="submit" value="Отправить">
	</form>
</div>