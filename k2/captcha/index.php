<?php
error_reporting(0);
include('captcha.php');
session_start();
$captcha = new KCAPTCHA();
$_SESSION['CAPTCHA'] = $captcha->getKeyString();
?>