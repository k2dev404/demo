<div class="item">	<input type="hidden" name="REQUIRED" value="0"><label><input type="checkbox" name="REQUIRED" value="1"<?	if($_POST['REQUIRED']){		?> checked="checked"<?	}	?>>Обязательное для заполнение</label></div><div class="item">	<input type="hidden" name="MULTIPLE" value="0"><label><input type="checkbox" name="MULTIPLE" value="1"<?	if($_POST['MULTIPLE']){		?> checked="checked"<?	}	?>>Множественное</label></div><div class="item">	<input type="hidden" name="SETTING[TRANSLATION]" value="0"><label><input type="checkbox" name="SETTING[TRANSLATION]" value="1"<?	if($_POST['SETTING']['TRANSLATION']){		?> checked<?	}	?>>Перевести название файла в транслит</label></div><div class="item">	<div class="name">Максимальный размер файла(в байтах)</div>	<div class="field"><input type="text" name="SETTING[FILESIZE]" value="<?=html($_POST['SETTING']['FILESIZE'])?>"></div></div><div class="item">	<div class="name">Файлы для загрузки</div>	<div class="field"><select name="SETTING[FILE_TYPE]" id="setting_type"><?	$i=0;	$arSettingFileType = array('Любые', 'Только картинки', 'Текстовые документы');	foreach($arSettingFileType as $sValue)	{		?><option value="<?=$i?>"<?		if($_POST['SETTING']['FILE_TYPE'] == $i){		?> selected="selected"<?		}		?>><?=$sValue?></option><?		$i++;	}	?></select></div></div><div class="item">	<div class="name">Допустимые расширения</div>	<div class="field"><input type="text" name="SETTING[FILE_EXT]" value="<?=html($_POST['SETTING']['FILE_EXT'])?>" id="ext"></div></div><div id="image" <?if($_POST['SETTING']['FILE_TYPE'] != 1){?> style="display:none"<?}?>>	<div class="item">		<div class="name">Минимальный размер при загрузки</div>		<table>			<tr>				<td><input type="text" name="SETTING[MIN][WIDTH]" value="<?=html($_POST['SETTING']['MIN']['WIDTH'])?>" style="width:48px"></td>				<td>&nbsp;x&nbsp;</td>				<td><input type="text" name="SETTING[MIN][HEIGHT]" value="<?=html($_POST['SETTING']['MIN']['HEIGHT'])?>" style="width:48px"></td>				<td>&nbsp;px</td>			</tr>		</table>	</div>	<div class="item">		<div class="name">Уменьшать картинку до размеров</div>		<table>			<tr>				<td><input type="text" name="SETTING[RESIZE][WIDTH]" value="<?=html($_POST['SETTING']['RESIZE']['WIDTH'])?>" style="width:48px"></td>				<td>&nbsp;x&nbsp;</td>				<td><input type="text" name="SETTING[RESIZE][HEIGHT]" value="<?=html($_POST['SETTING']['RESIZE']['HEIGHT'])?>" style="width:48px"></td>				<td width="30">&nbsp;px</td>				<td><input type="hidden" name="SETTING[RESIZE][FIX]" value="0">				<label><input type="checkbox" name="SETTING[RESIZE][FIX]" value="1"<?				if($_POST['SETTING']['RESIZE']['FIX']){					?> checked="checked"<?				}				?> id="fix">Фиксированный</label>				</td>			</tr>		</table>	</div></div><script>$(function(){	$('#setting_type').change(function(){		var ext = '';		val = $(this).val();		if(val == '1'){			$('#image').show();			ext = 'jpg, jpeg, gif, png';		}else{			$('#image').hide();		}		if(val == '2'){			ext = 'doc, txt, rtf';		}		$('#ext').val(ext);	});});</script>