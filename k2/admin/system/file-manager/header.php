<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');

permissionCheck('SECTION_CONTENT');

?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="/k2/admin/css/style.css">
        <script type="text/javascript" src="/k2/admin/js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/k2/admin/js/jquery-ui-1.8.17.custom.min.js"></script>
		<script type="text/javascript" src="/k2/admin/js/jquery.plugin.js"></script>
		<script type="text/javascript" src="/k2/admin/js/jquery.layer.js"></script>
		<script type="text/javascript" src="/k2/admin/js/jquery.MultiFile.pack.js"></script>
		<script type="text/javascript" src="/k2/admin/js/java.js"></script>
		<title>K2CMS</title>
		<style>
		html, body{
			background:#fff;
		};
		</style>
	</head>
<body>