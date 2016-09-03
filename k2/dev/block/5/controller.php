<?
if($_GET['q']){
	$arList = $LIB['BLOCK_CATEGORY']->Rows(4, array('ACTIVE' => 1, '+SQL' => "`NAME` LIKE '%".DBS($_GET['q'])."%'"), array('SORT' => 'ASC'));
	if($arList){
		Redirect($arList[0]['URL']);
	}

	$arList = $LIB['BLOCK_ELEMENT']->Rows(4, array('ACTIVE' => 1, '+SQL' => "`NAME` LIKE '%".DBS($_GET['q'])."%'"), array('SORT' => 'ASC'));
	if($arList){
		Redirect('/catalog/?q='.$_GET['q']);
	}
}

?><p>По вашему запросу ничего не найдено</p>