<?php

/**
 * FileIO Normal 本機儲存 API
 *
 * 以本機硬碟空間作為圖檔儲存的方式，並提供一套方法供程式管理圖片
 *
 * @package PMCLibrary
 * @version $Id$
 * @date $Date$
 */

use Yonkoma\Helper;

class FileIOnormal extends AbstractFileIO {
    var $imgPath, $thumbPath;

    public function __construct($parameter, $ENV) {
        parent::__construct($parameter, $ENV);
        $this->imgPath = $ENV['IMG'];
        $this->thumbPath = $ENV['THUMB'];
    }

    public function init() {
        return true;
    }

    public function imageExists($board, $imgname) {
        return file_exists($this->getImagePhysicalPath($board, $imgname));
    }

    public function deleteImage($board, $imgname) {
        if (!is_array($imgname)) {
            $imgname = array($imgname); // 單一名稱參數
        }

        $size = 0;
        $size_perimg = 0;
        foreach ($imgname as $i) {
            $size_perimg = $this->getImageFilesize($board, $i);
            // 刪除出現錯誤
            if (!@unlink($this->getImagePhysicalPath($board, $i))) {
                if ($this->imageExists($board, $i)) {
                    continue; // 無法刪除，檔案存在 (保留索引)
                }
            }
            $size += $size_perimg;
        }
        return $size;
    }

    /**
     * 取得圖檔的真實位置。
     */
    private function getImagePhysicalPath($board, $imgname) {
        return Helper\path_join(
            'boards',
            $board,
            (strpos($imgname, 's.') !== false ? $this->thumbPath : $this->imgPath),
            $imgname
        );
    }

    public function uploadImage($board, $imgname, $imgpath, $imgsize) {
        return false;
    }

    public function getImageFilesize($board, $imgname) {
        $size = filesize($this->getImagePhysicalPath($board, $imgname));
        if ($size === false) {
            $size = 0;
        }
        return $size;
    }

    public function getImageURL($board, $imgname) {
        return $this->getImageLocalURL($board, $imgname);
    }

    public function resolveThumbName($board, $thumbPattern) {
        $shortcut = $this->resolveThumbNameShortcut($board, $thumbPattern);
        if ($shortcut !== false) {
            return $shortcut;
        }
        $find = glob($this->thumbPath . $thumbPattern . 's.*');
        return ($find !== false && count($find) != 0) ? basename($find[0]) : false;
    }
    /**
     * 用傳統的 1234567890123s.jpg 規則嘗試尋找預覽圖，運氣好的話只需要找一次。
     *
     * @param string $thumbPattern 預覽圖檔名
     * @return bool 是否找到
     */
    private function resolveThumbNameShortcut($board, $thumbPattern) {
        $shortcutFind = $this->getImagePhysicalPath($board, $thumbPattern . 's.jpg');
        if (file_exists($shortcutFind)) {
            return basename($shortcutFind);
        } else {
            return false;
        }
    }
}