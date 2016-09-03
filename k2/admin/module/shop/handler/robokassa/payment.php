<?
if(!$arOrder){
	return false;
}

$arHandler = $MOD['SHOP_HANDLER']->ID('robokassa');
$sHex = md5($arHandler['DATA']['LOGIN'].':'.$arOrder['SUM'].':'.$arOrder['ID'].':'.$arHandler['DATA']['PASSWORD'].':Shp_item=2');

?><form action="https://merchant.roboxchange.com/Index.aspx" method="post">
	<input type="hidden" name="MrchLogin" value="<?=$arHandler['DATA']['LOGIN']?>">
	<input type="hidden" name="OutSum" value="<?=$arOrder['SUM']?>">
	<input type="hidden" name="InvId" value="<?=$arOrder['ID']?>">
	<input type="hidden" name="Desc" value="Оплата регистрации">
	<input type="hidden" name="SignatureValue" value="<?=$sHex?>">
	<input type="hidden" name="IncCurrLabel" value="PCR">
	<input type="hidden" name="Culture" value="ru">
	<input type="hidden" name="Shp_item" value="2">
	<input type="submit" class="sub" value="ОПЛАТИТЬ">
</form>