<?
function category()
{
	global $LIB;

	$arList = $LIB['BLOCK_CATEGORY']->Rows(4, array('ACTIVE' => 1), array('SORT' => 'ASC'));
	for ($i = 0, $n = count($arList); $i < $n; $i++) {
		$arAllElement[$arList[$i]['PARENT']][] = $arList[$i];
	}

	$arCategory = $LIB['BLOCK_CATEGORY']->Rows(4, array('ACTIVE' => 1, 'PARENT' => 0, '!=PHOTO' => ''), array('SORT' => 'ASC'));
	for ($i = 0, $n = count($arCategory); $i < $n; $i++) {
		$arPhoto = $LIB['FILE']->ID($arCategory[$i]['PHOTO']);

		?>
		<div class="item" style="background-image: url(<?=$arPhoto['PATH']?>)">
			<div class="itemBox">
				<a href="<?=$arCategory[$i]['URL']?>" class="more">подробнее</a>
				<div class="title"><?=$arCategory[$i]['NAME']?></div>
				<div class="link">
					<div class="linkBox linkBox<?=$arCategory[$i]['ID']?>">
						<?
						$j = 0;
						if($arAllElement[$arCategory[$i]['ID']]){
							foreach($arAllElement[$arCategory[$i]['ID']] as $arElm)
							{
								?>
								<a href="<?=$arElm['URL']?>"><?=$arElm['NAME']?></a>
								<?
								$j++;

								if($j == 7){
									?>
									</div>
									<div>
									<?
								}
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?
	}
}

function catalog()
{
	global $LIB;

	$arList = $LIB['BLOCK_ELEMENT']->Rows(4, array('ACTIVE' => 1), array('DATE_CREATED' => 'DESC'), array(), 8);
	for ($i = 0, $n = count($arList); $i < $n; $i++) {
		$arList[$i]['PHOTO_PATH'] = '/i/empty.gif';
		if($arList[$i]['PHOTO']){
			$arPhoto = $LIB['PHOTO']->Preview($arList[$i]['PHOTO'], array('WIDTH' => 286, 'HEIGHT' => 284, 'FIX' => 1));
			$arList[$i]['PHOTO_PATH'] = $arPhoto['PATH'];
		}

		?>
		<div class="item">
			<a href="<?=$arList[$i]['URL']?>"><img src="<?=$arList[$i]['PHOTO_PATH']?>" width="286" height="284" alt=""></a>
			<div class="block">
				<div class="name"><?=$arList[$i]['NAME']?></div>
				<form id="cartForm" method="post">
					<input type="hidden" name="ID" value="<?=$arList[$i]['ID']?>">
					<a href="#" class="button inCart">купить</a>
				</form>
				<div class="price"><?=$arList[$i]['PRICE']?> руб.</div>
			</div>
		</div>
		<?
	}
}

function city()
{
	global $CITY, $ALL_CITY;

	?>
	<div class="title">Ваш город <span><?=$CITY['NAME']?></span></div>
	<div class="pop">
		<?
		foreach($ALL_CITY as $arCity)
		{
			?>
			<a href="/?set_city=<?=$arCity['ID']?>"><?=$arCity['NAME']?></a>
			<?
		}
		?>
	</div>
	<?
}

function phone()
{
	global $CITY;

	foreach(explode("\r\n", $CITY['PHONE']) as $sPhone)
	{
		?>
		<span><?=$sPhone?></span>
		<?
	}
}

function slider()
{
	global $LIB;

	$arList = $LIB['BLOCK_ELEMENT']->Rows(6, array('ACTIVE' => 1), array('SORT' => 'ASC'));
	for ($i = 0, $n = count($arList); $i < $n; $i++) {
		$arFile = $LIB['FILE']->ID($arList[$i]['PHOTO']);

		?>
		<a href="<?=$arList[$i]['LINK']?>" class="item">
			<img src="<?=$arFile['PATH']?>" alt="">
		</a>
		<?
	}
}
?>