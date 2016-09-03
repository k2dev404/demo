<?
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/seo/class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/seo/class.page.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/seo/class.redirect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/k2/module/seo/class.sitemap.php');
$MOD['SEO'] = new Seo();
$MOD['SEO_PAGE'] = new SeoPage();
$MOD['SEO_REDIRECT'] = new SeoRedirect();
$MOD['SEO_SITEMAP'] = new SeoSitemap();
?>