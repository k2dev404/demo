<div class="catalogList">
	<div class="selOrder">
		<div class="sel order">
			<div class="title">Сортировать по <span><?=$this->SortName?></span></div>
			<div class="pop">
				<?
				foreach($this->SortField as $sKey => $arField)
				{
					$sURL = urlQuery(array('sort' => $sKey), array('page'));
					?>
					<a href="<?=$sURL?>"><?=$arField['NAME']?></a>
					<?
				}
				?>
			</div>
		</div>
	</div>
	<div class="filter">
		<form action="" method="get">
			<div class="filterMenu">
				<?
				foreach($this->Category as $arCategory)
				{
					if($arCategory['LEVEL'] && !in_array($arCategory['PARENT'], $this->Back)){
						continue;
					}
					?>
					<a href="<?=$arCategory['URL']?>" class="<?
					if($arCategory['LEVEL']){
						?>child<?
					}else{
						?>item<?
					}
					if(in_array($arCategory['ID'], $this->Back)){
						?> active<?
					}
					?>"><?=$arCategory['NAME']?></a>
					<?
				}
				?>
			</div>
			<div class="filterProp">
				<?
				foreach($this->Filter as $sKey => $arFilter)
				{
					?>
					<div class="item<?
					if(!in_array('filter'.$arFilter['ID'], $this->FilterHide)){
						?> active<?
					}
					?>" id="filter<?=$arFilter['ID']?>">
						<div class="title">
							<span><?=$arFilter['TITLE']?></span>
						</div>
						<div class="pop">
							<?
							if($sKey == 'PRICE'){
								?>
								<div class="filterPrice">
									<div class="line">
										<div class="lineBox"></div>
									</div>
									<div class="inputBox">
										<input name="priceMin" value="<?=$this->Filter['PRICE']['MIN_VALUE']?>" min="<?=$this->Filter['PRICE']['MIN']?>" readonly>
										&mdash;
										<input name="priceMax" value="<?=$this->Filter['PRICE']['MAX_VALUE']?>" max="<?=$this->Filter['PRICE']['MAX']?>" readonly>
										руб.
									</div>
								</div>
								<?
							}else{
								foreach($arFilter['OPTION'] as $sValue)
								{
									?>
									<label><input type="checkbox" name="f_<?=$arFilter['ID']?>[]" value="<?=urlencode($sValue)?>" <?
										if($_GET['f_'.$arFilter['ID']] && in_array(urlencode($sValue), $_GET['f_'.$arFilter['ID']])){
											?>checked<?
										}
										?>> <?=$sValue?></label>
									<?
								}
							}
							?>
						</div>
					</div>
					<?
				}
				?>
				<div class="filterSub">
					<a class="sub" href="#">Показать</a>
					<a class="clear" href="?">Сбросить</a>
				</div>
			</div>
		</form>
	</div>
	<div class="catalog">
		<div class="catalogBox">
			<?
			foreach($this->List as $arElm)
			{
				?>
				<div class="item">
					<a href="<?=$arElm['URL']?>"><img src="<?=$arElm['PHOTO_PATH']?>" width="286" height="284" alt=""></a>
					<div class="block">
						<div class="name"><?=$arElm['NAME']?></div>
						<form id="cartForm" method="post">
							<input type="hidden" name="ID" value="<?=$arElm['ID']?>">
							<a href="#" class="button inCart">купить</a>
						</form>
						<div class="price"><?=$arElm['PRICE']?> руб.</div>
					</div>
				</div>
				<?
			}
			?>
			<div class="item empty"></div>
		</div>
		<?$LIB['NAV']->Page(1)?>
	</div>
	<div class="clear"></div>
</div>