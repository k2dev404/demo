<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

if(!$arForm = $LIB['FORM']->ID($_GET['form'])){
	Redirect('/k2/admin/message/');
}

tab(array(array('Сообщения', '/message/', 1)));
$K2->Menu('TAB_SUB', array(array($arForm['NAME'], '/message/form/?form='.$arForm['ID'], 1)));

?>
<div class="content">
	<h1>Список сообщений</h1>
	<?
	$arField['ID'] = array('NAME' => 'ID', 'FORMAT' => '', 'ALIGN' => 'center', 'ACTIVE' => 1);
	$arField['DATE_CREATED'] = array('NAME' => 'Дата создания', 'FORMAT' => 'DATE_TIME', 'ALIGN' => 'center', 'ACTIVE' => 1);
	$arRows = $DB->Rows("SHOW COLUMNS FROM `k2_form".$arForm['ID']."`");

	$arField = fieldFormat('k2_form'.$arForm['ID'], $arField);

	$QB = new QueryBuilder;
	$QB->From('k2_form'.$arForm['ID'].' AS B');
	$QB->Num = true;

	if($arSettingView = userSettingView(false, array('TYPE' => 11, 'OBJECT' => $arForm['ID']))){
		for($i=0; $i<count($arRows); $i++)
		{
			$arIssetField[$arRows[$i]['Field']] = 1;
		}
		$arNewField = array();
		$arData = unserialize($arSettingView['DATA']);
		foreach($arData as $sKey => $arValue)
		{
			if($arIssetField[$sKey]){
				$arNewField[$sKey] = ($arField[$sKey]['NAME']?$arField[$sKey]:$arValue);
				$arNewField[$sKey]['ACTIVE'] = $arValue['ACTIVE'];
				$arNewField[$sKey]['ALIGN'] = $arValue['ALIGN'];
			}
	   	}
	   	$arField = $arNewField;
	}

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
	$QB->OrderBy('B.'.$arSort['FIELD'].' '.$arSort['METHOD']);

	$nOffset = 0;
	if($_PAGE>1){
		$nOffset = $_PAGE*$nLimit-$nLimit;
	}

	$QB->Limit($nOffset.', '.$nLimit);

	$arTableHead[] = array('HTML' => '<th width="1%" class="first"><input type="checkbox" title="Отметить поля" onclick="table.check.all(this, \'.table-body\')"></th>');
	$arTableHead = fieldTableHead('B', $QB, $arField, $arSort, $arTableHead);
	$arTableHead[] = array('NAME' => 'Действие');

	$QB->Select('B.ID');
	$QB->ConcatField('B.ID');
	if($_GET['q']){
		$QB->SearchText = $_GET['q'];
	}

	#p($QB->Build());
	#p($DB->Rows($QB->Build()));

	$arList = $DB->Rows($QB->Build());
	$arCount = $DB->Row("SELECT FOUND_ROWS()");
	$nPage = $_PAGE;
	$sNav = navPage($arCount['FOUND_ROWS()'], $nLimit, '?form='.$arForm['ID'].'&');
	if($nPage > $_PAGE){
		Redirect('/k2/admin/message/form/?form='.$arForm['ID']);
	}

	$arUserLogin = userAllLogin();
	?><table width="100%" class="nav">
		<tr>
			<td><?=$sNav?></td>
			<td align="right"><a href="#" onclick="return $.layer({get:'setting.php?form=<?=$arForm['ID']?>&back=<?=base64_encode($_SERVER['REQUEST_URI'])?>', title:'Настройки отображения', w:600}, function(){table.sort(-1, 'sf-body');})" class="button">Настройки отображения</a></td>
			</tr>
	</table>
	<form method="post" id="form">
		<input type="hidden" name="form" value="<?=$arForm['ID']?>">
		<table width="100%" class="table">
			<tr><?=tableHead($arTableHead, $arSort);?></tr>
			<tbody class="table-body"><?
			for($i=0; $i<count($arList); $i++)
			{
				?><tr goto="edit.php?form=<?=$arForm['ID']?>&id=<?=$arList[$i]['ID']?>">
					<td><input type="checkbox" name="ID[]" value="<?=$arList[$i]['ID']?>"></td><?
					tableBody(array(
					'CONTENT' => $arList[$i],
					'FIELD' => $arField,
					'USER_LOGIN' => $arUserLogin,
					'PREVIEW' => $arSettingView['PREVIEW']
					));
					?>
					<td align="center" class="action"><a href="delete.php?form=<?=$arForm['ID']?>&id=<?=$arList[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?form=<?=$arForm['ID']?>&id=<?=$arList[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
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
		</table><?
		if($i || $_GET['q']){
			?><table width="100%" class="select">
		    	<tr>
		        	<td>С отмеченными<select id="action" disabled>
			        	<option value="">Выбрать действие</option>
			        	<option value="delete">Удалить</option>
			        </select>
		        	<script>
		            $('#action').change(function(){
		            	val = $(this).val();
		            	if(!val){
		            		return false;
		            	}
		            	data = $('#form').serialize();
		                if(data.length){
		                	if(val == 'delete'){
		                		$.prompt(this, {'href':'/k2/admin/message/form/delete.php?id=<?=$arForm['ID']?>', 'yes':'return actionDelete(1)', 'no':'return actionDelete(0)'});
		                	}else{
                            	$('#form').attr('action', val+'.php').submit();
		                	}
		                }
		            });
		            $('#form input').change(function(){
		            	$('#action')[$('.table-body input:checkbox:checked').size()?'removeAttr':'attr']('disabled', 'disabled');
		            });
		        	</script>
		        	</td>
		        	<td width="60%">
		        		<table width="100%" class="search">
		        			<tr>
		        				<td>Поиск</td>
		        				<td width="99%"<?
		        				if($_GET['q']){
		        					?> class="q"<?
		        				}
		        				?>><?
		        				if($_GET['q']){
		        					?><a href="/k2/admin/message/form/?form=<?=$arForm['ID']?>" class="closeMini" title="Отменить поиск"></a><?
		        				}
		        				?><div class="search-box"><input type="text" name="q" value="<?=html($_GET['q'])?>"></div></td>
		        				<td><a href="#" onclick="$('#form').attr('method', 'get').submit(); return false;" class="icon search" title="Искать"></a></td>
		        			</tr>
		        		</table>
		        	</td>
		            <td align="right">На странице <select id="sizePage" url="/k2/admin/message/form/?form=<?=$arForm['ID']?>&"><?
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
		    </table><?
		}
		?>
	</form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>