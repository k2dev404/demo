<?
if (!@$CURRENT) {
	exit;
}

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
	$LIB['EVENT']->Execute('AJAX_REQUEST', $_REQUEST);
}

$MOD['SEO']->Start();

if ($GLOBALS['NAV']) {
	foreach ($GLOBALS['NAV'] as $nNav) {
		$DELAYED_VARIABLE['NAV' . $nNav] = $LIB['NAV']->BackResult($nNav);
	}
}
?>