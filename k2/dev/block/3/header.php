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
        	<?include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/design/1/header.php')?>
            <div class="box">
            	<?$LIB['NAV']->Back(2)?>
                <h1><!-- $H1$ --></h1>
            	<div class="page">

                    <div class="leftPage">
                    	<div class="leftMenu"><?leftMenu()?></div>
                    </div>
                    <div class="rightPage">