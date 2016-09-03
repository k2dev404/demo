<div class="contactList">
	<div class="contactListBox">
		<div class="left">
			<?
			foreach($this->List as $arItem)
			{
				?>
				<div class="item<?
				if($arItem['ID'] == $this->City['ID']){
					?> active<?
				}
				?>">
					<?=$arItem['TEXT']?>
					<div class="more">
						<a href="#" data-id="<?=$arItem['ID']?>">Смотреть на карте</a>
					</div>
				</div>
				<?
			}
			?>
		</div>
		<div class="map">
			<div id="map">
				<?
				foreach($this->List as $arItem)
				{
					?>
					<div class="mapItem mapItem<?=$arItem['ID']?><?
					if($arItem['ID'] == $this->City['ID']){
						?> active<?
					}
					?>">
						<script type="text/javascript" charset="utf-8" src="<?=$arItem['MAP']?>"></script>
					</div>
					<?
				}
				?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>