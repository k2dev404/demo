<div class="content">
	<h1>Файловый менеджер</h1>

	<div class="action-block">
		<div class="action-block-left">
			<div class="file-manager-nav-back">
				<?
				foreach($this->NavBack as $arItem){
					if(!$arItem['FIRST']){
						?>»<?
					}
					?>
					<a href="?dir=<?=urlencode($arItem['DIR'])?>"><?=$arItem['NAME']?></a>
					<?
				}
				?>
			</div>
		</div>
		<div class="action-block-right">
			<!--
			<div class="button-wrap button-list-wrap">
				<a class="button button-list">Создать</a>
				<div class="button-list-pop">
					<a href="#" class="button-list-pop-item">Создать папку</a>
					<a href="#" class="button-list-pop-item">Создать файл</a>
				</div>
			</div>
			-->
			<div class="button-wrap">
				<a class="button" id="bUpload" onclick="return $.layer({get:'upload.php?dir=<?=urlencode($this->Dir)?>', title:'Загрузить файл', w: 400, h: 120, 'iframe': true}, function(){})">Загрузить файл</a>
			</div>
			<script>
				//$('#bUpload').click();
			</script>
		</div>
		<div class="clear"></div>
	</div>

	<form method="post" id="form">
		<input type="hidden" name="session" value="<?=$USER['SESSION']?>">
		<table width="100%" class="table">
			<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" onclick="table.check.all(this, '.table-body')" title="Отметить поля">
				</th>
				<th class="first">Название</th>
				<th>Размер</th>
				<th>Дата изменения</th>
				<th>Права</th>
				<th>Действие</th>
			</tr>
			</thead>
			<tbody class="table-body">
			<?
			if($this->FILE['DIR']){
				foreach($this->FILE['DIR'] as $arFile){
					?>
					<tr>
						<td>
							<input type="checkbox" name="DIR[]" value="<?=urlencode($arFile['DIR'])?>">
						</td>
						<td align="left">
							<a href="?dir=<?=urlencode($arFile['DIR'])?>" class="fmIcon"><img src="/k2/admin/i/ext/<?=$arFile['ICON']?>" align=""><?=$arFile['NAME_FORMAT']?></a>
						</td>
						<td><?=$arFile['SIZE_FORMAT']?></td>
						<td align="center"><?=$arFile['TIME_FORMAT']?></td>
						<td align="center"><?=$arFile['CHMOD']?></td>
						<td align="center" class="action">
							<!--
							<div class="icon-wrap">
								<a title="Дополнительные действия" class="icon icon-more" href=""></a>
								<div class="action-pop">
									<a href="#">Получить ссылку</a>
									<a href="#">Копировать</a>
									<a href="#">Переместить</a>
									<a href="#">Переименовать</a>
									<a href="#">Архивировать</a>
								</div>
							</div>
							-->
							<a href="delete.php?dir=<?=urlencode($arFile['DIR'])?>&session=<?=$USER['SESSION']?>" class="icon delete" onclick="return $.prompt(this)" title="Удалить"></a>
							<!-- <a title="Редактировать" class="icon edit" href=""></a> -->
						</td>
					</tr>
					<?
				}
			}

			if($this->FILE['FILE']){
				foreach($this->FILE['FILE'] as $arFile){
					?>
					<tr>
						<td>
							<input type="checkbox" name="FILE[]" value="<?=urlencode($this->Dir.$arFile['NAME'])?>">
						</td>
						<td align="left">
							<a href="?dir=<?=urlencode($arFile['DIR'])?>" class="fmIcon"><img src="/k2/admin/i/ext/<?=$arFile['ICON']?>" align=""><?=$arFile['NAME_FORMAT']?></a>
						</td>
						<td><?=$arFile['SIZE_FORMAT']?></td>
						<td align="center"><?=$arFile['TIME_FORMAT']?></td>
						<td align="center"><?=$arFile['CHMOD']?></td>
						<td align="center" class="action">
							<!--
							<div class="icon-wrap">
								<a title="Дополнительные действия" class="icon icon-more" href=""></a>
								<div class="action-pop">
									<a href="#">Скачать</a>
									<a href="#">Получить ссылку</a>
									<a href="#">Копировать</a>
									<a href="#">Переместить</a>
									<a href="#">Переименовать</a>
									<a href="#">Архивировать</a>
								</div>
							</div>
							-->
							<a href="delete.php?dir=<?=urlencode($arFile['DIR'])?>&file=<?=urlencode($arFile['NAME'])?>&session=<?=$USER['SESSION']?>" title="Удалить" class="icon delete" onclick="return $.prompt(this)"></a>
							<!-- <a title="Редактировать" class="icon edit" href=""></a> -->
						</td>
					</tr>
					<?
				}
			}

			if(!$this->FILE){
				?>
				<tr class="noblick">
					<td class="fm-empty" colspan="6">Пустая папка</td>
				</tr>
				<?
			}
			?>

			</tbody>
		</table>
	</form>

	<table width="100%" class="select">
		<tbody>
		<tr>
			<td>С отмеченными<select id="action">
					<option value="0">Выбрать действие</option>
					<option value="delete">Удалить</option>
				</select>
				<script>
					$('#action').change(function () {
						val = $(this).val();
						if (!val) {
							return false;
						}
						data = $('#form').serialize();
						if (data.length) {
							if (val == 'delete') {
								$.prompt(this, {'href': 'delete.php', 'yes': 'return actionDelete(1)', 'no': 'return actionDelete(0)'});
							} else {
								$('#form').attr('action', val + '.php').submit();
							}
						}
					});
					$('#form input').change(function () {
						$('#action')[$('.table-body input:checkbox:checked').size() ? 'removeAttr' : 'attr']('disabled', 'disabled');
					});
				</script>
			</td>
		</tr>
		</tbody>
	</table>
</div>