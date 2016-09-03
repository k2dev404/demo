<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/file-manager/header.php');

if(!isset($_GET['parent']) && $USER['SETTING']['FM_PARENT'] && $LIB['FILE_DIR']->ID($USER['SETTING']['FM_PARENT'])){
	Redirect('?field='.$_GET['field'].'&parent='.$USER['SETTING']['FM_PARENT']);
}

if(!$_PARENT){
	$_PARENT = 1;
}

if($_POST){
	if($_POST['NAME']){
		if($_PARENT){
			$_POST['PARENT'] = $_PARENT;
		}
		$LIB['FILE_DIR']->Add($_POST);
	}
	if($_FILES['FILE']['name']){
		$_FILES['FILE']['PATH'] = 'ophen';
		$_FILES['FILE']['TRANSLATION'] = 1;
		$_FILES['FILE']['DIR'] = $_PARENT;
		$LIB['FILE']->Upload($_FILES['FILE']);
	}
	if($_POST['URL']){
		if(preg_match("#.+/(.*?)\.(jpg|jpeg|gif|png|bmp)$#i", $_POST['URL'], $arMath) && ($sCont = httpRequest($_POST['URL']))){
			$arPar = array(
			'NAME' => urldecode($arMath[1]).'.'.$arMath[2],
			'DIR' => $_PARENT,
			'FULL_PATH' => 1
			);
			$sPath = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5($_POST['URL'].time());
			@file_put_contents($sPath, $sCont);
			$LIB['FILE']->Add($sPath, $arPar);
			@unlink($sPath);
		}
	}
}

$DB->Query("DELETE FROM `k2_user_setting` WHERE `USER` = '".$USER['ID']."' AND `ACTION` = 'FM_PARENT';");
$DB->Query("
INSERT INTO `k2_user_setting` (
	`USER`,
	`ACTION`,
	`DATA`
)VALUES(
	'".$USER['ID']."', 'FM_PARENT', '".$_PARENT."'
);");

?><div class="fm">
	<table class="fm-table">
		<tr>
			<td colspan="2" class="fm-nav"><a href="?field=<?=$_REQUEST['field']?>&parent=1">K2</a><?
			$arDir = fileDirListBack($_PARENT);
			for($i=0, $c=count($arDir); $i<$c; $i++)
			{
            	?> » <?
            	if($i == $c-1){
            		echo html($arDir[$i]['NAME']);
            	}else{
            		?><a href="?field=<?=$_REQUEST['field']?>&parent=<?=$arDir[$i]['ID']?>"><?=html($arDir[$i]['NAME'])?></a><?
            	}
			}
			?></td>
		</tr>
		<tr valign="top">
			<td class="fm-table-l">
				<div class="fm-body">
					<table class="table" width="100%">
						<tr>
							<th class="first">Название</a></th>
							<th>Размер</a></th>
							<th>Дата создания</a></th>
							<th>Действие</th>
						</tr>
						<tbody><?
						$arDir = $DB->Rows("SELECT * FROM `k2_file_dir` WHERE `PARENT` = '".$_PARENT."' ORDER BY `NAME` ASC");
						for($i=0;  $i<count($arDir); $i++)
						{
							?><tr align="center">
								<td align="left"><a href="?field=<?=$_REQUEST['field']?>&parent=<?=$arDir[$i]['ID']?>" class="fmIcon"><img src="/k2/admin/i/ext/folder.png"><?=html($arDir[$i]['NAME'])?></a></td>
								<td>-</td>
								<td><?=dateFormat($arDir[$i]['DATE_CREATED'])?></td>
								<td><a href="delete-dir.php?field=<?=$_REQUEST['field']?>&id=<?=$arDir[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a></td>
							</tr><?
							$bFile = true;
						}
						$arFile = $DB->Rows("SELECT * FROM `k2_file` WHERE `DIR` != 0 AND `DIR` = '".$_PARENT."' ORDER BY `NAME` ASC");
						for($i=0; $i<count($arFile); $i++)
						{
	                    	?><tr align="center">
								<td align="left"><a href="/files/original/<?=$arFile[$i]['PATH']?>" onclick="return fm.click('?field=<?=$_REQUEST['field']?>&parent=<?=$arFile[$i]['DIR']?>&file=<?=$arFile[$i]['ID']?>')" ondblclick="return fm.dblclick('<?=$_REQUEST['field']?>', {'path':'/files/original/<?=$arFile[$i]['PATH']?>', 'width':'<?=$arFile[$i]['WIDTH']?>', 'height':'<?=$arFile[$i]['HEIGHT']?>'})" class="fmIcon"><img src="/k2/admin/i/ext/<?=fileIcon($arFile[$i]['PATH'])?>"><?=html($arFile[$i]['NAME'])?></a></td>
								<td><?=fileByte($arFile[$i]['SIZE'])?></td>
								<td><?=dateFormat($arFile[$i]['DATE_CREATED'])?></td>
								<td><a href="delete-file.php?field=<?=$_REQUEST['field']?>&id=<?=$arFile[$i]['ID']?>" onclick="return $.prompt(this)" class="icon delete" title="Удалить"></a></td>
							</tr><?
							$bFile = true;
						}
						if(!$bFile){
							?><tr class="noblick"><td colspan="4" class="fm-empty">Пустая папка</td></tr><?
						}
						?></tbody>
					</table>
				</div>
			</td>
			<td class="fm-table-r">
				<div class="fm-preview">
					<div class="fm-preview-box">
						<?
						$arFile = false;
						($_GET['file'] && ($arFile = $LIB['FILE']->ID($_GET['file'])));
						?>
						<table class="fm-preview-photo">
							<tr>
								<td align="center"><?
								if($arFile && $LIB['FILE']->IsPhoto($arFile['ID'])){
			                        $nWidth = $arFile['WIDTH'];
                           			$nHeight = $arFile['HEIGHT'];
			                        if(($arFile['WIDTH']>174) || ($arFile['HEIGHT'] > 129)){
			                        	if(($arFile['WIDTH'] / 174) > ($arFile['HEIGHT'] / 129)){
				                        	if(174 < $arFile['WIDTH']){
								                $nHeight = round($arFile['HEIGHT'] * (174 / $arFile['WIDTH']));
								                $nWidth = 174;
								            }
			                        	}else{
	                                        if(129 < $arFile['HEIGHT']){
								                $nWidth = round($arFile['WIDTH'] * (129 / $arFile['HEIGHT']));
								                $nHeight = 129;
								            }
			                        	}
			                        }
			                        //echo $nWidth.'x'.$nHeight;
									?><img src="<?=$arFile['PATH']?>" width="<?=$nWidth?>" height="<?=$nHeight?>"><?
								}else{
									?><p>Предварительный просмотр</p><?
								}
								?></td>
							</tr>
						</table><?
						if($arFile){
							?><table align="center" class="fm-preview-table">
								<tr>
									<td class="l">Название:</td>
									<td><?=$arFile['NAME']?></td>
								</tr>
								<tr>
									<td class="l">Размер:</td>
									<td><?=fileByte($arFile['SIZE'])?></td>
								</tr>
								<tr>
									<td class="l">Тип:</td>
									<td><?=($arFile['TYPE']?$arFile['TYPE']:'-')?></td>
								</tr>
								<tr>
									<td class="l">Дата&nbsp;создания:</td>
									<td><?=dateFormat($arFile['DATE_CREATED'])?></td>
								</tr>
								<tr>
									<td class="l">Создал:</td>
									<td><?
									if($sLogin = userLogin($arFile['USER'])){
										?><a href="/k2/admin/user/?id=<?=$arFile['USER']?>" target="_blank"><?=$sLogin?></a><?
									}else{
										?>-<?
									}
									?></td>
								</tr>
							</table><?
						}
						?>

					</div>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2">
				<div id="tab_" class="fm-upload-panel"><a href="javascript:void(0)" onclick="fm.layer('file', this)" class="fm-upload-panel-active">Загрузить файл</a><span>|</span><a href="javascript:void(0)" onclick="fm.layer('site', this)">Удаленная загрузка</a><span>|</span><a href="javascript:void(0)" onclick="fm.layer('dir', this)">Создать папку</a></div>
				<form action="?field=<?=$_REQUEST['field']?>&parent=<?=$_PARENT?>" method="post" enctype="multipart/form-data">
					<div class="fm-upload-layer" id="layer-file">
						<table>
							<tr>
								<td>Выберите файл</td>
								<td><input type="file" name="FILE" class="fm-upload-bottom"></td>
								<td><input type="submit" name="BSUB" value="Отправить" class="sub"></td>
								<td><div class="loading"></div></td>
							</tr>
						</table>
					</div>
					<div class="fm-upload-layer" id="layer-site" style="display:none">
						<table>
							<tr>
								<td>Ссылка</td>
								<td><input type="text" name="URL" value="http://" size="29" class="fm-upload-bottom"></td>
								<td><input type="submit" name="BSUB" value="Отправить" class="sub"></td>
								<td><div class="loading"></div></td>
							</tr>
						</table>
					</div>
					<div class="fm-upload-layer" id="layer-dir" style="display:none">
						<table>
							<tr>
								<td>Введите название</td>
								<td><input type="text" name="NAME" class="fm-upload-bottom"></td>
								<td><input type="submit" name="BSUB" value="Отправить" class="sub"></td>
								<td><div class="loading"></div></td>
							</tr>
						</table>
					</div>
				</form>
			</td>
		</tr>
	</table>
</div><?

include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/system/file-manager/footer.php');
?>