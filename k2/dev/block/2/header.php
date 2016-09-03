<?php if($_SERVER['REQUEST_URI']=='/index.php') { header("HTTP/1.1 301 Moved Permanently"); header("Location: /"); exit(); } ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta property="og:image" content="http://<?=$_SERVER['SERVER_NAME']?>/i/logo-ss.png">
		<link rel="stylesheet" type="text/css" href="/jc/style.css">
		<link rel="stylesheet" type="text/css" href="/jc/jquery.fancybox.css">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="/jc/jquery.fancybox.pack.js"></script>
		<script type="text/javascript" src="/jc/java.js"></script>
		<? seo_DESCRIPTION(); ?>
		<? seo_KEYWORDS(); ?>
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
    	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<title><!-- $TITLE$ --><?=dopSEO()?></title>
	</head>
	<body>
    	<div class="main">
        	<div class="topLine"></div>
            <? include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/design/1/header.php'); ?>
            <div class="box">
            	<div class="block">
                	<div class="blockBox">
                    	<div class="item item1">
                        	<a href="<?=$URL[5]?>" class="title">Окна</a>
                            <div class="text link">
                            	<a href="<?=$URL[10]?>">Спектр</a>
                                <?/*
                                <a href="<?=$URL[13]?>">LG</a>
                                */?>
                                <a href="<?=$URL[14]?>">Goodwin</a>
                                <a href="<?=$URL[12]?>">Rehau</a>
                                <a href="<?=$URL[36]?>">Нестандартные окна</a>
                            </div>
                        </div>
                        <div class="item item2 black">
                        	<div class="title">Спектр нашей деятельности</div>
                            <div class="text">
Мы счастливы подарить вам комфорт и уют. А потому специально для вас мы производим металлопластиковые и алюминиевые конструкции, отвечающие всем современным требованиям и способные удовлетворить запросам самого взыскательного заказчика.
                            </div>
                        </div>
                        <div class="item item3">
                        	<a href="<?=$URL[6]?>" class="title">Двери</a>
                            <div class="text">
                            	Всевозможные виды: межкомнатные, балконные, входные пластиковые и алюминиевые двери

                            </div>
                        </div>
                        <div class="item item4">
                        	<a href="<?=$URL[7]?>" class="title">Остекление<span>Балконов и лоджий</span></a>
                            <div class="text">
                            Хотите, чтобы ваш балкон наконец стал максимально уютным и функциональным? Мы всегда рады помочь, обращатесь!
                            </div>
                        </div>
                        <div class="item item5">
                        	<a href="<?=$URL[8]?>" class="title">Алюминиевые<span>Конструкции</span></a>
                            <div class="text">
                            Алюминиевые двери и раздвижные конструкции для балконов и лоджий обладают повышенной прочностью, износостойкостью и эстетическими характеристиками.
                            </div>
                        </div>
                        <div class="item item6">
                        	<a href="<?=$URL[9]?>" class="title">Рольставни<span>и рольворота</span></a>
                            <div class="text">
                            На сегодняшний день это один из самых эффективных и элегантных способов защиты от взлома и проникновения, а также от посторонних взглядов.
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
