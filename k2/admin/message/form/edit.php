<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('SECTION_CONTENT');

if(!$arMessage = $LIB['FORM_MESSAGE']->ID($_ID, $_GET['form'])){
	Redirect('/k2/admin/message/');
}
$arForm = $LIB['FORM']->ID($_GET['form']);

tab(array(array('Сообщения', '/message/', 1)));
$K2->Menu('TAB_SUB', array(array($arForm['NAME'], '/message/form/?form='.$arForm['ID'], 1)));

if($_POST){
	if($LIB['FORM_MESSAGE']->Edit($_ID, $arForm['ID'], $_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?form='.$arForm['ID'].'&id='.$_ID.'&complite=1');
		}else{
			Redirect('/k2/admin/message/form/?form='.$arForm['ID']);
		}
	}
}else{
	$_POST = $arMessage;
}

?><div class="content">
	<h1>Редактирование</h1>
    <form action="edit.php?form=<?=$arForm['ID']?>&id=<?=$_ID?>" method="post" enctype="multipart/form-data" class="form">
    	<?formError($LIB['FORM_MESSAGE']->Error)?>
    	<?
	    $arField = array_merge($LIB['FIELD']->Rows('k2_form'.$arForm['ID']), $LIB['FIELD_SEPARATOR']->Rows('k2_form'.$arForm['ID']));
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
    	?>
		<div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/message/form/?form=<?=$arForm['ID']?>">отменить</a></p>
		</div>
    </form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>