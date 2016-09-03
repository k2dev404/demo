<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

if(!$arPayer = $MOD['SHOP_PAYER']->ID($_ID)){
	Redirect('/k2/admin/module/shop/payer/');
}

tab(array(array('Модули', '/module/'), array('Интернет-магазин', '/module/shop/', 1)));
tab_(array(
	array('Заказы', '/module/shop/'),
	array('Плательщики', '/module/shop/payer/', 1),
	array('Адреса', '/module/shop/address/'),
	array('Статусы', '/module/shop/status/'),
	array('Оплата', '/module/shop/payment/'),
	array('Доставка', '/module/shop/delivery/'),
	array('Обработчики', '/module/shop/handler/')
));
tab_(array(array('Настройки', '/module/shop/payer/edit.php?id='.$_ID), array('Поля', '/module/shop/payer/field/?id='.$_ID, 1)), false, 'subMenu subMenu_');

if($_POST){
	if($nID = $LIB['FIELD']->Add('k2_mod_shop_payer'.$_ID, $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/module/shop/payer/field/?id='.$_ID);
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
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/module/shop/payer/field/?id=<?=$_ID?>">отменить</a></p>
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