<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/header.php');
permissionCheck('USER', 'GROUP');
tab(array(array('Пользователи', '/user/'), array('Группы', '/user/group/', 1)));
if($_POST){
	if($nID = $LIB['USER_GROUP']->Add($_POST)){
		if($_POST['BAPPLY_x']){
			Redirect('edit.php?id='.$nID.'&complite=1');
		}else{
			Redirect('/k2/admin/user/group/');
		}
	}
}
?><div class="content">
	<h1>Добавление</h1>
	<form action="add.php" method="post" enctype="multipart/form-data" class="form">
		<?formError($LIB['USER_GROUP']->Error)?>
	    <div class="item">
	    	<div class="name">Название<span class="star">*</span></div>
	    	<div class="field"><input type="text" name="NAME" value="<?=html($_POST['NAME'])?>" autofocus></div>
	    </div>
	    <table width="100%" class="table">
			<tr>
				<th class="first">Тип</th>
				<th>Права доступа</th>
			</tr><?
			$arType = $DB->Rows("SELECT * FROM `k2_permission_type` ORDER BY `SORT` ASC");
			for($i=0; $i<count($arType); $i++)
			{
				?><tr class="<?
				if($i%2){
					?> odd<?
				}
				?>">
					<td><?=$arType[$i]['NAME']?></td>
					<td><?
					$arPermission = $DB->Rows("SELECT * FROM `k2_permission_default` WHERE `TYPE` = '".$arType[$i]['TYPE']."' ORDER BY `SORT` ASC");
					for($n=0; $n<count($arPermission); $n++)
					{
						?><p><label><input type="checkbox" name="PERMISSION_DEFAULT[<?=$arType[$i]['TYPE']?>][<?=$arPermission[$n]['KEY']?>]" value="1"<?
						if((!$_POST && $arPermission[$n]['CHECKED']) || $_POST['PERMISSION_DEFAULT'][$arType[$i]['TYPE']][$arPermission[$n]['KEY']]){
							?> checked="checked"<?
						}
						?>><?=$arPermission[$n]['NAME']?></label></p><?
					}
					?></td>
				</tr><?
			}
		?></table>
	    <div class="saveBlock">
			<p><span class="star">*</span> — поля, обязательные для заполнения</p>
			<p><input type="submit" class="sub" value="Сохранить"> или <a href="/k2/admin/user/group/">отменить</a></p>
		</div>
	</form>
</div><?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/footer.php');
?>