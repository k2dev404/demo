<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
?>
	<div class="content">
		<h1>Панель управления</h1>

		<p>Слева отображена структура вашего сайта, она используется для навигации по сайту. При нажатии правой кнопкой
			мыши на название раздела отображается всплывающее меню с необходимыми операциями.</p>

		<p>Для переноса раздела, нажмите левой кнопкой мыши по названию раздела, и не отпуская, перенесите в нужный
			раздел.</p>

		<div class="mainTable">
			<table width="100%">
				<tr>
					<td>
						<div style="width:150px">
							<div class="icon message"></div>
							Сообщения
						</div>
					</td>
					<td class="section">
						<div><a href="/k2/admin/message/">Сообщения</a></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="line"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div>
							<div class="icon user"></div>
							Пользователи
						</div>
					</td>
					<td class="section">
						<div><a href="/k2/admin/user/">Пользователи</a></div>
						<div><a href="/k2/admin/user/group/">Группы</a></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="line"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="icon dev"></div>
						Разработка
					</td>
					<td class="section">
						<div><a href="/k2/admin/dev/block/">Функциональные блоки</a></div>
						<div><a href="/k2/admin/dev/form/">Формы</a></div>
						<div><a href="/k2/admin/dev/design/">Макеты дизайна</a></div>
						<div><a href="/k2/admin/dev/nav/">Шаблоны навигации</a></div>
						<div><a href="/k2/admin/dev/email/">Шаблоны писем</a></div>
						<div><a href="/k2/admin/dev/field/">Поля</a></div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="line"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="icon module"></div>
						Модули
					</td>
					<td class="section"><?
						$arModule = $LIB['MODULE']->Rows();
						for($i = 0; $i < count($arModule); $i++){
							$sName = strtolower($arModule[$i]['MODULE']);
							if(!$arModule[$i]['ACTIVE']){
								continue;
							}
							?>
							<div><a href="/k2/admin/module/<?=$sName?>/"><?=$arModule[$i]['NAME']?></a></div><?
						}
						?></td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="line"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="icon setting"></div>
						Настройки
					</td>
					<td class="section">
						<div><a href="/k2/admin/setting/">Настройки</a></div>
						<div><a href="/k2/admin/setting/select/">Списки</a></div>
						<div><a href="/k2/admin/setting/site/">Сайты</a></div>
						<div><a href="/k2/admin/setting/update/">Обновления</a></div>
					</td>
				</tr>
			</table>
		</div>
	</div>
<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>