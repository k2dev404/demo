<?

class MySQL
{
	var $DB;

	function MySQL()
	{
		return $this->Connect();
	}

	function Connect()
	{
		if (!$rConnect = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)) {
			if (!defined('DB_HOST')) {
				if ($_SERVER['REQUEST_URI'] != '/install/') {
					header('Location: /install/');
				}
			}
			exit('Not connect to MySQL');
		} else {
			if (!mysql_select_db(DB_BASE, $rConnect)) {
				exit('Not connect DB in MySQL');
			} else {
				return $this->DB = $rConnect;
			}
		}
	}

	function Query($sSQL)
	{
		global $SETTING;

		if ($SETTING['DEBUG_PANEL']) {
			$nTime = microtime(1);
			$rSQL = mysql_query($sSQL);
			$GLOBALS['DEBUG_SQL_TIME'] += round(microtime(1) - $nTime, 4);
			$GLOBALS['DEBUG_SQL']['QUERY'][] = $sSQL;
			$GLOBALS['DEBUG_SQL']['QUERY_TIME'][] = round(microtime(1) - $nTime, 4);
		} else {
			$rSQL = mysql_query($sSQL);
		}

		if (!mysql_error($this->DB)) {
			return $rSQL;
		} else {
			$this->Error = mysql_error($this->DB);

			return false;
		}
	}

	function Row($sSQL)
	{
		if (!$rResult = $this->Query($sSQL)) {
			return false;
		}

		return mysql_fetch_assoc($rResult);
	}

	function Rows($sSQL, $nSize = 0)
	{
		global $LIB;

		$arRows = array();

		if ($nSize) {

			$sSQL = str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS ', trim($sSQL));
			if (substr($sSQL, -1, 1) == ';') {
				$sSQL = substr($sSQL, 0, mb_strlen($sSQL, 'UTF-8') - 1);
			}

			$nPage = (int)$_GET['page'];
			$nStart = 0;
			if ($nPage > 1) {
				$nStart = $nPage * $nSize - $nSize;
			}
			$sSQL_ = $sSQL.' LIMIT '.$nStart.', '.$nSize;

			if (!$rResult = $this->Query($sSQL_)) {
				return false;
			}
			$nCount = mysql_num_rows($rResult);
			if (!$nCount) {
				$sSQL_ = $sSQL.' LIMIT 0, '.$nSize;
				$rResult = $this->Query($sSQL_);
				$nCount = mysql_num_rows($rResult);
			}

			for ($i = 0; $i < $nCount; $i++) {
				$arRows[] = mysql_fetch_assoc($rResult);
			}
			$arCount = $this->Row("SELECT FOUND_ROWS()");

			$LIB['NAV']->Setting = array();
			$LIB['NAV']->Setting['TOTAL'] = $arCount['FOUND_ROWS()'];
			$LIB['NAV']->Setting['SIZE'] = $nSize;

			return $arRows;
		}

		if (!$rResult = $this->Query($sSQL)) {
			return false;
		}
		for ($i = 0; $i < @mysql_num_rows($rResult); $i++) {
			$arRows[] = mysql_fetch_assoc($rResult);
		}

		return $arRows;
	}

	function Insert($sSQL)
	{
		if ($this->Query($sSQL)) {
			return mysql_insert_id($this->DB);
		}

		return false;
	}

	function CSQL($arPar)
	{

		$arOperation = array('<=>', '!=', '>=', '<=', '<>', '>', '<', '=');

		$sRet = 'SELECT SQL_CALC_FOUND_ROWS';

		if ($arPar['SELECT']) {
			$bFirst = true;
			foreach ($arPar['SELECT'] as $sValue) {
				if (!$bFirst) {
					$sRet .= ', ';
				}
				$sVal = DBS($sValue);
				if ($sVal == '*') {
					$sRet .= ' *';
				} else {
					$sRet .= ' `'.DBS($sValue).'`';
				}
				$bFirst = false;
			}
		} else {
			$sRet .= ' *';
		}

		$sRet .= ' FROM `'.$arPar['FROM'].'`';

		if ($arPar['WHERE']) {
			$bFirst = true;
			$sRet .= ' WHERE';
			foreach ($arPar['WHERE'] as $sKey => $sValue) {
				if (!$bFirst) {
					$sRet .= ' AND';
				}

				if ($sKey == '+SQL') {
					$sRet .= ' '.$sValue;
					continue;
				}

				$bFindOperation = false;
				foreach($arOperation as $sOper)
				{
					if(strpos($sKey, $sOper) !== false){
						$sKey = str_replace($sOper, '', $sKey);
						$sRet .= ' `'.DBS($sKey).'` '.$sOper.' \''.DBS($sValue).'\'';
						$bFindOperation = true;
					}
				}

				if(!$bFindOperation){
					$sRet .= ' `'.DBS($sKey).'` = \''.DBS($sValue).'\'';
				}

				$bFirst = false;
			}
		}

		if ($arPar['ORDER_BY']) {
			$bFirst = true;
			$sRet .= ' ORDER BY ';
			foreach ($arPar['ORDER_BY'] as $sKey => $sValue) {
				if ($sValue == 'RAND') {
					$sRet .= ' RAND()';
				} else {
					if (!$bFirst) {
						$sRet .= ' ,';
					}
					$sRet .= ' `'.DBS($sKey).'` '.DBS($sValue);
				}
				$bFirst = false;
			}
		}

		if ($arPar['LIMIT']) {
			$sRet .= ' LIMIT '.(int)$arPar['LIMIT'];
		} elseif ($arPar['SIZE']) {
			$nPage = (int)$_GET['page'];
			$sRet .= ' LIMIT ';
			$nStart = 0;
			$arPar['SIZE'] = (int)$arPar['SIZE'];
			if ($nPage > 1) {
				$nStart = $nPage * $arPar['SIZE'] - $arPar['SIZE'];
			}
			$sRet .= $nStart.', '.$arPar['SIZE'];
		}

		return $sRet;
	}

	function LastUpdate($arTable = array())
	{
		global $DB;

		if (!is_array($arTable)) {
			$arTable = array($arTable);
		}
		$sTime = '';
		for ($i = 0; $i < count($arTable); $i++) {
			if ($i) {
				$sTime .= ', ';
			}

			if ($arRow = $DB->Row("CHECKSUM TABLE ".$arTable[$i]."")) {
				$sTime .= $arRow['Checksum'];
			}
		}

		return $sTime;
	}

	function Dump($sFile)
	{
		if ($sQuery = file_get_contents($sFile)) {
			$sQuery = str_replace("\r", '', $sQuery);
			$arQuery = explode("\n", $sQuery);
			$i = 0;
			$this->Error = false;
			while ($i < count($arQuery)) {
				$sQuery = trim($arQuery[$i]);
				if (mb_strlen($sQuery)) {
					while (mb_substr($sQuery, mb_strlen($sQuery) - 1, 1) <> ';' && mb_substr($sQuery, 0, 1) <> '#' && mb_substr($sQuery, 0, 2) <> '--' && $i + 1 < count($arQuery)) {
						$i++;
						$sQuery .= "\n".$arQuery[$i];
					}
					$sQuery = trim($sQuery);
					if (mb_substr($sQuery, 0, 1) <> '#' && mb_substr($sQuery, 0, 2) <> '--' && $sQuery != '') {
						@mysql_query($sQuery, $this->DB);
						if (mysql_error($this->DB)) {
							$this->Error = mysql_error($this->DB);
						}
					}
				}
				$i++;
			}
			if ($this->Error) {
				return false;
			}
		}

		return true;
	}
}

class DB extends MySQL
{

}

?>