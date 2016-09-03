<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

tab(array(array('Раздел', '/section/edit.php?section='.$_SECTION), array('Наполнение', '/section/content/?section='.$_SECTION, 1)));

$arSBlock = $LIB['SECTION_BLOCK']->ID($_SECTION_BLOCK);
$arSection = $LIB['SECTION']->ID($arSBlock['SECTION']);
$arBlock = $LIB['BLOCK']->ID($arSBlock['BLOCK'], 1);

if(!$arSBlock || !$arSection || !$arBlock){
	Redirect('/k2/admin/');
}

$arSBlockList = $LIB['SECTION_BLOCK']->Rows($arSection['ID']);
$arTab = array();
for($i=0; $i<count($arSBlockList); $i++)
{
	$arTab[] = array($arSBlockList[$i]['NAME'], '/section/content/?section='.$_SECTION.'&section_block='.$arSBlockList[$i]['ID'], ($_SECTION_BLOCK == $arSBlockList[$i]['ID']));
}
if($arTab){
	tab_($arTab);
}

if($_CATEGORY){
	if($arCategory = $LIB['BLOCK_CATEGORY']->Back($arBlock['ID'], $_CATEGORY)){
		for($i=0; $i<count($arCategory); $i++)
		{
			$arNav[] = array($arCategory[$i]['NAME'], '/section/content/?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&category='.$arCategory[$i]['ID']);
		}
		navBack($arNav);
	}
}

if($_POST){
	$_POST['CATEGORY'] = $_CATEGORY;
	if($nID = $LIB['BLOCK_ELEMENT']->Add($arSBlock['ID'], $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?section='.$_SECTION.'&section_block='.$_SECTION_BLOCK.'&id='.$nID.'&category='.$_CATEGORY.'&complite=1');
		}else{
			Redirect('/k2/admin/section/content/?section='.$arSection['ID'].'&section_block='.$_SECTION_BLOCK.'&category='.$_CATEGORY);
		}
	}
}else{
	$_POST['ACTIVE'] = 1;
}

?><div class="content">
	<h1>Добавление</h1>
    <form method="post" enctype="multipart/form-data" class="form">
    	<?formError($LIB['BLOCK_ELEMENT']->Error)?>
    	<input type="hidden" name="SECTION" value="<?=$_SECTION?>">
    	<input type="hidden" name="SECTION_BLOCK" value="<?=$_SECTION_BLOCK?>">
    	<?
	    if($arBlock['FORM_EDIT_ELEMENT'] && file_exists($_SERVER['DOCUMENT_ROOT'].$arBlock['FORM_EDIT_ELEMENT'])){
			include_once($_SERVER['DOCUMENT_ROOT'].$arBlock['FORM_EDIT_ELEMENT']);
		}else{
			if(!isset($_POST['SORT'])){
		    	$_POST['SORT'] = 10;
		    	if($arContent = $DB->Row("SELECT SORT FROM `k2_block".$arSBlock['BLOCK']."` WHERE `SECTION_BLOCK` = '".$_SECTION_BLOCK."' ORDER BY `ID` DESC LIMIT 1")){
		    		$_POST['SORT'] = $arContent['SORT'] + 10;
		    	}
		    }
			$arField = array_merge($LIB['FIELD']->Rows('k2_block'.$arBlock['ID']), $LIB['FIELD_SEPARATOR']->Rows('k2_block'.$arBlock['ID']));
			for($i=0; $i<count($arField); $i++)
			{
		        if(!$i){
		        	usort($arField, 'sortArray');
		        }
		        if(!$arField[$i]['FIELD']){
		        	?><div class="fieldGroup"><?=$arField[$i]['NAME']?></div><?
		        }else{
		       		echo $LIB['FORM']->Element($arField[$i]['ID'], '<div class="item"><div class="name">%NAME%</div><div class="field">%FIELD%</div></div>');
		        }
			}
		}
    	?>
    	<div class="moreField">
    		<a class="link"><?=($_COOKIE['K2_MORE_FIELD']?'Скрыть дополнительные поля':'Показать дополнительные поля')?></a>
    		<div class="moreFieldBox"<?if($_COOKIE['K2_MORE_FIELD']){?> style="display:block"<?}?>>
	    		<div class="item">
					<input type="hidden" name="ACTIVE" value="0"><label><input type="checkbox" name="ACTIVE" value="1"<?
					if($_POST['ACTIVE']){
						?> checked<?
					}
					?>>Активность</label>
				</div>
		        <div class="item">
					<div class="name">Сортировка<span class="star">*</span></div>
					<div class="field"><input type="text" name="SORT" value="<?=html($_POST['SORT'])?>"></div>
				</div>
				<div class="item">
					<div class="name">Альтернативный адрес</div>
					<div class="field"><input type="text" name="URL_ALTERNATIVE" value="<?=html($_POST['URL_ALTERNATIVE'])?>"></div>
				</div>
				<div class="item">
					<div class="name">Заголовок окна</div>
					<div class="field">
						<input type="text" name="SEO_TITLE" value="<?=html($_POST['SEO_TITLE'])?>">
						<div class="note">Тег &lt;TITLE&gt;</div>
					</div>
				</div>
				<div class="item">
					<div class="name">Ключевые слова</div>
					<div class="field">
						<input type="text" name="SEO_KEYWORD" value="<?=html($_POST['SEO_KEYWORD'])?>">
						<div class="note">Тег &lt;KEYWORD&gt;</div>
					</div>
				</div>
				<div class="item">
					<div class="name">Описание страницы</div>
					<div class="field">
						<textarea name="SEO_DESCRIPTION" cols="40" rows="2"><?=html($_POST['SEO_DESCRIPTION'])?></textarea>
						<div class="note">Тег &lt;DESCRIPTION&gt;</div>
					</div>
				</div>
			</div>
    	</div>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="../?section=<?=$arSection['ID']?>&section_block=<?=$_SECTION_BLOCK?>&category=<?=$_CATEGORY?>">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>