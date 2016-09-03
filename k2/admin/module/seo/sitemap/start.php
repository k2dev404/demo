<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
include_once('header.php');

permissionCheck('MODULE', 'SEO');

$arSetting = $MOD['SEO']->Setting;

$sResult = $MOD['SEO_SITEMAP']->Start($arSetting['SITEMAP_DOMAIN'], $arSetting);

$arRow = $DB->Row("SELECT COUNT(*) AS `TOTAL` FROM `k2_mod_seo_sitemap` WHERE `COMPLITE` = 1");

if($sResult == 'next'){
	?>
	<script>
		setTimeout(function(){
			location.href = '?';
		}, <?=($arSetting['SITEMAP_DELAY'] * 1000)?>);
	</script>
	<p>Обработано ссылок <b><?=(int)$arRow['TOTAL']?></b></p>
	<?
}

if($sResult == 'complite'){
	?>
	<p>Файл <a href="<?=html($arSetting['SITEMAP_ADDRESS'])?>" target="_blank"><?=html($arSetting['SITEMAP_ADDRESS'])?></a> сгенерирован</p>
	<?
}

include_once('footer.php');
?>


