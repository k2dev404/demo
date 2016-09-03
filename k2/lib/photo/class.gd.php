<?

class PhotoGD
{
	function Resize($arPar)
	{
		$this->Prop = $arPar;
		if (!$this->Prop['FIX']) {
			if (!$this->Prop['SET_WIDTH']) {
				$this->Prop['SET_WIDTH'] = 100000;
			}
			if (!$this->Prop['SET_HEIGHT']) {
				$this->Prop['SET_HEIGHT'] = 100000;
			}
		}
		$arPS[0] = $this->Prop['REAL_WIDTH'];
		$arPS[1] = $this->Prop['REAL_HEIGHT'];
		if ($this->Prop['FIX']) {
			$bPriorityX = $this->Prop['REAL_WIDTH'] / $this->Prop['SET_WIDTH'] < $this->Prop['REAL_HEIGHT'] / $this->Prop['SET_HEIGHT'] ? 1 : 0;
			if ($bPriorityX) {
				if ($this->Prop['SET_WIDTH'] < $arPS[0]) {
					$arPS[1] = round($arPS[1] * ($this->Prop['SET_WIDTH'] / $arPS[0]));
					$arPS[0] = $this->Prop['SET_WIDTH'];
				}
			} else {
				if ($this->Prop['SET_HEIGHT'] < $arPS[1]) {
					$arPS[0] = round($arPS[0] * ($this->Prop['SET_HEIGHT'] / $arPS[1]));
					$arPS[1] = $this->Prop['SET_HEIGHT'];
				}
			}
			$arPar = array(0, 0, 0, 0, $arPS[0], $arPS[1], $this->Prop['REAL_WIDTH'], $this->Prop['REAL_HEIGHT']);
			$this->Crop($arPS, $arPar);
		} else {
			if ($this->Prop['SET_WIDTH'] < $arPS[0]) {
				$arPS[1] = round($arPS[1] * ($this->Prop['SET_WIDTH'] / $arPS[0]));
				$arPS[0] = $this->Prop['SET_WIDTH'];
			}
			if ($this->Prop['SET_HEIGHT'] < $arPS[1]) {
				$arPS[0] = round($arPS[0] * ($this->Prop['SET_HEIGHT'] / $arPS[1]));
				$arPS[1] = $this->Prop['SET_HEIGHT'];
			}
			$arPar = array(0, 0, 0, 0, $arPS[0], $arPS[1], $this->Prop['REAL_WIDTH'], $this->Prop['REAL_HEIGHT']);

			return $this->Crop($arPS, $arPar);
		}

		if ($this->Prop['FIX']) {
			if ($this->Prop['UPDATE_WIDTH'] == $arPS[0] && $this->Prop['UPDATE_HEIGHT'] == $arPS[1]) {
				$this->Prop['REAL_WIDTH'] = $arPS[0];
				$this->Prop['REAL_HEIGHT'] = $arPS[1];

				if ($this->Prop['SET_WIDTH'] > $this->Prop['REAL_WIDTH'] && $this->Prop['SET_HEIGHT'] > $this->Prop['REAL_HEIGHT']) {
					return $this->Crop(array($this->Prop['SET_WIDTH'], $this->Prop['SET_HEIGHT']), array(($this->Prop['SET_WIDTH'] / 2 - $this->Prop['UPDATE_WIDTH'] / 2), ($this->Prop['SET_HEIGHT'] / 2 - $this->Prop['UPDATE_HEIGHT'] / 2), 0, 0, $this->Prop['UPDATE_WIDTH'], $this->Prop['UPDATE_HEIGHT'], $this->Prop['UPDATE_WIDTH'], $this->Prop['UPDATE_HEIGHT']));
				}

				if ($bPriorityX) {
					return $this->Crop(array($this->Prop['SET_WIDTH'], $this->Prop['SET_HEIGHT']), array(0, 0, 0, (round($this->Prop['UPDATE_HEIGHT'] / 2) - round($this->Prop['SET_HEIGHT'] / 2)), $this->Prop['REAL_WIDTH'], $this->Prop['REAL_HEIGHT'], $this->Prop['REAL_WIDTH'], $this->Prop['REAL_HEIGHT']));
				} else {
					return $this->Crop(array($this->Prop['SET_WIDTH'], $this->Prop['SET_HEIGHT']), array(0, 0, ($this->Prop['UPDATE_WIDTH'] / 2 - $this->Prop['SET_WIDTH'] / 2), 0, $this->Prop['REAL_WIDTH'], $this->Prop['REAL_HEIGHT'], $this->Prop['REAL_WIDTH'], $this->Prop['REAL_HEIGHT']));
				}
			}
		}
	}

	protected function Crop($arPS, $arPar)
	{
		if ($this->Prop['MIME'] == 'image/jpeg') {
			$rCreated[0] = imagecreatefromjpeg($this->Prop['PATH']);
			$rCreated[1] = imagecreatetruecolor($arPS[0], $arPS[1]);
			imagefill($rCreated[1], 0, 0, imagecolorallocatealpha($rCreated[1], 255, 255, 255, 100));
		}
		if ($this->Prop['MIME'] == 'image/gif') {
			$rCreated[0] = imagecreatefromgif($this->Prop['PATH']);
			$rCreated[1] = imagecreatetruecolor($arPS[0], $arPS[1]);

			$rCreatedTransp = imagecolortransparent($rCreated[0]);
			$rCreatedColor = @imagecolorsforindex($rCreated[0], $rCreatedTransp);
			$rCreatedExact = imagecolorexact($rCreated[1], $rCreatedColor['red'], $rCreatedColor['green'], $rCreatedColor['blue']);
			imagefill($rCreated[1], 0, 0, $rCreatedExact);

			imagecolortransparent($rCreated[1], $rCreatedExact);
		}
		if ($this->Prop['MIME'] == 'image/png') {
			$rCreated[0] = imagecreatefrompng($this->Prop['PATH']);
			$rCreated[1] = imagecreatetruecolor($arPS[0], $arPS[1]);
			imagefill($rCreated[1], 0, 0, imagecolorallocatealpha($rCreated[1], 255, 255, 255, 100));
			imagealphablending($rCreated[1], false);
			imagesavealpha($rCreated[1], true);
		}

		imagecopyresampled($rCreated[1], $rCreated[0], $arPar[0], $arPar[1], $arPar[2], $arPar[3], $arPar[4], $arPar[5], $arPar[6], $arPar[7]);

		if ($this->Prop['MIME'] == 'image/jpeg') {
			imagejpeg($rCreated[1], $this->Prop['PATH'], $this->Prop['QUALITY']);
		}
		if ($this->Prop['MIME'] == 'image/gif') {
			imagegif($rCreated[1], $this->Prop['PATH']);
		}
		if ($this->Prop['MIME'] == 'image/png') {
			imagepng($rCreated[1], $this->Prop['PATH']);
		}

		imagedestroy($rCreated[0]);
		clearstatcache();
		$arPhotoProp = @getimagesize($this->Prop['PATH']);
		$this->Prop['UPDATE_WIDTH'] = $arPhotoProp[0];
		$this->Prop['UPDATE_HEIGHT'] = $arPhotoProp[1];

		if ($this->Prop['MARK']) {
			$arWM['RESOURCE'] = imagecreatefrompng($this->Prop['MARK']);
			$arWM['WIDTH'] = imagesx($arWM['RESOURCE']);
			$arWM['HEIGHT'] = imagesy($arWM['RESOURCE']);
			if ($this->Prop['MIME'] == 'image/jpeg') {
				$rImage = imagecreatefromjpeg($this->Prop['PATH']);
			}
			if ($this->Prop['MIME'] == 'image/gif') {
				$rImage = imagecreatefromgif($this->Prop['PATH']);
			}
			if ($this->Prop['MIME'] == 'image/png') {
				$rImage = imagecreatefrompng($this->Prop['PATH']);
			}

			clearstatcache();
			$arSize = getimagesize($this->Prop['PATH']);
			imagecolortransparent($arWM['RESOURCE'], imagecolorat($arWM['RESOURCE'], 0, 0));
			imagecopyresampled($rImage, $arWM['RESOURCE'], ($arSize[0] / 2 - $arWM['WIDTH'] / 2), ($arSize[1] / 2 - $arWM['HEIGHT'] / 2), 0, 0, $arWM['WIDTH'], $arWM['HEIGHT'], $arWM['WIDTH'], $arWM['HEIGHT']);
			if ($this->Prop['MIME'] == 'image/jpeg') {
				imagejpeg($rImage, $this->Prop['PATH'], $this->Prop['QUALITY']);
			}
			if ($this->Prop['MIME'] == 'image/gif') {
				imagegif($rImage, $this->Prop['PATH']);
			}
			if ($this->Prop['MIME'] == 'image/png') {
				imagepng($rImage, $this->Prop['PATH']);
			}
			imagedestroy($rImage);
			imagedestroy($arWM['RESOURCE']);
		}

		return $this->Prop;
	}
}

?>
