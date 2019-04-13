<?php

/**
 * FileIO Normal 本機儲存 API (With IFS 索引快取)
 *
 * 以本機硬碟空間作為圖檔儲存的方式，並提供一套方法供程式管理圖片
 *
 * @package PMCLibrary
 * @version $Id$
 * @date $Date$
 */

use Yonkoma\Helper;

class FileIOnormal extends AbstractIfsFileIO {
    var $imgPath, $thumbPath;

    public function __construct($parameter, $ENV) {
        parent::__construct($parameter, $ENV);

        $this->imgPath = $ENV['IMG'];
        $this->thumbPath = $ENV['THUMB'];
    }

    public function init() {
        return true;
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
                // 無法刪除，檔案消失 (更新索引)
            }
            $this->IFS->delRecord($i);
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
//        return (strpos($imgname, 's.') !== false ? $this->thumbPath : $this->imgPath) . $imgname;
    }

    public function uploadImage($board, $imgname, $imgpath, $imgsize) {
        $this->IFS->addRecord($board, $imgname, $imgsize, ''); // 加入索引之中
    }

    public function getImageURL($board, $imgname) {
        return $this->getImageLocalURL($board, $imgname);
    }
}