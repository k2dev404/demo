<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<script src="http://yastatic.net/jquery/1.11.1/jquery.min.js"></script>
	<script src="/js/jquery-ui.js"></script>
	<script src="/js/jquery.cookie.js"></script>
	<script src="/js/jquery.cycle.all.js"></script>
	<script src="/js/java.js"></script>
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
	<div class="page">
		<div class="box">
			<?$LIB['NAV']->Back(2)?>
			<?
			if($CURRENT['SECTION']['ID'] == 16 && $CURRENT['ELEMENT']['ID']){

			}else{
				?><h1><!-- $H1$ --></h1><?
			}
			?>