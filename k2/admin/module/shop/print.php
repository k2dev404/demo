<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');

permissionCheck('MODULE', 'SHOP');

if(!$arOrder = $MOD['SHOP_ORDER']->ID($_ID)){
	Redirect('/k2/admin/module/shop/');
}

$arPayment_ = $MOD['SHOP_PAYMENT']->Rows();
for($i=0; $i<count($arPayment_); $i++)
{
	$arPayment[$arPayment_[$i]['ID']] = $arPayment_[$i]['NAME'];
}
$arDelivery_ = $MOD['SHOP_DELIVERY']->Rows();
for($i=0; $i<count($arDelivery_); $i++)
{
	$arDelivery[$arDelivery_[$i]['ID']] = $arDelivery_[$i]['NAME'];
}
$arPayer_ = $MOD['SHOP_PAYER']->Rows();
for($i=0; $i<count($arPayer_); $i++)
{
	$arPayer[$arPayer_[$i]['ID']] = $arPayer_[$i]['NAME'];
}
$arUser_ = $LIB['USER']->Rows();
for($i=0; $i<count($arUser_); $i++)
{
	$arUser[$arUser_[$i]['ID']] = $arUser_[$i]['LOGIN'];
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Заказ №<?=$arOrder['ID']?> от <?=dateFormat($arOrder['DATE_CREATED'], 'd.m.Y')?></title>
	</head>
	<body>
<style type="text/css">
*{
	font-family:tahoma;
	font-size:12px;
}
.content{
	width:800px;
	margin:auto;
}
.fieldGroup{
	border:1px solid #e6e8ea;
	margin:25px 0 0 0;
	background:#666;
	padding:3px 0 3px 10px;
	font-size:14px;
	color:#fff;
}
.shopOrder{
	text-align:right;
	width:180px;
	white-space:nowrap;
}
.horizont{
	margin-top:15px;
}
.horizont td{
	padding:5px 10px 0 0;
}
table{
	border-collapse:collapse;
}
.table td, .table th{
	border:1px solid #000;
	padding:2px 5px 3px 5px;
}
</style>
<div class="content">
	<h2>Заказ №<?=$arOrder['ID']?> от <?=dateFormat($arOrder['DATE_CREATED'], 'd.m.Y')?></h2>
	<form action="edit.php?id=<?=$_ID?>" method="post" enctype="multipart/form-data" class="form">
		<?formError($MOD['SHOP_ORDER']->Error)?>
        <div class="fieldGroup">
        	<div class="title">Информация о заказе</div>
        </div>
		<table width="100%" class="horizont">
		    <tr>
		    	<td class="shopOrder">Способ оплаты:</td>
		    	<td><?=($arPayment[$arOrder['PAYMENT']]?$arPayment[$arOrder['PAYMENT']]:'-')?></td>
		    </tr>
		    <tr>
		    	<td class="shopOrder">Способ доставки:</td>
		    	<td><?=($arDelivery[$arOrder['DELIVERY']]?$arDelivery[$arOrder['DELIVERY']]:'-')?></td>
		    </tr><?
		    if($arOrder['DELIVERY_OPHEN']){
			    ?><tr>
			    	<td class="shopOrder">Транспортная компания:</td>
			    	<td><?=html($arOrder['DELIVERY_OPHEN'])?></td>
			    </tr><?
		    }
		    ?>
		    <tr>
		    	<td class="shopOrder">Плательщик:</td>
		    	<td><?=($arPayer[$arOrder['PAYER']]?$arPayer[$arOrder['PAYER']]:'-')?></td>
		    </tr>
		    <tr>
		    	<td class="shopOrder">Пользователь:</td>
		    	<td><?=($arUser[$arOrder['USER_CREATED']]?'<a href="/k2/admin/user/edit.php?id='.$arOrder['USER_CREATED'].'">'.$arUser[$arOrder['USER_CREATED']].'</a>':'-')?></td>
		    </tr>
	    </table>
	    <div class="fieldGroup">Плательщик</div>
	    <table width="100%" class="horizont"><?
	    $arField_ = $LIB['FIELD']->Rows('k2_mod_shop_payer'.$arOrder['PAYER']);
	    for($i=0; $i<count($arField_); $i++)
		{
			$arField[$arField_[$i]['FIELD']] = $arField_[$i]['NAME'];
		}
	    $arPayerElement = $MOD['SHOP_PAYER_ELEMENT']->Rows($arOrder['PAYER'], array('SHOP_ORDER' => $_ID));
	    for($i=0; $i<count($arPayerElement); $i++)
	    {
	   		foreach($arPayerElement[$i] as $sKey=>$sText)
	   		{
	   			if(!$arField[$sKey]){
	   				continue;
	   			}
	   			?><tr>
	   				<td class="shopOrder"><?=$arField[$sKey]?>:</td>
	    			<td><?=$sText?></td>
	    		</tr><?
	   		}
	    }
	    ?></table><?
	    if($arOrder['DELIVERY'] != 1 && ($arAddress = $MOD['SHOP_ADDRESS']->ID($arOrder['USER_CREATED']))){
		    ?><div class="fieldGroup">Адрес доставки</div>
		    <table width="100%" class="horizont"><?
		    $arField_ = $LIB['FIELD']->Rows('k2_mod_shop_address');
		    $arField = array();
		    for($i=0; $i<count($arField_); $i++)
			{
				?><tr>
		   			<td class="shopOrder"><?=$arField_[$i]['NAME']?>:</td>
		    		<td><?
		    		if($arAddress[$arField_[$i]['FIELD']]){
		    			echo $arAddress[$arField_[$i]['FIELD']];
		    		}else{
		    			?>-<?
		    		}
		    		?></td>
		    	</tr><?
			}
		    ?></table><?
	    }
	    ?>
        <div class="fieldGroup">Состав заказа</div>
     	<br>
        <table width="100%" class="table">
		    <tr>
		    	<th class="first">Название</th>
		    	<th>Свойства</th>
		    	<th>Количество</th>
		    	<th>Цена</th>
		    </tr><?
		    $arOrderProduct = $MOD['SHOP_ORDER_PRODUCT']->Rows(array('SHOP_ORDER' => $_ID));
		    for($i=0; $i<count($arOrderProduct); $i++)
		    {
		    	?><tr>
			    	<td><?=$arOrderProduct[$i]['NAME']?></td>
			    	<td><?
			    	if($arData = unserialize($arOrderProduct[$i]['DATA_ORDER'])){
			    		foreach($arData as $sKey => $sValue)
			    		{
			    			echo $sValue;
			    			?><br><?
			    		}
			    	}
			    	?></td>
			    	<td align="center"><?=$arOrderProduct[$i]['QUANTITY']?></td>
			    	<td align="right"><?=$arOrderProduct[$i]['PRICE']?></td>
			    </tr><?
		    }
		    ?><tr>
		    	<td colspan="3" align="right"><b>Доставка</b>:</td>
		    	<td align="right"><?
		    	if(($arDelivery = $MOD['SHOP_DELIVERY']->ID($arOrder['DELIVERY'])) && $arDelivery['PRICE'] != '0.00'){
		    		echo $arDelivery['PRICE'];
		    	}else{
		    		?>0<?
		    	}
		    	?></th>
		    </tr>
		    <tr>
		    	<td colspan="3" align="right"><b>Итого</b>:</td>
		    	<td align="right"><?=$arOrder['SUM']?></th>
		    </tr>
	    </table><?
	    if($sText = html($arOrder['COMMENT'])){
	    	?><div class="fieldGroup">Комментарий к заказу</div>
	    	<p style="padding-left:15px;"><?=$sText?></p><?
	    }
		?>
	</form>
</div>
<script type="text/javascript">window.print();</script>