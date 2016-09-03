<?

class PhotoIM
{
	function Resize($arPar)
	{
		$im = new imagick($arPar['PATH']);
		if ($arPar['FIX']) {
			$im->cropThumbnailImage($arPar['SET_WIDTH'], $arPar['SET_HEIGHT']);
			$im->setImagePage(0, 0, 0, 0);
		} else {
			$im->thumbnailImage($arPar['SET_WIDTH'], $arPar['SET_HEIGHT'], true);
		}
		if ($arPar['MARK']) {
			$wm = new imagick($arPar['MARK']);
			$im->setImageColorspace($wm->getImageColorspace());
			$arSize = $wm->getImageGeometry();
			$im->compositeImage($wm, $wm->getImageCompose(), ($arPar['SET_WIDTH'] - $arSize['width']), ($arPar['SET_HEIGHT'] - $arSize['height']));
		}
		if ($im->writeImage($arPar['PATH'])) {
			$arSize = $im->getImageGeometry();
			$im->destroy();
			$arPar['UPDATE_WIDTH'] = $arSize['width'];
			$arPar['UPDATE_HEIGHT'] = $arSize['height'];

			return $arPar;
		}
		$im->destroy();

		return false;
	}
}

?>
