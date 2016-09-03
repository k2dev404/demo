<?
#require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/cache/lang/'.LANG.'.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.cart.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.order.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.order-product.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.payer.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.payer-element.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.status.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.payment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.delivery.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.handler.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/shop/class.address.php');
#require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/cache/function.php');
$MOD['SHOP'] = new Shop();
$MOD['SHOP_CART'] = new ShopCart();
$MOD['SHOP_ORDER'] = new ShopOrder();
$MOD['SHOP_ORDER_PRODUCT'] = new ShopOrderProduct();
$MOD['SHOP_PAYER'] = new ShopPayer();
$MOD['SHOP_PAYER_ELEMENT'] = new ShopPayerElement();
$MOD['SHOP_STATUS'] = new ShopStatus();
$MOD['SHOP_PAYMENT'] = new ShopPayment();
$MOD['SHOP_DELIVERY'] = new ShopDelivery();
$MOD['SHOP_HANDLER'] = new ShopHandler();
$MOD['SHOP_ADDRESS'] = new ShopAddress();
?>