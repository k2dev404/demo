<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="/k2/admin/css/style.css">
	<script type="text/javascript" src="/k2/admin/js/jquery.plugin.js"></script>
	<script type="text/javascript" src="/k2/admin/js/jquery.layer.js"></script>
	<script type="text/javascript" src="/k2/admin/tinymce/jquery.tinymce.min.js"></script>
	<script type="text/javascript" src="/k2/admin/tinymce/init.js"></script>
	<script type="text/javascript" src="/k2/admin/setting/update/check/"></script>
	<?
	if($SETTING['CODE_HIGHLIGHTER']){
		?>
		<link rel="stylesheet" type="text/css" href="/k2/admin/css/codemirror.css">
		<script type="text/javascript" src="/k2/admin/js/codemirror/codemirror.js"></script>
		<script type="text/javascript" src="/k2/admin/js/codemirror/matchbrackets.js"></script>
		<script type="text/javascript" src="/k2/admin/js/codemirror/htmlmixed.js"></script>
		<script type="text/javascript" src="/k2/admin/js/codemirror/xml.js"></script>
		<script type="text/javascript" src="/k2/admin/js/codemirror/javascript.js"></script>
		<script type="text/javascript" src="/k2/admin/js/codemirror/css.js"></script>
		<script type="text/javascript" src="/k2/admin/js/codemirror/clike.js"></script>
		<script type="text/javascript" src="/k2/admin/js/codemirror/php.js"></script>
		<script type="text/javascript" src="/k2/admin/js/codemirror/init.js"></script>
		<?
	}
	?>
	<script type="text/javascript">var section = <?=(int)$_SECTION?>;</script>
	<script type="text/javascript" src="/k2/admin/js/java.js"></script>
	<title>K2CMS: <?=$_SERVER['HTTP_HOST']?></title>
</head>
<body>
<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/setting/update/check.php');
?>
<table width="100%" class="h">
	<tr class="top under">
		<td class="l"></td>
		<td></td>
		<td class="r">Вы вошли как: <a href="/k2/admin/user/edit.php?id=<?=$USER['ID']?>"
		                               class="active"><?=$USER['LOGIN']?></a>(<?=$LIB['USER_GROUP']->Name($USER['USER_GROUP'])?>
			)<a href="/k2/admin/?logout=1" class="close" title="Выйти из системы"></a></td>
		<td>
			<div class="b1"></div>
		</td>
	</tr>
	<tr class="head">
		<td class="l"><a href="/k2/admin/" class="logo"></a>

			<div class="version">v<?=$SYSTEM['VERSION']?></div>
		</td>
		<td></td>
		<td class="r"><?
			$arMap = array('message' => 'СООБЩЕНИЯ', 'user' => 'ПОЛЬЗОВАТЕЛИ', 'dev' => 'РАЗРАБОТКА', 'module' => 'МОДУЛИ', 'setting' => 'НАСТРОЙКИ');
			foreach($arMap as $key => $value){
				?><a href="/k2/admin/<?=$key?>/"<?
				if(preg_match("#^/k2/admin/".$key."#", $_SERVER['REQUEST_URI'])){
					?> class="active"<?
				}
				?>><?=$value?></a><?
			}
			?></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="4" class="tabBox"><?
			$arSite = $LIB['SITE']->Rows();
			if(count($arSite) > 1){
				?><select name="domain" id="setDomain" style="margin:0 0 0 30px; max-width:214px;"><?
					for($i = 0; $i < count($arSite); $i++){
						?>
						<option value="<?=$arSite[$i]['ID']?>"<?
						if($arSite[$i]['ID'] == $USER['SETTING']['SITE_ACTIVE']){
							?> selected<?
						}
						?>><?=html($arSite[$i]['NAME'])?></option><?
					}
					?></select>
				<script>
					$(function () {
						$('#setDomain').change(function () {
							location.href = '/k2/admin/set-domain.php?id=' + $(this).val();
						});
					});
				</script><?
			}
			for($i = 0; $i < count($arSite); $i++){
				if($arSite[$i]['ID'] == $USER['SETTING']['SITE_ACTIVE']){
					$arActiveDomain = $arSite[$i];
					break;
				}
			}
			if(!$arActiveDomain){
				setSetting('SITE_ACTIVE', $arSite[0]['ID']);
			}
			?>
		</td>
	</tr>
	<tr class="b2">
		<td colspan="4"></td>
	</tr>
	<tr class="b3">
		<td valign="top">
			<div class="panel">
				<div class="l"><a href="http://<?=$arActiveDomain['DOMAIN']?>" class="icon home" target="_blank"
				                  title="Перейти на сайт"></a><a href="#" class="icon resize controlTree"
				                                                 title="Развернуть/свернуть карту разделов"></a></div>
				<div class="r"><a href="/k2/admin/section/add.php" class="button">Добавить раздел</a></div>
				<div class="clear"></div>
			</div><?
			$nTreeWidth = $_COOKIE['K2_TREE_WIDTH'] ? $_COOKIE['K2_TREE_WIDTH'] : 300;
			?>
			<div id="tree" style="width:<?=$nTreeWidth?>px">
				<div id="tree-box" style="width:<?=$nTreeWidth?>px; overflow:hidden;">
					<div id="tree-content" style="display:none"><?
						if(!($sMap = $MOD['CACHE']->GetVar(900, 'admin-tree'.$arActiveDomain['ID'].$DB->LastUpdate('k2_section')))){
							$sMap = _treeMap($USER['SETTING']['SITE_ACTIVE']);
							$MOD['CACHE']->SaveVar($sMap);
						}
						echo $sMap;
						?></div>
				</div>
			</div>
		</td>
		<td>
			<div id="slider"></div>
		</td>
		<td valign="top" class="b6">

