<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

define('PHP_SELF', 'pixmicat.php');
define('DEBUG', TRUE);
$_SERVER['HTTP_HOST'] = '127.0.0.1';


require dirname(__FILE__).'/../../config.php';

require ROOTPATH.'lib/pmclibrary.php';
//require 'vendor/autoload.php';
require_once ROOTPATH.'lib/lib_pms.php';
require ROOTPATH.'lib/lib_compatible.php'; // 引入相容函式庫
require ROOTPATH.'lib/lib_common.php'; // 引入共通函式檔案
