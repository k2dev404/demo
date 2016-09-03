<?
class HandlerBeznal
{
	function Form($nOrder)
	{
    	global $MOD, $DB;

    	if(!$arOrder = $MOD['SHOP_ORDER']->ID($nOrder)){
    		return false;
    	}

    	if(!$arPayer = $DB->Row("SELECT * FROM `k2_mod_shop_payer".$arOrder['PAYER']."` WHERE `SHOP_ORDER` = '".$arOrder['ID']."'")){
		    return false;
		}
		$arHandler = $MOD['SHOP_HANDLER']->ID('beznal');

		$sHTML = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/k2/admin/module/shop/handler/beznal/html.php');

        $arSum = explode('.', $arOrder['SUM']);

        if($arPayer['FULL_COMPANY']){
        	$arPayer['COMPANY'] = $arPayer['FULL_COMPANY'];
        }

        $arReplace = array(
		'DATA_BANK' => $arHandler['DATA']['BANK'],
        'DATA_BIK' => $arHandler['DATA']['BANK_BIK'],
        'DATA_INN' => $arPayer['INN'],
        'DATA_KPP' => $arPayer['KPP'],
        'DATA_BANK_SCHET' => $arHandler['DATA']['BANK_SCHET'],
        'DATA_BANK_SCHET_POL' => $arHandler['DATA']['BANK_SCHET_POL'],
        'DATA_COMPANY' => $arHandler['DATA']['COMPANY'],
        'PAYER_COMPANY' => $arPayer['COMPANY'],

		'DATA_KOR_SCHET' => $arHandler['DATA']['KOR_SCHET'],

		'ORDER_ID' => $arOrder['ID'],
		'DATE' => date('d.m.Y'),
		'PAYER_NAME' => $arPayer['LASTNAME'].' '.$arPayer['NAME'],
		'PAYER_ADRESS' => $arPayer['ADRESS'],
		'ORDER_SUM_RUB' => $arSum[0],
		'ORDER_SUM_KOP' => $arSum[1]);

		$arOrderProduct = $MOD['SHOP_ORDER_PRODUCT']->Rows(array('SHOP_ORDER' => $arOrder['ID']));
		for($i=0; $i<count($arOrderProduct); $i++)
		{
			$sOrderProduct .= '<tr>
			<td align="center">'.($i+1).'</td>
			<td align="left">'.$arOrderProduct[$i]['NAME'].'</td>
			<td align="right">'.$arOrderProduct[$i]['QUANTITY'].'</td>
			<td align="center">шт</td>
			<td align="right">'.$arOrderProduct[$i]['PRICE'].'</td>
			<td align="right">'.number_format($arOrderProduct[$i]['PRICE']*$arOrderProduct[$i]['QUANTITY'], 2, '.', '').'</td></tr>';
			$nSum += $arOrderProduct[$i]['PRICE']*$arOrderProduct[$i]['QUANTITY'];
		}
        $arReplace['SHOP_ITEM'] = $sOrderProduct;
        $arReplace['ORDER_SUM_ITOGO'] = number_format($nSum, 2, '.', '');

		if($arHandler['DATA']['NDS']){
        	$arReplace['DATA_NDS'] = $arHandler['DATA']['NDS'].'%';
        	$arReplace['ORDER_SUM_TOTAL'] = number_format($arReplace['ORDER_SUM_ITOGO']+($arReplace['ORDER_SUM_ITOGO']/$arHandler['DATA']['NDS'])*100, 2, '.', '');
		}else{
			$arReplace['DATA_NDS'] = 'Без НДС';
			$arReplace['ORDER_SUM_TOTAL'] = $arReplace['ORDER_SUM_ITOGO'];
		}

		$arReplace['ORDER_TOTAL'] = $i;

		$arReplace['ORDER_SUM_PRO'] = $this->num2str($arReplace['ORDER_SUM_ITOGO']);

        foreach($arReplace as $sKey => $sValue)
        {
        	$sHTML = str_replace('%'.$sKey.'%', $sValue, $sHTML);
        }

        return $sHTML;
	}

	function SaveHTML($nOrder)
	{
		global $MOD;

		if(!$arOrder = $MOD['SHOP_ORDER']->ID($nOrder)){
    		return false;
    	}
		$sFile = '/files/ophen/'.md5($arOrder['ID'].$arOrder['DATE_CREATED']).'.html';
        if(file_exists($_SERVER['DOCUMENT_ROOT'].$sFile)){
        	return $sFile;
        }

		if(!$sHTML = $this->Form($nOrder)){
			return false;
		}

		file_put_contents($_SERVER['DOCUMENT_ROOT'].$sFile, $sHTML);

		return $sFile;
	}

    function num2str($inn, $stripkop=false) {
        $nol = 'ноль';
        $str[100]= array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот', 'восемьсот','девятьсот');
        $str[11] = array('','десять','одиннадцать','двенадцать','тринадцать', 'четырнадцать','пятнадцать','шестнадцать','семнадцать', 'восемнадцать','девятнадцать','двадцать');
        $str[10] = array('','десять','двадцать','тридцать','сорок','пятьдесят', 'шестьдесят','семьдесят','восемьдесят','девяносто');
        $sex = array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять')
        );
        $forms = array(
            array('копейка', 'копейки', 'копеек', 1),
            array('рубль', 'рубля', 'рублей',  0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов',  0),
            array('миллиард', 'миллиарда', 'миллиардов',  0),
            array('триллион', 'триллиона', 'триллионов',  0),
        );
        $out = $tmp = array();
        $tmp = explode('.', str_replace(',','.', $inn));
        $rub = number_format($tmp[ 0], 0,'','-');
        if ($rub== 0) $out[] = $nol;
        $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0,2) : '00';
        $segments = explode('-', $rub);
        $offset = sizeof($segments);
        if ((int)$rub== 0) {
            $o[] = $nol;
            $o[] = $this->morph( 0, $forms[1][ 0],$forms[1][1],$forms[1][2]);
        }
        else {
            foreach ($segments as $k=>$lev) {
                $sexi= (int) $forms[$offset][3];
                $ri = (int) $lev;
                if ($ri== 0 && $offset>1) {
                    $offset--;
                    continue;
                }
                $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);
                $r1 = (int)substr($ri, 0,1);
                $r2 = (int)substr($ri,1,1);
                $r3 = (int)substr($ri,2,1);
                $r22= (int)$r2.$r3;
                if ($ri>99) $o[] = $str[100][$r1];
                if ($r22>20) {
                    $o[] = $str[10][$r2];
                    $o[] = $sex[ $sexi ][$r3];
                }
                else {
                    if ($r22>9) $o[] = $str[11][$r22-9];
                    elseif($r22> 0) $o[] = $sex[ $sexi ][$r3];
                }
                $o[] = $this->morph($ri, $forms[$offset][ 0],$forms[$offset][1],$forms[$offset][2]);
                $offset--;
            }
        }
        if (!$stripkop) {
            $o[] = $kop;
            $o[] = $this->morph($kop,$forms[ 0][ 0],$forms[ 0][1],$forms[ 0][2]);
        }
        return preg_replace("/\s{2,}/",' ',implode(' ',$o));
    }

    function morph($n, $f1, $f2, $f5) {
        $n = abs($n) % 100;
        $n1= $n % 10;
        if ($n>10 && $n<20) return $f5;
        if ($n1>1 && $n1<5) return $f2;
        if ($n1==1) return $f1;
        return $f5;
    }

}


?>