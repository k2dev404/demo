<?

class SandBox
{
	function Template($sPath = '')
	{
		global $LIB, $MOD, $MOD_SETTING, $DB, $CURRENT, $USER, $SETTING;

		if(!$sPath){
			$sPath = 'template.php';
		}

		include($this->Dir.$sPath);
	}
}

class Debug
{
	static function Start()
	{
		$GLOBALS['DEBUG']['START'] = microtime(1);
	}

	static function End()
	{
		?>
		<link rel="stylesheet" type="text/css" href="/k2/admin/css/debug.css">
		<div id="k2debug">
			<div>
				Страница сгенерирована за: <b><?=round(microtime(1) - $GLOBALS['DEBUG']['START'], 4)?></b> сек. Время
				генерации запросов: <b><?=$GLOBALS['DEBUG_SQL_TIME']?></b> сек. Использовано запросов:
				<b><?=count($GLOBALS['DEBUG_SQL']['QUERY'])?></b> шт. <a href="javascript:void(0)" onclick="document.getElementById('k2debug-sql').style.display = 'block'; this.style.display = 'none';">Показатьзапросы</a>
			</div>
			<div id="k2debug-sql">
				<table>
					<tr>
						<th>Запрос</th>
						<th>Время</th>
					</tr>
					<?
					for($i = 0; $i < count($GLOBALS['DEBUG_SQL']['QUERY']); $i++){
						?>
						<tr>
							<td align="left"><?=$GLOBALS['DEBUG_SQL']['QUERY'][$i]?></td>
							<td><?=$GLOBALS['DEBUG_SQL']['QUERY_TIME'][$i]?></td>
						</tr>
						<?
					}
					?>
				</table>
			</div>
		</div>
		<?
	}
}

?>