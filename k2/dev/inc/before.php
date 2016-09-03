<?
global $CITY, $ALL_CITY;

if($_GET['ajax']){
	$CURRENT['DESIGN']['ID'] = 3;
}

$arList = $LIB['BLOCK_ELEMENT']->Rows(3, array('ACTIVE' => 1), array('SORT' => 'ASC'), array());
for($i=0; $i<count($arList); $i++)
{
	if(!$CITY){
		$CITY = $arList[$i];
	}
	$ALL_CITY[] = $arList[$i];
}

if($_GET['set_city']){
	setcookie('CITY', $_GET['set_city'], time() + 30758400, '/', '');
	Redirect('/');

}

if($_COOKIE['CITY']){
	for($i=0; $i<count($arList); $i++)
	{
		if($arList[$i]['ID'] == $_COOKIE['CITY']){
			$CITY = $arList[$i];
			break;
		}
	}
}
?>