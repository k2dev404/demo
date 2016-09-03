<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('MODULE', 'SHOP');

tab(array(array('Модули', '/module/'), array('Интернет-магазин', '/module/shop/', 1)));
tab_(array(
	array('Заказы', '/module/shop/', 1),
	array('Плательщики', '/module/shop/payer/'),
	array('Адреса', '/module/shop/address/'),
	array('Статусы', '/module/shop/status/'),
	array('Оплата', '/module/shop/payment/'),
	array('Доставка', '/module/shop/delivery/'),
	array('Обработчики', '/module/shop/handler/')
));

$arModule = $LIB['MODULE']->ID('SHOP');

$arField['ID'] =           array('NAME' => 'ID', 'FORMAT' => '', 'ALIGN' => 'center', 'ACTIVE' => 1);
$arField['DATE_CREATED'] = array('NAME' => 'Дата заказа', 'FORMAT' => 'DATE', 'ALIGN' => 'center', 'ACTIVE' => 1);
$arField['USER_CREATED'] = array('NAME' => 'Пользователь', 'FORMAT' => 'USER', 'ALIGN' => 'left', 'ACTIVE' => 1);
$arField['SUM'] =          array('NAME' => 'Сумма', 'FORMAT' => '', 'ALIGN' => 'right', 'ACTIVE' => 1);

$QB = new QueryBuilder;
$QB->From('k2_mod_shop_order AS SO');
$QB->Select('SO.STATUS');
$QB->Num = true;

$nLimit = 20;
$arSort = array('FIELD' => 'ID', 'METHOD' => 'desc');
if($arRows = userSettingSession(true)){
    if($arField[$arRows['PAGE_SORT']['FIELD']]){
    	$arSort = $arRows['PAGE_SORT'];
    }
    if($arRows['PAGE_SIZE'] > 1){
		$nLimit = $arRows['PAGE_SIZE'];
	}
}
$QB->OrderBy('SO.'.$arSort['FIELD'].' '.$arSort['METHOD']);
$nOffset = 0;
if($_PAGE>1){
	$nOffset = $_PAGE*$nLimit-$nLimit;
}
$QB->Limit($nOffset.', '.$nLimit);
$arTableHead[] = array('HTML' => '<th width="1%" class="first"><input type="checkbox" title="Отметить поля" onclick="table.check.all(this, \'.table-body\')"></th>');
$arTableHead = fieldTableHead('SO', $QB, $arField, $arSort, $arTableHead);
$arTableHead[] = array('NAME' => 'Статус');
$arTableHead[] = array('NAME' => 'Товары');
$arTableHead[] = array('NAME' => 'Действие');

for($i=0; $i<count($arTableHead); $i++)
{
	if(in_array($arTableHead[$i]['FIELD'], array('STATUS', 'PAYMENT', 'DELIVERY'))){
		$arTableHead[$i]['SORT'] = false;
	}
}

$arList = $DB->Rows($QB->Build());
$arCount = $DB->Row("SELECT FOUND_ROWS()");
$nPage = $_PAGE;
$sNav = navPage($arCount['FOUND_ROWS()'], $nLimit);
if($nPage > $_PAGE){
	Redirect('/k2/admin/module/shop/');
}

$arUserLogin = userAllLogin();

$arStatus_ = $MOD['SHOP_STATUS']->Rows();
for($i=0; $i<count($arStatus_); $i++)
{
    $arStatus[$arStatus_[$i]['ID']] = $arStatus_[$i]['NAME'];
}

?><div class="content">
    <h1>Список заказов</h1>
    <table width="100%" class="nav">
    	<tr>
        	<td><?=$sNav?></td>
        </tr>
    </table>
    <form method="post" id="form">
	    <table width="100%" class="table">
	    	<tr><?=tableHead($arTableHead, $arSort);?></tr>
	    	<tbody class="table-body"><?
		    	for($i=0; $i<count($arList); $i++)
				{
					?><tr goto="edit.php?id=<?=$arList[$i]['ID']?>">
						<td><input type="checkbox" name="ID[]" value="<?=$arList[$i]['ID']?>"></td><?
						tableBody(array(
						'CONTENT' => $arList[$i],
						'FIELD' => $arField,
						'USER_LOGIN' => $arUserLogin,
						'PREVIEW' => $arSettingView['PREVIEW']
						));
						?>
						<td><?=($arStatus[$arList[$i]['STATUS']]?$arStatus[$arList[$i]['STATUS']]:'-')?></td>
						<td class="textFix"><?
						$arProduct = $MOD['SHOP_ORDER_PRODUCT']->Rows(array('ORDER' => $arList[$i]['ID']), array('ID' => 'ASC'), array('NAME'));
						for($j=0; $j<count($arProduct); $j++)
						{
			            	if($j){
			            		?>, <?
			            	}
			            	echo $arProduct[$j]['NAME'];
						}
						?></td>
						<td align="center"><a href="delete.php?id=<?=$arList[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?id=<?=$arList[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
					</tr><?
				}
				if(!$i){
					?><tr class="noblick empty">
						<td colspan="<?=count($arTableHead)+2?>" align="center" height="100">Нет данных</td>
					</tr><?
				}
				?>
			</tbody>
		</table>
	    <table width="100%" class="nav">
	    	<tr>
	        	<td>
	            	<div class="navPage"><?=$sNav?></div>
	            </td>
	        </tr>
	    </table>
	</form>
    <table width="100%" class="select">
    	<tr>
        	<td>С отмеченными<select id="action" disabled><option value="">Выбрать действие</option><option value="delete">Удалить</option></select>
        	<script>
            $('#action').change(function(){
            	val = $(this).val();
            	if(!val){
            		return false;
            	}
            	data = $('#form').serialize();
                if(data.length){
                	if(val == 'delete'){
                		$.prompt(this, {'href':'/k2/admin/module/shop/', 'yes':'return actionDelete(1)', 'no':'return actionDelete(0)'});
                	}
                }
            });
            $('#form input').change(function(){
            	$('#action')[$('.table-body input:checkbox:checked').size()?'removeAttr':'attr']('disabled', 'disabled');
            });
        	</script>
        	</td>
            <td align="right">На странице <select id="sizePage" url="/k2/admin/module/shop/?"><?
            $arSize = array(10, 20, 50, 100);
            for($i=0; $i<count($arSize); $i++)
            {
            	?><option<?
            	if($nLimit == $arSize[$i]){
            		?> selected<?
            	}
            	?>><?=$arSize[$i]?></option><?
            }
            ?></select></td>
        </tr>
    </table>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>