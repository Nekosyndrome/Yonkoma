<?php

/*
  FileIO - Pixmicat! File I/O
  FileIO Kernel Switcher
 */

/**
 * 抽象 FileIO，預先實作好本地圖檔相關方法。
 */

use Yonkoma\Helper;

abstract class AbstractFileIO implements IFileIO {

    /** @var ILogger */
    var $LOG;

    /**
     * 伺服器絕對位置
     *
     * @var string
     */
    private $absoluteUrl;

    /**
     * 圖檔總容量快取檔案位置
     *
     * @var string
     */
    private $cacheFile;

    public function __construct() {
        $this->LOG = PMCLibrary::getLoggerInstance('AbstractFileIO');
        $this->absoluteUrl = $this->getAbsoluteUrl();
        $this->cacheFile = $this->getCacheFile();
    }

    private function getAbsoluteUrl() {
        $phpSelf = $_SERVER['PHP_SELF'];
        return sprintf(
                '//%s%s', $_SERVER['HTTP_HOST'], substr($phpSelf, 0, strpos($phpSelf, PHP_SELF))
        );
    }

    private function getCacheFile() {
        return STORAGE_PATH . 'sizecache.dat';
    }

    protected function getImageLocalURL($board, $imgname) {
        return Helper\anchor(
            $board,
            (strpos($imgname, 's.') !== false ? basename(THUMB_DIR) : basename(IMG_DIR)),
            $imgname
        );
    }

    protected function remoteImageExists($img) {
        try {
            $result = file_get_contents($img, false, null, 0, 1);
        } catch (Exception $ignored) {
            $this->LOG->error("remoteImageExists -> file_get_contents failed");
            return false;
        }

        return ($result !== false);
    }
}

// 引入實作
require ROOTPATH . 'lib/fileio/fileio.' . FILEIO_BACKEND . '.php';
