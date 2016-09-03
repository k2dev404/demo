<?
if($CURRENT['ELEMENT']){
	$arBack = $LIB['BLOCK_CATEGORY']->Back($CURRENT['BLOCK']['ID'], $CURRENT['CATEGORY']['ID']);
	foreach($arBack as $arCategory)
	{
		$LIB['NAV']->BackAdd(2, array($arCategory['NAME'], $arCategory['URL']));
	}

	$LIB['NAV']->BackAdd(2, array($CURRENT['ELEMENT']['NAME']));

	if($CURRENT['ELEMENT']['PHOTO']){
		$arPhoto = $LIB['PHOTO']->Preview($CURRENT['ELEMENT']['PHOTO'], array('WIDTH' => 324, 'HEIGHT' => 321, 'FIX' => 1));
		$CURRENT['ELEMENT']['PHOTO_PATH'] = $arPhoto['PATH'];

		if($CURRENT['ELEMENT']['PHOTOS']){
			$this->Preview[] = $CURRENT['ELEMENT']['PHOTO_PATH'];
			foreach(toArray($CURRENT['ELEMENT']['PHOTOS']) as $nPhoto)
			{
				if($i > 2){
					break;
				}
				$arPhoto = $LIB['PHOTO']->Preview($nPhoto, array('WIDTH' => 324, 'HEIGHT' => 321, 'FIX' => 1));
				$this->Preview[] = $arPhoto['PATH'];
				$i++;
			}

		}
	}

	$this->Prop = array();
	$arFields = $LIB['FIELD']->Rows('k2_block'.$CURRENT['BLOCK']['ID']);
	foreach($arFields as $arField)
	{
		if(substr($arField['FIELD'], 0, 1) == '_' && $CURRENT['ELEMENT'][$arField['FIELD']]){
			$this->Prop[] = array($arField['NAME'], $CURRENT['ELEMENT'][$arField['FIELD']]);
		}
	}

	$this->Element = $CURRENT['ELEMENT'];

	$this->Template('template-full.php');
	return false;
}

$this->Category = $LIB['BLOCK_CATEGORY']->Child(4, 0, array('ACTIVE' => 1));

$arFilter = array('ACTIVE' => 1);

$this->Back = array();

if($CURRENT['CATEGORY']){
	$arBack = $LIB['BLOCK_CATEGORY']->Back($CURRENT['BLOCK']['ID'], $CURRENT['CATEGORY']['ID']);
	foreach($arBack as $arCategory)
	{
		$this->Back[] = $arCategory['ID'];

		$LIB['NAV']->BackAdd(2, array($arCategory['NAME'], $arCategory['URL']));
	}

	$arAllCategoryID = array();
	$arChild = $LIB['BLOCK_CATEGORY']->Child($CURRENT['BLOCK']['ID'], $CURRENT['CATEGORY']['ID'], true, array('ACTIVE' => 1));
	foreach($arChild as $arCategory)
	{
		$arAllCategoryID[] = $arCategory['ID'];
	}

	if(!$arAllCategoryID){
		$arFilter['CATEGORY'] = $CURRENT['CATEGORY']['ID'];
	}

	if($arAllCategoryID){
		$arSQL[] = "`CATEGORY` IN(".implode(',', $arAllCategoryID).")";
	}
}

if($arSQL){
	$arFilter['+SQL'] = implode(' AND ', $arSQL);
}

$this->FilterField = array('PRICE', '_MATERIAL', '_DIAMETR', '_BRAND', '_COUNTRY');

$arFields = $LIB['FIELD']->Rows('k2_block'.$CURRENT['BLOCK']['ID']);

$arFilterData = $this->Filter = array();
$arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], $arFilter, array('SORT' => 'ASC'), array());
for ($i = 0, $n = count($arList); $i < $n; $i++) {
	foreach($arFields as $arField)
	{
		if($arList[$i][$arField['FIELD']] && in_array($arField['FIELD'], $this->FilterField)){
			$arFilterData[$arField['FIELD']][] = $arList[$i][$arField['FIELD']];
		}
	}
}

if($arFilterData['PRICE']){
	$this->Filter['PRICE']['ID'] = 17;
	$this->Filter['PRICE']['TITLE'] = 'Цена';
	$this->Filter['PRICE']['MIN'] = min($arFilterData['PRICE']);
	$this->Filter['PRICE']['MAX'] = max($arFilterData['PRICE']);

	if(isset($_GET['priceMin'])){
		$this->Filter['PRICE']['MIN_VALUE'] = (int)$_GET['priceMin'];
	}else{
		$this->Filter['PRICE']['MIN_VALUE'] = $this->Filter['PRICE']['MIN'];
	}
	if($_GET['priceMax']){
		$this->Filter['PRICE']['MAX_VALUE'] = (int)$_GET['priceMax'];
	}else{
		$this->Filter['PRICE']['MAX_VALUE'] = $this->Filter['PRICE']['MAX'];
	}

	if($this->Filter['PRICE']['MIN'] == $this->Filter['PRICE']['MAX']){
		unset($this->Filter['PRICE']);
	}
}

foreach($this->FilterField as $sField)
{
	if($arFilterData[$sField] && !in_array($sField, array('PRICE'))){
		foreach($arFields as $arField)
		{
			if($arField['FIELD'] == $sField){

				$arUniq = array_unique($arFilterData[$sField]);
				if(count($arUniq) > 1){
					$this->Filter[$sField] = array('ID' => $arField['ID'], 'TITLE' => $arField['NAME'], 'OPTION' => array_unique($arFilterData[$sField]));
				}

				break;
			}

		}
	}
}

if(preg_match("#f_#", $_SERVER['REQUEST_URI'])){
	$arElementID = array(-1);
	$arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], $arFilter, array('SORT' => 'ASC'), array());
	for ($i = 0, $n = count($arList); $i < $n; $i++) {
		foreach($this->Filter as $sField => $arField)
		{
			if($_GET['f_'.$arField['ID']] && is_array($_GET['f_'.$arField['ID']])){
				if(in_array(urlencode($arList[$i][$sField]), $_GET['f_'.$arField['ID']])){
					$arElementID[] = $arList[$i]['ID'];
				}
			}
		}
	}

	$arSQL[] = "`ID` IN(".implode(',', $arElementID).")";
	$arFilter['+SQL'] = implode(' AND ', $arSQL);
}

if(preg_match("#f_#", $_SERVER['REQUEST_URI'])){
	$arElementID = array(-1);
	$arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], $arFilter, array('SORT' => 'ASC'), array());
	for ($i = 0, $n = count($arList); $i < $n; $i++) {
		foreach($this->Filter as $sField => $arField)
		{
			if($_GET['f_'.$arField['ID']] && is_array($_GET['f_'.$arField['ID']])){
				if(in_array(urlencode($arList[$i][$sField]), $_GET['f_'.$arField['ID']])){
					$arElementID[] = $arList[$i]['ID'];
				}
			}
		}
	}

	$arSQL[] = "`ID` IN(".implode(',', $arElementID).")";
	$arFilter['+SQL'] = implode(' AND ', $arSQL);
}

if($_GET['priceMin']){
	$arFilter['>=PRICE'] = (int)$_GET['priceMin'];
	$arFilter['<=PRICE'] = (int)$_GET['priceMax'];
}

if($_GET['q']){
	if($arFilter['+SQL']){
		$arFilter['+SQL'] = " AND `NAME` LIKE '%".DBS($_GET['q'])."%'";
	}else{
		$arFilter['+SQL'] = " `NAME` LIKE '%".DBS($_GET['q'])."%'";
	}
}

$this->FilterHide = array();
if($_COOKIE['FILTER_HIDE']){
	$this->FilterHide = explode(',', $_COOKIE['FILTER_HIDE']);
}

$this->SortName = 'возрастанию цены';

$arSort = array('PRICE' => 'ASC');
$this->SortField = $arSortField = array(
		'price_asc' => array('NAME' => 'По возрастанию цены', 'SQL' => array('PRICE' => 'ASC'), 'TITLE' => 'возрастанию цены'),
		'price_desc' => array('NAME' => 'По убыванию цены', 'SQL' => array('PRICE' => 'DESC'), 'TITLE' => 'убыванию цены'),
		'name' => array('NAME' => 'По алфавиту', 'SQL' => array('NAME' => 'ASC'), 'TITLE' => 'алфавиту'),
		'date' => array('NAME' => 'По новизне', 'SQL' => array('DATE_CREATED' => 'DESC'), 'TITLE' => 'новизне'),
);

if($_SESSION['SORT']){
	$arSort = $arSortField[$_SESSION['SORT']]['SQL'];
	$this->SortName = $arSortField[$_SESSION['SORT']]['TITLE'];
}
if($_GET['sort'] && $arSortField[$_GET['sort']]){
	$arSort = $arSortField[$_GET['sort']]['SQL'];
	$_SESSION['SORT'] = $_GET['sort'];
	$this->SortName = $arSortField[$_SESSION['SORT']]['TITLE'];
}

$this->List = array();
$arList = $LIB['BLOCK_ELEMENT']->Rows($CURRENT['BLOCK']['ID'], $arFilter, $arSort, array(), 9);
for ($i = 0, $n = count($arList); $i < $n; $i++) {
	$arList[$i]['PHOTO_PATH'] = '/i/empty.gif';
	if($arList[$i]['PHOTO']){
		$arPhoto = $LIB['PHOTO']->Preview($arList[$i]['PHOTO'], array('WIDTH' => 286, 'HEIGHT' => 284, 'FIX' => 1));
		$arList[$i]['PHOTO_PATH'] = $arPhoto['PATH'];
	}
	$this->List[] = $arList[$i];
}

$this->Template();

?>