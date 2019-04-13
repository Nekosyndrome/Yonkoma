<?php
/**
 * Pixmicat! interface declarations
 *
 * @package PMCLibrary
 * @version $Id$
 */


/**
 * IFileIO
 */
interface IFileIO {
    /**
     * 建置初始化。通常在安裝時做一次即可。
     */
    function init();

    /**
     * 圖檔是否存在。
     *
	 * @param string $board   版面名稱
     * @param string $imgname 圖檔名稱
     * @return bool 是否存在
     */
    function imageExists($board, $imgname);

    /**
     * 刪除圖片。
     *
	 * @param string $board   版面名稱
     * @param string $imgname 圖檔名稱
     */
    function deleteImage($board, $imgname);

    /**
     * 上傳圖片。
     *
	 * @param string $board   版面名稱
     * @param string $imgname 圖檔名稱
     * @param string $imgpath 圖檔路徑
     * @param int $imgsize 圖檔檔案大小 (byte)
     */
    function uploadImage($board, $imgname, $imgpath, $imgsize);

    /**
     * 取得圖檔檔案大小。
     *
	 * @param string $board   版面名稱
     * @param string $imgname 圖檔名稱
     * @return mixed 檔案大小 (byte) 或 0 (失敗時)
     */
    function getImageFilesize($board, $imgname);

    /**
     *　取得圖檔的 URL 以便 &lt;img&gt; 標籤顯示圖片。
     *
	 * @param string $board   版面名稱
     * @param string $imgname 圖檔名稱
     * @return string 圖檔 URL
     */
    function getImageURL($board, $imgname);

    /**
     * 取得預覽圖檔名。
     *
	 * @param string $board
     * @param string $thumbPattern 預覽圖檔名格式
     * @return string 預覽圖檔名
     */
    function resolveThumbName($board, $thumbPattern);
}

/**
 * IPIOCondition
 */
interface IPIOCondition {
	/**
	 * 檢查是否需要進行檢查步驟。
	 *
	 * @param  string $type  目前模式 ("predict" 預知提醒、"delete" 真正刪除)
	 * @param  mixed  $limit 判斷機制上限參數
	 * @return boolean       是否需要進行進一步檢查
	 */
	public static function check($type, $limit);

	/**
	 * 列出需要刪除的文章編號列表。
	 *
	 * @param  string $type  目前模式 ("predict" 預知提醒、"delete" 真正刪除)
	 * @param  mixed  $limit 判斷機制上限參數
	 * @return array         文章編號列表陣列
	 */
	public static function listee($type, $limit);

	/**
	 * 輸出 Condition 物件資訊。
	 *
	 * @param  mixed  $limit 判斷機制上限參數
	 * @return string        物件資訊文字
	 */
	public static function info($limit);
}

/**
 * ILogger
 */
interface ILogger {
	/**
	 * 建構元。
	 *
	 * @param string $logName Logger 名稱
	 * @param string $logFile 記錄檔案位置
	 */
	public function __construct($logName, $logFile);
	/**
	 * 檢查是否 logger 要記錄 DEBUG 等級。
	 *
	 * @return boolean 要記錄 DEBUG 等級與否
	 */
	public function isDebugEnabled();

	/**
	 * 檢查是否 logger 要記錄 INFO 等級。
	 *
	 * @return boolean 要記錄 INFO 等級與否
	 */
	public function isInfoEnabled();

	/**
	 * 檢查是否 logger 要記錄 ERROR 等級。
	 *
	 * @return boolean 要記錄 ERROR 等級與否
	 */
	public function isErrorEnabled();

	/**
	 * 以 DEBUG 等級記錄訊息。
	 *
	 * @param string $format 格式化訊息內容
	 * @param mixed $varargs 參數
	 */
	public function debug($format, $varargs = '');

	/**
	 * 以 INFO 等級記錄訊息。
	 *
	 * @param string $format 格式化訊息內容
	 * @param mixed $varargs 參數
	 */
	public function info($format, $varargs = '');

	/**
	 * 以 ERROR 等級記錄訊息。
	 *
	 * @param string $format 格式化訊息內容
	 * @param mixed $varargs 參數
	 */
	public function error($format, $varargs = '');
}

/**
 * MethodInterceptor (AOP Around Advice)
 */
interface MethodInterceptor {
	/**
	 * 代理呼叫方法。
	 *
	 * @param  array  $callable 要被呼叫的方法
	 * @param  array  $args     方法傳遞的參數
	 * @return mixed            方法執行的結果
	 */
	public function invoke(array $callable, array $args);
}

/**
 * IModule
 */
interface IModule {
	/**
	 * 回傳模組名稱方法
	 *
	 * @return string 模組名稱。建議回傳格式: mod_xxx : 簡短註解
	 */
	public function getModuleName();

	/**
	 * 回傳模組版本號方法
	 *
	 * @return string 模組版本號
	 */
	public function getModuleVersionInfo();
}