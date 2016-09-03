<?
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/lib/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/class/index.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/k2/admin/inc/function.php');
?>
<iframe src="/k2/admin/system/file-manager/manager.php?field=<?=urlencode($_REQUEST['field'])?>" width="100%" height="400" frameborder="0" name="ifr" id="ifr"></iframe>