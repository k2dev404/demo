<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

if(!$arHandler = $MOD['SHOP_HANDLER']->ID('bank')){
	Redirect('/k2/admin/module/shop/handler/');
}

tab(array(array('Модули', '/module/'), array('Интернет-магазин', '/module/shop/', 1)));
tab_(array(
	array('Заказы', '/module/shop/'),
	array('Плательщики', '/module/shop/payer/'),
	array('Адреса', '/module/shop/address/'),
	array('Статусы', '/module/shop/status/'),
	array('Оплата', '/module/shop/payment/'),
	array('Доставка', '/module/shop/delivery/'),
	array('Обработчики', '/module/shop/handler/', 1)
));

$arField[] = array('FIELD' => 'POL_NAME', 'NAME' => 'Наименование получателя платежа', 'REQUIRED' => 1);
$arField[] = array('FIELD' => 'INN', 'NAME' => 'ИНН получателя платежа', 'REQUIRED' => 1);
$arField[] = array('FIELD' => 'N_SCHET', 'NAME' => 'Номер счета получателя платежа', 'REQUIRED' => 1);
$arField[] = array('FIELD' => 'KPP', 'NAME' => 'КПП получателя платежа', 'REQUIRED' => 0);
$arField[] = array('FIELD' => 'BANK', 'NAME' => 'Наименование банка получателя платежа', 'REQUIRED' => 1);
$arField[] = array('FIELD' => 'BIK', 'NAME' => 'БИК', 'REQUIRED' => 1);
$arField[] = array('FIELD' => 'KOR_SCHET', 'NAME' => 'Кор./сч', 'REQUIRED' => 1);

if($_POST){
	for($i=0; $i<count($arField); $i++)
    {
	    if($arField[$i]['REQUIRED'] && !$_POST['DATA'][$arField[$i]['FIELD']]){
	    	$sError = changeMessage($arField[$i]['NAME']);
			break;
	    }
    }
    if(!$sError){
		if($nID = $MOD['SHOP_HANDLER']->Edit('bank', $_POST)){
	    	if($_POST['BAPPLY_x']){
				Redirect('?complite=1');
			}else{
				Redirect('/k2/admin/module/shop/handler/');
			}
		}
    }
}else{
	$_POST = $arHandler;
}
?><div class="content">
	<h1><?=$arHandler['NAME']?></h1>
    <form action="?" method="post" class="form">
    	<?formError($sError)?><?
		for($i=0; $i<count($arField); $i++)
	    {
		   	?><div class="item">
				<div class="name"><?=$arField[$i]['NAME']?><?
			    if($arField[$i]['REQUIRED']){
			    	?><span class="star">*</span><?
			    }
			    ?></div>
				<div class="field"><input type="text" name="DATA[<?=$arField[$i]['FIELD']?>]" value="<?=html($_POST['DATA'][$arField[$i]['FIELD']])?>"></div>
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