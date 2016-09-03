<?

class ShopCart
{
	function __construct()
	{
		session_start();
	}

	function ID($nID)
	{
		global $DB;

		if ($arCart = $DB->Row("SELECT * FROM `k2_mod_shop_cart` WHERE `ID` = '".(int)$nID."'")) {
			$arCart['DATA'] = unserialize($arCart['DATA']);
			$arCart['DATA_TMP'] = unserialize($arCart['DATA_TMP']);

			return $arCart;
		}
		$this->Error = 'Элемент не найден';

		return false;
	}

	function Rows($nUser = false, $arFilter = array(), $arOrderBy = array(), $arSelect = array(), $nSize = 0, $nLimit = 0)
	{
		global $LIB, $DB, $USER;

		if ($nSize) {
			$LIB['NAV']->Setting['SIZE'] = $nSize;
			$LIB['NAV']->Setting['TOTAL'] = 0;
		}

		if ($nLimit) {
			$LIB['NAV']->Setting = array();
		}

		if ($nUser) {
			$arFilter['USER'] = (int)$nUser;
		} else {
			if ($USER) {
				$arFilter['USER'] = $USER['ID'];
			} else {
				$arFilter['SESSION'] = session_id();
			}
		}

		$arCFilter = array('FROM' => 'k2_mod_shop_cart', 'WHERE' => $arFilter, 'ORDER_BY' => $arOrderBy, 'SELECT' => $arSelect, 'SIZE' => $nSize, 'LIMIT' => $nLimit);

		$sSQL = $DB->CSQL($arCFilter);

		if ((!$arList = $DB->Rows($sSQL)) && $_GET['page'] > 1) {
			$_GET['page'] = 1;
			$sSQL = $DB->CSQL($arCFilter);
			$arList = $DB->Rows($sSQL);
		}

		if ($arList) {
			$arCount = $DB->Row("SELECT FOUND_ROWS()");
			$LIB['NAV']->Setting['TOTAL'] = $arCount['FOUND_ROWS()'];
			for ($i = 0; $i < count($arList); $i++) {
				if ($arList[$i]['DATA']) {
					$arList[$i]['DATA'] = unserialize($arList[$i]['DATA']);
				}
				if ($arList[$i]['DATA_TMP']) {
					$arList[$i]['DATA_TMP'] = unserialize($arList[$i]['DATA_TMP']);
				}
			}
		}

		return $arList;
	}

	function Add($arPar)
	{
		global $LIB, $USER, $DB;

		if (!$arPar['CODE']) {
			$this->Error = 'Не указан код товара';

			return false;
		}

		if (!$arPar['ARTICLE']) {
			$this->Error = 'Не указан артикул товара';

			return false;
		}

		$arPar['PRICE'] = number_format($arPar['PRICE'], 2, '.', '');

		if ($arPar['PRICE'] == '0.00') {
			$this->Error = 'Не указана цена товара';

			return false;
		}

		if (!$arPar['NAME']) {
			$this->Error = 'Не указано название товара';

			return false;
		}

		if (!$arPar['DATA']) {
			$arPar['DATA'] = array();
		}
		if (!$arPar['DATA_TMP']) {
			$arPar['DATA_TMP'] = array();
		}

		$nSession = session_id();

		$nQuantity = $arPar['QUANTITY'];

		if ($nQuantity < 1) {
			$this->Error = 'Не указано количество товара';

			return false;
		}

		if ($USER) {
			$arCart = $DB->Row("SELECT `ID`, `QUANTITY` FROM `k2_mod_shop_cart` WHERE `USER` = '".$USER['ID']."' AND `CODE` = '".DBS($arPar['CODE'])."'");
			$nQuantity += $arCart['QUANTITY'];
		} elseif ($_SESSION['SHOP_CART'][$arPar['CODE']]) {
			$arCart = $DB->Row("SELECT `ID`, `QUANTITY` FROM `k2_mod_shop_cart` WHERE `USER` = 0 AND `SESSION` = '".$nSession."' AND `CODE` = '".DBS($arPar['CODE'])."'");
			$nQuantity += $arCart['QUANTITY'];
		}

		if ($arCart) {
			if ($DB->Query("
			UPDATE
				`k2_mod_shop_cart`
			SET
				`QUANTITY` = '".$nQuantity."'
			WHERE
				`ID` = '".$arCart['ID']."';")
			) {
				return $arCart['ID'];
			}
		} elseif ($nID = $DB->Insert("INSERT INTO `k2_mod_shop_cart` (`USER`, `SESSION`, `DATE_CREATED`, `NAME`, `CODE`, `ARTICLE`, `PRICE`, `QUANTITY`, `DATA`, `DATA_TMP`) VALUES (
			'".(int)$USER['ID']."', '".($USER['ID'] ? '' : $nSession)."', NOW(), '".DBS($arPar['NAME'])."', '".DBS($arPar['CODE'])."', '".DBS($arPar['ARTICLE'])."', '".$arPar['PRICE']."', '".$nQuantity."', '".DBS(serialize($arPar['DATA']))."', '".DBS(serialize($arPar['DATA_TMP']))."'
		);")
		) {
			if (!$USER) {
				$_SESSION['SHOP_CART'][$arPar['CODE']] = true;
			}

			return $nID;
		}

		return false;
	}

	function Reload($arPar)
	{
		global $USER, $DB;

		$nQuantity = abs($arPar['QUANTITY']);

		if (!$arPar['CODE']) {
			$this->Error = 'Не указан код товара';

			return false;
		}

		$nSession = session_id();

		if ($USER) {
			$arCart = $DB->Row("SELECT `ID`, `QUANTITY` FROM `k2_mod_shop_cart` WHERE `USER` = '".$USER['ID']."' AND `CODE` = '".DBS($arPar['CODE'])."'");
		} elseif ($_SESSION['SHOP_CART'][$arPar['CODE']]) {
			$arCart = $DB->Row("SELECT `ID`, `QUANTITY` FROM `k2_mod_shop_cart` WHERE `USER` = 0 AND `SESSION` = '".$nSession."' AND `CODE` = '".DBS($arPar['CODE'])."'");
		}

		if ($arCart) {
			if ($nQuantity && $DB->Query("UPDATE `k2_mod_shop_cart` SET `QUANTITY` = '".$nQuantity."' WHERE `ID` = '".$arCart['ID']."'")) {
				return $arCart['ID'];
			} else {
				$bDelete = true;
			}
		} elseif ($nQuantity) {
			return $this->Add($arPar);
		} else {
			$bDelete = true;
		}
		if ($bDelete) {
			if ($USER) {
				$DB->Query("DELETE FROM `k2_mod_shop_cart` WHERE `USER` = '".(int)$USER['ID']."' AND `CODE` = '".DBS($arPar['CODE'])."'");
			} else {
				$DB->Query("DELETE FROM `k2_mod_shop_cart` WHERE `SESSION` = '".$nSession."' AND `CODE` = '".DBS($arPar['CODE'])."'");
				unset($_SESSION['SHOP_CART'][$arPar['CODE']]);
			}

			return true;
		}

		return false;
	}

	function Delete($nID)
	{
		global $DB;

		if (!$arCart = $this->ID($nID)) {
			return false;
		}
		$DB->Query("DELETE FROM `k2_mod_shop_cart` WHERE `ID` = '".(int)$nID."'");
		unset($_SESSION['SHOP_CART'][$arCart['CODE']]);
	}

	function Clear()
	{
		global $USER, $DB;

		if ($USER) {
			$DB->Query("DELETE FROM `k2_mod_shop_cart` WHERE `USER` = '".$USER['ID']."'");
		} else {
			$DB->Query("DELETE FROM `k2_mod_shop_cart` WHERE `SESSION` = '".session_id()."'");
			unset($_SESSION['SHOP_CART']);
		}
	}

	function Quantity()
	{
		global $DB, $USER;

		if ($USER) {
			$arCart = $DB->Rows("SELECT `QUANTITY` FROM `k2_mod_shop_cart` WHERE `USER` = '".$USER['ID']."'");
		} elseif ($_SESSION['SHOP_CART']) {
			$arCart = $DB->Rows("SELECT `QUANTITY` FROM `k2_mod_shop_cart` WHERE `USER` = 0 AND `SESSION` = '".session_id()."'");
		}
		if ($arCart) {
			return count($arCart);
		}

		return 0;
	}

	function Sum()
	{
		global $DB, $USER;

		if ($USER) {
			$arCart = $DB->Rows("SELECT `PRICE`, `QUANTITY` FROM `k2_mod_shop_cart` WHERE `USER` = '".$USER['ID']."'");
		} elseif ($_SESSION['SHOP_CART']) {
			$arCart = $DB->Rows("SELECT `PRICE`, `QUANTITY` FROM `k2_mod_shop_cart` WHERE `USER` = 0 AND `SESSION` = '".session_id()."'");
		}
		if ($arCart) {
			$nSum = 0;
			for ($i = 0; $i < count($arCart); $i++) {
				$nSum += $arCart[$i]['PRICE'] * $arCart[$i]['QUANTITY'];
			}

			return number_format($nSum, 2, '.', '');
		}

		return 0;
	}

	function Bind()
	{
		global $DB, $USER;

		if (!$USER) {
			return false;
		}

		$DB->Query("DELETE FROM `k2_mod_shop_cart` WHERE `USER` = '".$USER['ID']."'");
		$DB->Query("UPDATE `k2_mod_shop_cart` SET `USER` = '".$USER['ID']."', SESSION = '' WHERE `SESSION` = '".session_id()."'");
		unset($_SESSION['SHOP_CART']);

		return true;
	}
}

?>