<?
require_once($_SERVER['DOCUMENT_ROOT'].'/config.php');

global $LIB, $MOD, $MOD_SETTING, $DB, $CURRENT, $USER, $SETTING;

$arLib = array('db', 'user', 'form', 'file', 'photo', 'site', 'section', 'design', 'nav', 'block', 'field', 'select', 'module', 'template', 'url', 'event', 'email', 'tool');
foreach($arLib as $sValue){
	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/'.$sValue.'/index.php');
}

if($arModule = $LIB['MODULE']->Rows()){
	for($i = 0; $i < count($arModule); $i++){
		if($arModule[$i]['ACTIVE']){
			$MOD_SETTING[$arModule[$i]['MODULE']] = unserialize($arModule[$i]['SETTING']);
			include_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/'.strtolower($arModule[$i]['MODULE']).'/index.php');
		}
	}
	unset($i, $arModule);
}

ob_start('bufferContent');

require_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/event.php');

$arSetting = $DB->Rows("SELECT * FROM `k2_setting`");
for($i = 0; $i < count($arSetting); $i++){
	$SETTING[$arSetting[$i]['TYPE']] = $arSetting[$i]['SETTING'];
}

if(strstr($_SERVER['REQUEST_URI'], '/k2/admin/')){
	session_start();
	define('ADMIN_MODE', true);
}

if($_GET['logout']){
	$LIB['USER']->Logout();
}

$USER = $LIB['USER']->Auth();
?>