<div class="catalogFull">
	<div class="left">
		<?
		if($CURRENT['ELEMENT']['PHOTO_PATH']){
			?>
			<div class="leftBlock">
				<div class="photo">
					<a id="photo" style="background: #fff url(<?=$CURRENT['ELEMENT']['PHOTO_PATH']?>) center no-repeat"></a>
				</div>
				<?
				if($this->Preview){
					?>
					<div class="preview">
						<?
						$bFirst = true;
						foreach($this->Preview as $sPhoto)
						{
							?>
							<a href="<?=$sPhoto?>"<?
							if($bFirst){
								?> class="active"<?
							}
							?>><img src="<?=$sPhoto?>" width="79" height="79" alt=""></a>
							<?
							$bFirst = false;
						}
						?>
						<div class="clear"></div>
					</div>
					<?
				}
				?>
			</div>
			<?
		}
		?>
		<div class="rightBlock rightBlock<?=(int)$CURRENT['ELEMENT']['PHOTO']?>">
			<h1><?=$this->Element['NAME']?></h1>
			<div class="prop">
				<table>
					<tbody>
					<?
					foreach($this->Prop as $arProp)
					{
						?>
						<tr>
							<td><?=$arProp[0]?></td>
							<td width="40%"><?=$arProp[1]?></td>
						</tr>
						<?
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="clear"></div>
		<div class="text">
			<?
			if($this->Element['TEXT_FULL']){
				?>
				<div class="title">Подробное описание</div>
				<?=$this->Element['TEXT_FULL']?>
				<?
			}
			if($this->Element['TEXT_PRIM']){
				?>
				<div class="title">Применение</div>
				<?=$this->Element['TEXT_PRIM']?>
				<?
			}
			?>
		</div>
	</div>
	<div class="right">
		<div class="catalogRight">
			<div class="price"><?=$this->Element['PRICE']?> руб.</div>
			<div class="info"></div>
			<form id="cartForm" method="post">
				<input type="hidden" name="ID" value="<?=$this->Element['ID']?>">
				<div class="count">
					<span>Выбрать кол-во</span>
					<div class="fullQuantity">
						<div class="n down"><i></i></div>
						<input type="text" name="QUANTITY" readonly="" value="1">
						<div class="n up active"><i></i></div>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="subBox">
					<input type="submit" class="inCart" value="Добавить в корзину">
				</div>
			</form>
		</div>
	</div>
	<div class="clear"></div>
</div>