<?

class Cache
{
    function Get($nTime = 0, $sKey)
    {
        $this->Time = $nTime;
        $this->Key = $sKey;
        $this->MD5Key = md5($sKey);
        $this->Dir = '/k2/cache/'.substr($this->MD5Key, 0, 2).'/';
        $this->File = $this->Dir.$this->MD5Key.'.php';

        if ($nTime > 1 && file_exists($_SERVER['DOCUMENT_ROOT'].$this->File)) {
            if (filemtime($_SERVER['DOCUMENT_ROOT'].$this->File) + $nTime > time()) {
                $arCache = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->File));
                $nPage = 1;
                if ($_GET['page'] && ($_GET['page'] > 1) && ($_GET['page'] <= $arCache['P'])) {
                    $nPage = (int)$_GET['page'];
                }
                $sPageCache = $_SERVER['DOCUMENT_ROOT'].$this->Dir.$this->MD5Key.'_'.$nPage.'.html';
                if (file_exists($sPageCache)) {
                    readfile($sPageCache);

                    return false;
                }
            } else {
                $this->Delete($this->Key);
            }
        }
        ob_start();

        return true;
    }

    function Save()
    {
        global $LIB;

        $sCont = ob_get_contents();
        ob_end_clean();

        $arCache['T'] = $this->Time;
        $arCache['P'] = $LIB['NAV']->Setting['PAGES'];

        if ($this->Time > 1 && $LIB['FILE']->Create($this->File, serialize($arCache))) {
            $nPage = 1;
            if ($_GET['page'] > 1 && $_GET['page'] <= $arCache['P']) {
                $nPage = (int)$_GET['page'];
            }
            $LIB['FILE']->Create($this->Dir.$this->MD5Key.'_'.$nPage.'.html', $sCont);
        }
        echo $sCont;
    }

    function Delete($sKey)
    {
        $sKey = md5($sKey);
        $sDir = '/k2/cache/'.substr($sKey, 0, 2).'/';
        $sFile = $sDir.$sKey.'.php';
        if (file_exists($_SERVER['DOCUMENT_ROOT'].$sFile)) {
            $arCache = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'].$sFile));
            $nPage = ($arCache['P'] ? $arCache['P'] : 1);
            for ($i = 0; $i < $nPage; $i++) {
                @unlink($_SERVER['DOCUMENT_ROOT'].$sDir.$sKey.'_'.($i + 1).'.html');
            }

            return @unlink($_SERVER['DOCUMENT_ROOT'].$sFile);
        }

        return true;
    }

    function GetVar($nTime = 0, $sKey)
    {
        $this->Time = $nTime;
        $this->Key = 'key_'.$sKey;
        $this->MD5Key = md5($sKey);
        $this->Dir = '/k2/cache/'.substr($this->MD5Key, 0, 2).'/';
        $this->File = $this->Dir.$this->MD5Key.'.php';

        if ($nTime > 1 && file_exists($_SERVER['DOCUMENT_ROOT'].$this->File)) {
            if (filemtime($_SERVER['DOCUMENT_ROOT'].$this->File) + $nTime > time()) {
                $arCache = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->File));

                return $arCache['CODE'];
            } else {
                $this->Delete($this->Key);
            }
        }

        return false;
    }

    function SaveVar($arCode)
    {
        global $LIB;

        $arCache['T'] = $this->Time;
        $arCache['CODE'] = $arCode;

        if ($this->Time > 1 && $LIB['FILE']->Create($this->File, serialize($arCache))) {
            return true;
        }

        return false;
    }

    function Clear()
    {
        $sPath = $_SERVER['DOCUMENT_ROOT'].'/k2/cache/';
        $arFile = dirList($sPath);
        for ($i = 0; $i < count($arFile); $i++) {
            if (preg_match("#.+/(.+?)\.php$#", $arFile[$i], $arMath)) {
                $arCache = unserialize(file_get_contents($sPath.$arFile[$i]));
                if (filemtime($sPath.$arFile[$i]) + $arCache['T'] < time()) {
                    if (!$arCache['P']) {
                        $arCache['P'] = 1;
                    }
                    for ($j = 0; $j < $arCache['P']; $j++) {
                        $sSubDir = substr($arMath[1], 0, 2);
                        @unlink($sPath.$sSubDir.'/'.$arMath[1].'_'.($j + 1).'.html');
                        if (!@unlink($sPath.$arFile[$i])) {
                            $bBadDelete = true;
                        }
                        @rmdir($sPath.$sSubDir);
                    }
                }
            }
        }

        return (bool)$bBadDelete;
    }

    function ClearAll()
    {
        return dirClear($_SERVER['DOCUMENT_ROOT'].'/k2/cache/');
    }
}

class CacheMemcache
{
    function __construct()
    {
        $this->Memcache = new Memcache;
        $this->Memcache->connect(CACHE_MEMCACHE_SERVER, CACHE_MEMCACHE_PORT);
    }

    function Get($sKey)
    {
        if ($arDate = $this->Memcache->get(serialize($sKey))) {
            return unserialize($arDate);
        }
    }

    function Set($sKey, $arData, $nTime = 1000)
    {
        $this->Memcache->set(serialize($sKey), serialize($arData), 0, $nTime);
    }

    function Delete($sKey)
    {
        $this->Memcache->delete();
    }

    function Replace()
    {
        $this->Memcache->replace();
    }

    function Clear()
    {
        $this->Memcache->flush();
    }
}

?>