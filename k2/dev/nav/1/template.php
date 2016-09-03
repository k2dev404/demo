<div class="navPage"><?
for($i = 0; $i < $this->Setting['PAGES']; $i++)
{
	?> <a href="<?=$arList[$i]['URL']?>"<?
	if($arList[$i]['CURRENT']){
		?> class="active" <?
	}
	?>><?=($i+1)?></a> <?
}
?></div>