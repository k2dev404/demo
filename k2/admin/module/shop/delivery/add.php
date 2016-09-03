<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

tab(array(array('Модули', '/module/'), array('Интернет-магазин', '/module/shop/', 1)));
tab_(array(
	array('Заказы', '/module/shop/'),
	array('Плательщики', '/module/shop/payer/'),
	array('Адреса', '/module/shop/address/'),
	array('Статусы', '/module/shop/status/'),
	array('Оплата', '/module/shop/payment/'),
	array('Доставка', '/module/shop/delivery/', 1),
	array('Обработчики', '/module/shop/handler/')
));
if($_POST){
	if($nID = $MOD['SHOP_DELIVERY']->Add($_POST)){
    	if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/module/shop/delivery/');
		}
	}
}else{
	$_POST['ACTIVE'] = 1;
}

?><div class="content">
	<h1>Добавление</h1>
    <form method="post" class="form">
    	<?formError($MOD['SHOP_DELIVERY']->Error)?>
    	<div class="item"><label><input type="checkbox" name="ACTIVE" value="1"<?
	    if($_POST['ACTIVE']){
	    	?> checked<?
	    }
	    ?>>Активность</label></div>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>" autofocus></div>
		</div>
		<div class="item">
			<div class="name">Цена</div>
			<div class="field"><input type="text" name="PRICE" value="<?=html($_POST['PRICE'])?>"></div>
		</div>
        <div class="item">
			<div class="name">Описание</div>
			<div class="field"><textarea name="TEXT" cols="40" rows="6"><?=html($_POST['TEXT'])?></textarea></div>
		</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/module/shop/delivery/">отменить</a></p>
		</div>
    </form>
</div><?

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>