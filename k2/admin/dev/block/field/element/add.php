<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('DEV');

$K2->Menu('TAB');
$K2->Menu('TAB_SUB', array(array('Настройки', '/dev/block/edit.php?id='.$_ID), array('Поля категорий', '/dev/block/field/category/?id='.$_ID), array('Поля элементов', '/dev/block/field/element/?id='.$_ID, 1)));

if(!$arBlock = $LIB['BLOCK']->ID($_ID)){
	Redirect('/k2/admin/dev/block/');
}
if($_POST){
	if($nID = $LIB['FIELD']->Add('k2_block'.$_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/dev/block/field/element/?id='.$_ID);
		}
	}
}
?><div class="content">
	<h1>Добавление</h1>
    <form action="add.php?id=<?=$_ID?>" method="post" class="form">
    	<?formError($LIB['FIELD']->Error)?>
		<?
		if(!in_array($_REQUEST['TYPE'], array('INPUT', 'TEXTAREA', 'CHECKBOX', 'SELECT', 'FILE', 'REFERENCE', 'HIDDEN'))){
			$_REQUEST['TYPE'] = 'INPUT';
		}
		include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/dev/field/type/add.php');
		include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/dev/field/type/'.strtolower($_REQUEST['TYPE']).'/add.php');
		?>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/dev/block/field/element/?id=<?=$_ID?>">отменить</a></p>
		</div>
    </form>
    <script type="text/javascript">
	$(function(){
		$('#type-field').change(function(){
			location.href = 'add.php?id=<?=$_ID?>&TYPE='+$(this).val();
			return false;
		});
	});
	</script>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>