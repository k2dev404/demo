<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

$arSBlock = $LIB['SECTION_BLOCK']->ID($_SECTION_BLOCK);
$arBlock = $LIB['BLOCK']->ID($arSBlock['BLOCK'], 1);
$arSection = $LIB['SECTION']->ID($arSBlock['SECTION']);

if(!$arSBlock || !$arBlock || !$arSection){
	Redirect('/k2/admin/');
}
$_SECTION = $arSBlock['SECTION'];
$_BLOCK = $arSBlock['BLOCK'];

tab(array(array('Раздел', '/section/edit.php?section='.$_SECTION), array('Наполнение', '/section/content/?section='.$_SECTION, 1)));

$arSBlockAll = $LIB['SECTION_BLOCK']->Rows($_SECTION);
for($i=0; $i<count($arSBlockAll); $i++)
{
	$arTab[] = array($arSBlockAll[$i]['NAME'], '/section/content/?section='.$_SECTION.'&section_block='.$arSBlockAll[$i]['ID'], ($_SECTION_BLOCK == $arSBlockAll[$i]['ID']), $arSBlockAll[$i]['ACTIVE']);
}

tab_($arTab);

if($_CATEGORY){
	if($arCategory = $LIB['BLOCK_CATEGORY']->Back($arBlock['ID'], $_CATEGORY)){
		for($i=0; $i<count($arCategory); $i++)
		{
			$arNav[] = array($arCategory[$i]['NAME'], '/section/content/?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&category='.$arCategory[$i]['ID']);
		}
		navBack($arNav);
	}
}
?>
<div class="content">
	<h1>Список элементов</h1>
	<?
	$arField['ID'] = array('NAME' => 'ID', 'FORMAT' => '', 'ALIGN' => 'center', 'ACTIVE' => 1);
	$arRows = $DB->Rows("SHOW COLUMNS FROM `k2_block".$_BLOCK."`");

	$arField = fieldFormat('k2_block'.$_BLOCK, $arField);

	$QB = new QueryBuilder;
	$QB->From('k2_block'.$_BLOCK.' AS B');
	$QB->Where('B.SECTION_BLOCK = ?', $_SECTION_BLOCK);
	$QB->AndWhere('B.CATEGORY = ?', $_CATEGORY);
	$QB->Num = true;

	if($arSettingView = userSettingView(false, array('TYPE' => 1, 'OBJECT' => $_BLOCK))){
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
	$arSort = array('FIELD' => 'ID', 'METHOD' => 'asc');
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
	$QB->Select('B.ACTIVE');
	$QB->ConcatField('B.ACTIVE');
	if($_GET['q']){
		$QB->SearchText = $_GET['q'];
	}

	#p($QB->Build());
	#p($DB->Rows($QB->Build()));

	$arList = $DB->Rows($QB->Build());
	$arCount = $DB->Row("SELECT FOUND_ROWS()");
	$sURI = '?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&category='.$_CATEGORY;
	$nPage = $_PAGE;
	$sNav = navPage($arCount['FOUND_ROWS()'], $nLimit, $sURI.'&');
	if($nPage > $_PAGE){
		Redirect('/k2/admin/section/content/'.$sURI);
	}

	$arUserLogin = userAllLogin();
	?><table width="100%" class="nav">
		<tr>
			<td><?=$sNav?></td>
			<td align="right"><a href="#" onclick="return $.layer({get:'setting.php?block=<?=$arBlock['ID']?>&back=<?=base64_encode($_SERVER['REQUEST_URI'])?>', title:'Настройки отображения', w:600}, function(){table.sort(-1, 'sf-body');})" class="button">Настройки отображения</a><?
			if($arBlock['CATEGORY'] && !$arCount['FOUND_ROWS()']){
				?><a href="../category/add.php?section=<?=$_SECTION?>&section_block=<?=$_SECTION_BLOCK?>&category=<?=$_CATEGORY?>" class="button">Добавить категорию</a><?
			}
			?><a href="add.php?section=<?=$_SECTION?>&section_block=<?=$_SECTION_BLOCK?>&category=<?=$_CATEGORY?>" class="button">Добавить элемент</a><?
			if($USER['SETTING']['CATEGORY_MOVE']['BLOCK'] == $_BLOCK){
                $arCBackID = array();
           		$arCBack = $LIB['BLOCK_CATEGORY']->Back(array('BLOCK' => $_BLOCK, 'ID' => $_CATEGORY));
           		for($i=0; $i<count($arCBack); $i++)
           		{
                	$arCBackID[] = $arCBack[$i]['ID'];
           		}

           		for($i=0; $i<count($USER['SETTING']['CATEGORY_MOVE']['ID']); $i++)
           		{
                	if(in_array($USER['SETTING']['CATEGORY_MOVE']['ID'][$i], $arCBackID)){
	           			$bDontInsert = true;
	           		}
           		}

           		if(!$bDontInsert){
           			?><a href="/k2/admin/section/content/category/insert.php?section=<?=$_SECTION?>&section_block=<?=$_SECTION_BLOCK?>&category=<?=$_CATEGORY?>" class="button insert">Вставить категорию</a><?
           			$bInsert = true;
           		}
    		}
			if($USER['SETTING']['ELEMENT_MOVE']['BLOCK'] == $_BLOCK){
           		?><a href="insert.php?section=<?=$_SECTION?>&section_block=<?=$_SECTION_BLOCK?>&category=<?=$_CATEGORY?>" class="button insert">Вставить элемент</a><?
    			$bInsert = true;
    		}
    		if($bInsert){
    			?><a href="insert-cancel.php" class="button cancal">Отменить вставку</a><?
    		}
			?></td>
			</tr>
	</table>
	<form method="post" id="form">
		<input type="hidden" name="session" value="<?=$USER['SESSION']?>">
		<input type="hidden" name="section" value="<?=$_SECTION?>">
		<input type="hidden" name="section_block" value="<?=$_SECTION_BLOCK?>">
		<input type="hidden" name="category" value="<?=$_CATEGORY?>">
		<input type="hidden" name="block" value="<?=$_BLOCK?>">
		<table width="100%" class="table">
			<tr><?=tableHead($arTableHead, $arSort);?></tr>
			<tbody class="table-body"><?
			for($i=0; $i<count($arList); $i++)
			{
				?><tr<?
				if(!$arList[$i]['ACTIVE']){
					?> class="passive"<?
				}
				?> goto="edit.php?section=<?=$_SECTION?>&section_block=<?=$_SECTION_BLOCK?>&category=<?=$_CATEGORY?>&id=<?=$arList[$i]['ID']?>">
					<td><input type="checkbox" name="ID[]" value="<?=$arList[$i]['ID']?>"></td><?
					tableBody(array(
					'CONTENT' => $arList[$i],
					'FIELD' => $arField,
					'USER_LOGIN' => $arUserLogin,
					'PREVIEW' => $arSettingView['PREVIEW']
					));
					?>
					<td align="center" class="action"><a href="delete.php?section=<?=$_SECTION?>&section_block=<?=$_SECTION_BLOCK?>&category=<?=$_CATEGORY?>&id=<?=$arList[$i]['ID']?>&session=<?=$USER['SESSION']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a><a href="edit.php?section=<?=$_SECTION?>&section_block=<?=$_SECTION_BLOCK?>&category=<?=$_CATEGORY?>&id=<?=$arList[$i]['ID']?>" class="icon edit" title="Редактировать"></a></td>
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
			        	<option value="active">Активировать</option>
			        	<option value="deactive">Деактивировать</option>
			        	<option value="move">Перенести</option>
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
		                		$.prompt(this, {'href':'/k2/admin/section/content/element/delete.php<?=$sURI?>&session=<?=$USER['SESSION']?>', 'yes':'return actionDelete(1)', 'no':'return actionDelete(0)'});
		                	}else{
                            	$('#form').attr('action', val+'.php').submit();
		                	}
		                }
		            });
		            $('#form input').change(function(){
		            	$('#action')[$('.table-body input:checkbox:checked').size()?'removeAttr':'attr']('disabled', 'disabled');
		            });

		            <?
		            if($_GET['move_message']){
		            	?>
		            	$.alert({'text':'Теперь выберите необходимый раздел и нажмите кнопку "Вставить элемент"'});
		            	<?
		            }
		            ?>
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
		        					?><a href="?section=<?=$_SECTION?>&section_block=<?=$_SECTION_BLOCK?>&category=<?=$_CATEGORY?>" class="closeMini" title="Отменить поиск"></a><?
		        				}
		        				?><div class="search-box"><input type="text" name="q" value="<?=html($_GET['q'])?>"></div></td>
		        				<td><a href="#" onclick="$('#form').attr('method', 'get').submit(); return false;" class="icon search" title="Искать"></a></td>
		        			</tr>
		        		</table>
		        	</td>
		            <td align="right">На странице <select id="sizePage" url="/k2/admin/section/content/element/<?=$sURI?>&"><?
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