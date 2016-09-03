<?
$arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], array('SECTION_BLOCK' => $CURRENT['SECTION_BLOCK']['ID'], 'ACTIVE' => 1), array('ID' => 'ASC'), array('TEXT'));
for($i=0; $i<count($arList); $i++)
{
	echo $arList[$i]['TEXT'];
}
?>