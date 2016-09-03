<?
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/function.php');

header('Pragma: no-cache');

if($SETTING['DEBUG_PANEL'] && ($USER['USER_GROUP'] == 1)){
	Debug::Start();
}

$arParse = parse_url($_GET['PATH']);
$arUrl = $LIB['URL']->ID($arParse['path']);

$CURRENT['ACTION'] = 'section';
$CURRENT['STATUS'] = 200;

if($arUrl){
	$CURRENT['SITE'] = $LIB['SITE']->ID($arUrl['SITE']);
	$CURRENT['SECTION'] = $LIB['SECTION']->ID($arUrl['SECTION']);
	if($arUrl['SECTION_BLOCK']){
		$CURRENT['SECTION_BLOCK'] = $LIB['SECTION_BLOCK']->ID($arUrl['SECTION_BLOCK']);
		$CURRENT['BLOCK']['ID'] = $CURRENT['SECTION_BLOCK']['BLOCK'];
	}
	if($arUrl['CATEGORY']){
		$CURRENT['CATEGORY'] = $LIB['BLOCK_CATEGORY']->ID($arUrl['CATEGORY'], $CURRENT['BLOCK']['ID']);
		$CURRENT['ACTION'] = 'category';
	}

	if($arUrl['ELEMENT']){
		$CURRENT['ELEMENT'] = $LIB['BLOCK_ELEMENT']->ID($arUrl['ELEMENT'], $CURRENT['SECTION_BLOCK']['ID']);
		$CURRENT['ACTION'] = 'element';
	}
}else{
	if(preg_match("#(.*?)/([c]?)(\d+)/(\d+)(/)?$#i", $arParse['path'], $arMath)){
		$arPath = pathinfo($arMath[1]);
		$arPath['SECTION_BLOCK'] = $arMath[3];
		if($arMath[2]){
			$CURRENT['ACTION'] = 'category';
			$arPath['CATEGORY'] = $arMath[4];
		}else{
			$CURRENT['ACTION'] = 'element';
			$arPath['ELEMENT'] = $arMath[4];
		}
	}else{
		$arPath = pathinfo($_GET['PATH']);
	}

	if(!$arPath['extension']){
		$arPath['URL'] = $arPath['dirname'].'/'.$arPath['basename'].'/';
	}else{
		$arPath['URL'] = $arPath['dirname'].'/'.$arPath['filename'].'.'.$arPath['extension'];
	}
	$arPath['URL'] = str_replace(array("\\", "//"), array("", "/"), $arPath['URL']);

	$arSite = $LIB['SITE']->Rows();
	for($i = 0; $i < count($arSite); $i++){
		if($arSite[$i]['DOMAIN'] == $_SERVER['SERVER_NAME']){
			$CURRENT['SITE'] = $arSite[$i];
			break;
		}
		if($arSite[$i]['ALIAS']){
			$arExp = explode("\n", $arSite[$i]['ALIAS']);
			for($j = 0; $j < count($arExp); $j++){
				if(str_replace(array("\r", "\n"), array('', ''), $arExp[$j]) == $_SERVER['SERVER_NAME']){
					$CURRENT['SITE'] = $arSite[$i];
					$bFind = true;
					break;
				}
			}
			if($bFind){
				break;
			}
		}
	}

	if(!$CURRENT['SITE']){
		$CURRENT['SITE'] = $arSite[0];
	}

	if($arPath['URL'] == '/'){
		$CURRENT['SECTION'] = $LIB['SECTION']->ID($CURRENT['SITE']['SECTION_INDEX']);
	}elseif($arSection = $DB->Row("SELECT * FROM `k2_section` WHERE `SITE` = '".$CURRENT['SITE']['ID']."' AND `URL_ORIGINAL` = '".DBS($arPath['URL'])."'")){
		$arSection['URL'] = $arSection['URL_ORIGINAL'];
		$CURRENT['SECTION'] = $arSection;
		$CURRENT['SECTION']['URL'] = respectiveURL($arSection, 'section');

		if($CURRENT['ACTION'] == 'element'){
			if($arElm = $LIB['BLOCK_ELEMENT']->ID($arPath['ELEMENT'], $arPath['SECTION_BLOCK'])){
				$CURRENT['SECTION_BLOCK'] = $LIB['SECTION_BLOCK']->ID($arElm['SECTION_BLOCK']);
				$CURRENT['BLOCK']['ID'] = $CURRENT['SECTION_BLOCK']['BLOCK'];
				if($arElm['CATEGORY']){
					$CURRENT['CATEGORY'] = $LIB['BLOCK_CATEGORY']->ID($arElm['CATEGORY'], $CURRENT['BLOCK']['ID']);
				}
				$CURRENT['ELEMENT'] = $arElm;
			}else{
				$CURRENT['STATUS'] = 404;
			}
		}

		if($CURRENT['ACTION'] == 'category'){
			$arSBock = $LIB['SECTION_BLOCK']->ID($arPath['SECTION_BLOCK']);
			if($arCatagory = $LIB['BLOCK_CATEGORY']->ID($arPath['CATEGORY'], $arSBock['BLOCK'])){
				$CURRENT['SECTION_BLOCK'] = $arSBock;
				$CURRENT['BLOCK']['ID'] = $arSBock['BLOCK'];
				$CURRENT['CATEGORY'] = $arCatagory;
			}else{
				$CURRENT['STATUS'] = 404;
			}
		}
	}else{
		$CURRENT['STATUS'] = 404;
	}
}

if($CURRENT['STATUS'] == 200){
	header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
}else{
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
	$CURRENT['SECTION'] = $LIB['SECTION']->ID($CURRENT['SITE']['SECTION_NOT_FOUND']);
	$CURRENT['ACTION'] = 'section';
}

if($CURRENT['SECTION']['URL_REDIRECT'] && ($CURRENT['SECTION']['URL_REDIRECT'] != $CURRENT['SECTION']['URL'])){
	Redirect($CURRENT['SECTION']['URL_REDIRECT']);
}

$CURRENT['DESIGN'] = $LIB['DESIGN']->ID($CURRENT['SECTION']['DESIGN_SHOW']);

if(!$CURRENT['SITE']['ACTIVE'] && ($USER['USER_GROUP'] != 1)){
	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/site-off.php');
	exit;
}

include($_SERVER['DOCUMENT_ROOT'].'/k2/before.php');
include($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/before.php');

if($CURRENT['DESIGN']['ID']){
	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/design/'.$CURRENT['DESIGN']['ID'].'/header.php');
}
$nPermission = 1;
if($USER['USER_GROUP'] != 1){
	if($USER['PERMISSION']['SECTION'][$CURRENT['SECTION']['ID']]){
		$nPermission = $USER['PERMISSION']['SECTION'][$CURRENT['SECTION']['ID']];
	}elseif($CURRENT['SECTION']['PERMISSION']){
		$nPermission = $CURRENT['SECTION']['PERMISSION'];
	}elseif($USER['PERMISSION']['SITE'][$CURRENT['SITE']['ID']]){
		$nPermission = $USER['PERMISSION']['SITE'][$CURRENT['SITE']['ID']];
	}elseif($CURRENT['SITE']['PERMISSION']){
		$nPermission = $CURRENT['SITE']['PERMISSION'];
	}
}

if($nPermission != 4){
	if($CURRENT['ACTION'] == 'section' || $CURRENT['STATUS'] == 404){
		$arSBlock = $DB->Rows("SELECT * FROM `k2_section_block` WHERE `SECTION` = '".$CURRENT['SECTION']['ID']."' AND `ACTIVE` = 1 ORDER BY `SORT` ASC");
		if($arSBlock){
			foreach($arSBlock as $arCSBlock){
				$CURRENT['SECTION_BLOCK'] = $arCSBlock;
				$CURRENT['BLOCK']['ID'] = $arCSBlock['BLOCK'];

				$ob = new SandBox;
				$ob->Dir = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/block/'.$arCSBlock['BLOCK'].'/';
				$ob->Template('controller.php');
			}
		}
	}else{
		$ob = new SandBox;
		$ob->Dir = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/block/'.$CURRENT['BLOCK']['ID'].'/';
		$ob->Template('controller.php');
	}
}else{
	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/permission-denied.php');
}

if($CURRENT['DESIGN']['ID']){
	include_once($_SERVER['DOCUMENT_ROOT'].'/k2/dev/design/'.$CURRENT['DESIGN']['ID'].'/footer.php');
}

include($_SERVER['DOCUMENT_ROOT'].'/k2/after.php');
include($_SERVER['DOCUMENT_ROOT'].'/k2/dev/inc/after.php');

if($SETTING['DEBUG_PANEL'] && ($USER['USER_GROUP'] == 1)){
	Debug::End();
}
?>