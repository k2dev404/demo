<?
global $CITY;

$arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], array('ACTIVE' => 1), array('ID' => 'ASC'));
for($i=0; $i<count($arList); $i++)
{
	$arList[$i]['MAP'] = str_replace('height=500', 'height=100%', $arList[$i]['MAP']);
	$this->List[] = $arList[$i];
}

$this->City = $CITY;
$this->Template();
?>