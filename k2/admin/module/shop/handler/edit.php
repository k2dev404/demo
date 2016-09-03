<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

if(!$arHandler = $MOD['SHOP_HANDLER']->ID($_ID)){
	Redirect('/k2/admin/module/shop/handler/');
}

tab(array(array('Модули', '/module/'), array('Интернет-магазин', '/module/shop/', 1)));
tab_(array(
	array('Заказы', '/module/shop/'),
	array('Плательщики', '/module/shop/payer/'),
	array('Статусы', '/module/shop/status/'),
	array('Оплата', '/module/shop/payment/'),
	array('Доставка', '/module/shop/delivery/'),
	array('Обработчики', '/module/shop/handler/', 1)
));

if($_POST){
	if($nID = $MOD['SHOP_HANDLER']->Edit($_ID, $_POST)){
    	if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/module/shop/handler/');
		}
	}
}else{
	$_POST = $arHandler;
}

?><div class="content">
	<h1>Редактирование</h1>
    <form action="edit.php?id=<?=$_ID?>" method="post" class="form">
    	<?formError($MOD['SHOP_HANDLER']->Error)?>
    	<div class="item"><label><input type="checkbox" name="ACTIVE" value="1"<?
	    if($_POST['ACTIVE']){
	    	?> checked<?
	    }
	    ?>>Активность</label></div>
        <div class="item">
			<div class="name">Название<span class="star">*</span></div>
			<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>"></div>
		</div><?
		for($i=0; $i<count($arHandler['FIELD']); $i++)
	    {
		   	?><div class="item">
				<div class="name"><?=$arHandler['FIELD'][$i]['NAME']?><?
			    if($arHandler['FIELD'][$i]['REQUIRED']){
			    	?><span class="star">*</span><?
			    }
			    ?></div>
				<div class="field"><input type="text" name="DATA[<?=$arHandler['FIELD'][$i]['FIELD']?>]" value="<?=html($_POST['DATA'][$arHandler['FIELD'][$i]['FIELD']])?>"></div>
			</div><?
	    }
        ?><div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/module/shop/handler/">отменить</a></p>
		</div>
    </form>
</div><?

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>