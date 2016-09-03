<div class="navBack"><a href="/">Главная</a><?
	for($i=0, $c = count($arList); $i < $c; $i++)
	{
		if($arList[$i]['ID'] == 1){
			continue;
		}

		?> &gt; <?

		if($i + 2 > $c){
			echo $arList[$i]['NAME'];
		}else{
			?><a href="<?=$arList[$i]['URL']?>"><?=$arList[$i]['NAME']?></a><?
		}
	}
?></div>