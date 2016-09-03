<?
if (!@$CURRENT) {
	exit;
}

$MOD['SEO']->End();

if ($GLOBALS['NAV']) {
	foreach ($GLOBALS['NAV'] as $nNav) {
		$DELAYED_VARIABLE['NAV' . $nNav] = $LIB['NAV']->BackResult($nNav);
	}
}
?>