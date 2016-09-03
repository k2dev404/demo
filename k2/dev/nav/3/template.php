<div class="menu">
	<ul class="reset"><?
		for ($i = 0; $i < count($arList); $i++) {
			if ($arList[$i]['ID'] == 1) {
				$arList[$i]['URL'] = '/';
			}
			?>
			<li<?
			if ($arList[$i]['CURRENT']) {
				?> class="active"<?
			}
			?>><a href="<?=$arList[$i]['URL']?>"><?=$arList[$i]['NAME']?></a></li> <?
		}
		?></ul>
</div>