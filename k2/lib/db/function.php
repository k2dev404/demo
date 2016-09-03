<?
function DBS($sText)
{
	global $DB;
	
	return mysqli_real_escape_string($DB->DB, $sText);
}

?>
