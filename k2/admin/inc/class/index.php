<?
class K2
{
	function Template($sPathTemplate = '')
	{
		global $LIB, $MOD, $MOD_SETTING, $DB, $USER, $SETTING, $K2;

		$sFileTemplate = str_replace('/k2/admin/', '/k2/admin/template/', $_SERVER['SCRIPT_FILENAME']);

		if($sPathTemplate){
			$sFileTemplate = $_SERVER['DOCUMENT_ROOT'].'/k2/admin/template'.$sPathTemplate;
		}

		if(file_exists($sFileTemplate)){
			include($sFileTemplate);
		}
	}

	function Menu($sType, $arPar = array(), $arRight = array(), $sClass = 'subMenu')
	{
		if(!$this->MenuLink){
			$arMenu = [];
			include($_SERVER['DOCUMENT_ROOT'].'/k2/admin/menu.php');
			preg_match("#/k2/admin(/.+/)#", $_SERVER['REQUEST_URI'], $arMath);

			$arSplit = explode('/', $arMath[1]);

			$sPath = '/';
			foreach($arSplit as $sDir){
				if(!$sDir){
					continue;
				}
				$sPath .= $sDir.'/';

				foreach($arMenu as $sLink => $sName){
					if(strpos($sLink, $sPath) === 0){
						$nLevel = substr_count($sLink, '/');
						$nCurrent = (strpos($_SERVER['REQUEST_URI'], $sLink) !== false);
						if($nLevel == 2){
							$nCurrent = ($arMath[1] == $sLink);
							$nLevel = 3;
						}

						if($nLevel > 3 && strpos($_SERVER['REQUEST_URI'], $arLastLink[3]) === false){
							continue;
						}

						$this->MenuLink[$nLevel][$sLink] = array('NAME' => $sName, 'CURRENT' => $nCurrent);

						$arLastLink[$nLevel] = $sLink;
					}
				}
			}
		}

		if($sType == 'TAB'){
			?>
			<div class="tab">
				<?
				foreach($this->MenuLink[3] as $sLink => $arName){
					?>
					<a href="/k2/admin<?=$sLink?>"<?
					if($arName['CURRENT']){
						?> class="active"<?
					}
					?>><?=$arName['NAME']?></a>
					<?
				}
				?>
			</div>
			<?
		}

		if($sType == 'TAB_SUB'){
			if(!$arPar){
				$arMenuLink = $this->MenuLink[4];
				if($arMenuLink){
					foreach($arMenuLink as $sLink => $arLink){
						$arPar[] = array($arLink['NAME'], $sLink, $arLink['CURRENT']);
					}
				}
			}

			?>
		<div class="<?=$sClass?>">
			<div class="l"><?
				for($i = 0; $i < count($arPar); $i++){
					$arClass = array();
					if($i){
						?><span>|</span><?
					}
					if($arPar[$i][2]){
						$arClass[] = 'active';
					}
					if(isset($arPar[$i][3]) && !$arPar[$i][3]){
						$arClass[] = 'passive';
					}
					?><a href="/k2/admin<?=$arPar[$i][1]?>"<?
					if($arClass){
						?> class="<?=implode(' ', $arClass)?>"<?
					}
					?>><?=$arPar[$i][0]?></a><?
				}
				?></div><?
			if($arRight){
				?>
				<div class="r"><?
				for($i = 0; $i < count($arRight); $i++){
					if($i){
						?><span>|</span><?
					}
					echo $arRight[$i];
				}
				?></div><?
			}
			?>
			<div class="clear"></div></div><?
		}
	}
}

$K2 = new K2;
?>