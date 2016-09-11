<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SETTING');
$K2->Menu('TAB');

$CONTROLLER = new K2;

$sCurrentDir = '/';

if($_GET['dir']){
	$sDir = urldecode($_GET['dir']);
	if(file_exists($_SERVER['DOCUMENT_ROOT'].$sDir)){
		$sCurrentDir = $sDir;
	}
}

$CONTROLLER->Dir = $sCurrentDir;

$sFullPath = $_SERVER['DOCUMENT_ROOT'].$sCurrentDir;

$arFiles = FDir::Scan($sFullPath);

if($arFiles){
	foreach($arFiles as $sFile){
		$sType = substr($sFile, -1, 1) == '/' ? 'DIR' : 'FILE';

		$arFile = [];
		$arFile['TIME'] = filemtime($sFullPath.$sFile);
		$arFile['TIME_FORMAT'] = date('d.m.Y, H:i', $arFile['TIME']);
		$arFile['NAME'] = str_replace('/', '', $sFile);
		$arFile['NAME_FORMAT'] = $arFile['NAME'];

		if(!preg_match('//u', $arFile['NAME_FORMAT'])){
			$arFile['NAME_FORMAT'] = iconv('CP1251', 'UTF-8', $arFile['NAME_FORMAT']);
		}

		$arFile['CHMOD'] = substr(sprintf('%o', fileperms($sFullPath.$sFile)), -3);

		if($sType == 'DIR'){
			$arFile['SIZE'] = 0;
			$arFile['SIZE_FORMAT'] = '-';
			$arFile['ICON'] = 'folder.png';
			$arFile['DIR'] = $sCurrentDir.$arFile['NAME'].'/';
		}else{
			$arFile['SIZE'] = filesize($sFullPath.$sFile);
			$arFile['SIZE_FORMAT'] = FFile::Size($sFullPath.$sFile);
			$arFile['ICON'] = FFile::Icon($arFile['NAME']);
			$arFile['DIR'] = $sCurrentDir;
		}

		$CONTROLLER->FILE[$sType][] = $arFile;
	}
}
$arExp = explode('/', $sCurrentDir);
$arExp[0] = '/';
$arFullDir = [];
for($i = 0, $c = count($arExp) - 1; $i < $c; $i++){
	$arItem = [];

	if($i){
		$arFullDir[] = $arExp[$i];
		$arItem['DIR'] = '/'.implode('/', $arFullDir).'/';
	}else{
		$arItem['DIR'] = '/';
	}

	$arItem['NAME'] = $arExp[$i];

	if(!preg_match('//u', $arItem['NAME'])){
		$arItem['NAME'] = iconv('CP1251', 'UTF-8', $arItem['NAME']);
	}

	if($arItem['NAME'] == '/'){
		$arItem['NAME'] = 'Корневая папка';
	}

	if(!$i){
		$arItem['FIRST'] = true;
	}

	if($c == $i + 1){
		$arItem['LAST'] = true;
	}

	$CONTROLLER->NavBack[] = $arItem;
}


$CONTROLLER->Template();

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>