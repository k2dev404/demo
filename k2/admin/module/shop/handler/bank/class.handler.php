<?
class HandlerBank
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
		$arHandler = $MOD['SHOP_HANDLER']->ID('bank');

		$sHTML = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/k2/admin/module/shop/handler/bank/html.php');

        $arSum = explode('.', $arOrder['SUM']);

        if($arAddress = $MOD['SHOP_ADDRESS']->ID($arOrder['USER_CREATED'])){
        	$arFullAddress = array();
        	if($arAddress['CITY']){
        		$arFullAddress[] = 'г.'.$arAddress['CITY'];
        	}
        	if($arAddress['STREET']){
        		$arFullAddress[] = 'ул.'.$arAddress['STREET'];
        	}
        	if($arAddress['DOM']){
        		if($arAddress['CORP']){
        			$arFullAddress[] = $arAddress['DOM'].'/'.$arAddress['CORP'];
        		}else{
        			$arFullAddress[] = $arAddress['DOM'];
        		}
        	}
        	if($arAddress['OFFICE']){
        		$arFullAddress[] = 'кв.'.$arAddress['OFFICE'];
        	}

        	$arPayer['ADDRESS'] = implode(', ', $arFullAddress);
        }


        $arReplace = array(
		'DATA_POL_NAME' => $arHandler['DATA']['POL_NAME'],
		'DATA_INN' => $arHandler['DATA']['INN'],
		'DATA_KPP' => $arHandler['DATA']['KPP'],
		'DATA_N_SCHET' => $arHandler['DATA']['N_SCHET'],
		'DATA_BANK' => $arHandler['DATA']['BANK'],
		'DATA_BIK' => $arHandler['DATA']['BIK'],
		'DATA_KOR_SCHET' => $arHandler['DATA']['KOR_SCHET'],

		'ORDER_ID' => $arOrder['ID'],
		'DATE' => date('d.m.Y'),
		'PAYER_NAME' => $arPayer['LASTNAME'].' '.$arPayer['NAME'],
		'PAYER_ADDRESS' => $arPayer['ADDRESS'],
		'ORDER_SUM_RUB' => $arSum[0],
		'ORDER_SUM_KOP' => $arSum[1]);

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
}


?>