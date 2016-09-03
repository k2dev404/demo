<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<script type="text/javascript" src="http://yastatic.net/jquery/1.11.1/jquery.min.js"></script>
	<script src="/js/jquery.cycle.all.js"></script>
	<script type="text/javascript" src="/js/java.js"></script>
	<meta name="keywords" content="<!-- $KEYWORD$ -->" />
	<meta name="description" content="<!-- $DESCRIPTION$ -->" />
	<title><!-- $TITLE$ --></title>
</head>
<body>
<div class="main">
	<div class="head">
		<div class="box">
			<a href="/" class="logo"></a>
			<div class="city">
				<div class="sel">
					<?city()?>
				</div>
			</div>
			<div class="phone">
				<?phone()?>
			</div>
			<div class="search">
				<div class="searchBox">
					<form action="/search/" method="get">
						<input type="image" src="/i/head/search.png">
						<input name="q" type="text">
					</form>
				</div>
			</div>
			<a href="/cart/" class="cart">
				<span class="count"><?=$MOD['SHOP_CART']->Quantity()?></span>
				<span class="title">Корзина</span>
			</a>
			<?$LIB['NAV']->Menu(3, array('ACTIVE' => 1))?>
		</div>
	</div>
	<div class="slider">
		<div class="box">
			<div class="nav"></div>
			<div class="sliderBox">
				<?slider()?>
			</div>
		</div>
	</div>
	<div class="box">
		<div class="category">
			<?category()?>
			<div class="clear"></div>
		</div>
	</div>
	<div class="catalog">
		<div class="box">
			<div class="b1">Новинки</div>
			<div class="catalogBox">
				<?catalog()?>
			</div>
		</div>
	</div>
	<div class="box">
		<div class="b2">
			<div class="b2Box">

