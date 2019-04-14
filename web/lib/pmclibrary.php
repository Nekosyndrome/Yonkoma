<?php
/**
 * Pixmicat! Library Singleton Factory
 *
 * 集中函式庫以方便呼叫，並可回傳單例物件。
 *
 * @package PMCLibrary
 * @version $Id$
 * @since 7th.Release
 */

require ROOTPATH.'lib/interfaces.php';
require ROOTPATH.'lib/lib_simplelogger.php';
require ROOTPATH.'lib/lib_loggerinterceptor.php';

use Yonkoma\Helper;
class PMCLibrary {
	/**
	 * 取得 PIO 函式庫物件
	 *
	 * @return IPIO PIO 函式庫物件
	 */
	public static function getPIOInstance($board = null) {
		global $PIOEnv;
		global $config;
		static $instPIO = [];
		if (!$board) {
			$board = Helper\current_board();
		}

		$index = $board ? $board : '__share_db';
		if (!isset($instPIO[$index])) {
			require_once ROOTPATH.'lib/lib_pio.php';
			$pioExactClass = '\\Yonkoma\\Database\\Implementation\\'. ucfirst($config['db']['type']);
			$instPIO[$index] = new LoggerInjector(
				new $pioExactClass($config['db'], $board, $PIOEnv),
				new LoggerInterceptor(PMCLibrary::getLoggerInstance($pioExactClass))
			);
		}
		return $instPIO[$index];
	}

	/**
	 * 取得 PMS 函式庫物件
	 *
	 * @return PMS PMS 函式庫物件
	 */
	public static function getPMSInstance() {
		global $ModuleList;
		static $instPMS = null;
		if ($instPMS == null) {
			require_once ROOTPATH.'lib/lib_pms.php';
			$instPMS = new PMS(array( // PMS 環境常數
				'MODULE.PATH' => ROOTPATH.'module/',
				'MODULE.PAGE' => PHP_SELF.'?mode=module&amp;load=',
				'MODULE.LOADLIST' => $ModuleList
			));
		}
		return $instPMS;
	}

	/**
	 * 取得 FileIO 函式庫物件
	 *
	 * @return IFileIO FileIO 函式庫物件
	 */
	public static function getFileIOInstance() {
		static $instFileIO = null;
		if ($instFileIO == null) {
			require ROOTPATH.'lib/lib_fileio.php';
                        $fileIoExactClass = 'FileIO'.FILEIO_BACKEND;
			$instFileIO = new $fileIoExactClass(
                                unserialize(FILEIO_PARAMETER),
				array( // FileIO 環境常數
					'IMG' => IMG_DIR,
					'THUMB' => THUMB_DIR
				)
			);
		}
		return $instFileIO;
	}

	/**
	 * 取得 Logger 函式庫物件
	 *
	 * @param string $name 識別名稱
	 * @return ILogger Logger 函式庫物件
	 */
	public static function getLoggerInstance($name = 'Global') {
		static $instLogger = array();
		if (!array_key_exists($name, $instLogger)) {
			$instLogger[$name] = new SimpleLogger($name, STORAGE_PATH .'error.log');
		}
		return $instLogger[$name];
	}

	/**
	 * 取得語言函式庫物件
	 *
	 * @return LanguageLoader Language 函式庫物件
	 */
	public static function getLanguageInstance() {
		static $instLanguage = null;
		if ($instLanguage == null) {
			require ROOTPATH.'lib/lib_language.php';
			$instLanguage = LanguageLoader::getInstance();
		}
		return $instLanguage;
	}
}
