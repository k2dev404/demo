<?
function includeTemplate($sFile)
{
	global $LIB, $MOD, $DB, $CURRENT, $USER;

	$sDirType = 'block';
	$sDirID = $CURRENT['BLOCK']['ID'];

	if ($CURRENT['FORM']['ID']) {
		$sDirType = 'form';
		$sDirID = $CURRENT['FORM']['ID'];
	}

	$sPath = $_SERVER['DOCUMENT_ROOT'].'/k2/dev/'.$sDirType.'/'.$sDirID.'/'.$sFile;

	if (file_exists($sPath)) {
		include($sPath);
	}
}

?>