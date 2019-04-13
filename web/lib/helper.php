<?php
/**
 * 這邊的共用函式預設會直接被 include 到 Helper namespace 底下
 * 詳見 composer.json 的設定
 */

namespace Yonkoma\Helper;


function uri_segment(int $index = 0)
{
    global $config;
    static $skip = null;
    static $uriSegments = null;

    if ($uriSegments===null) {
        $skip = $config['url']['skip_segments'];
        $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        // 第一個 segment 為 script 名稱的情況
        if ($skip+1 < count($uriSegments) && $uriSegments[$skip+1] == $config['url']['script']) {
            $skip++;
        }
    }

    if ($index < 0 || $index+$skip >= count($uriSegments)) {
        return null;
    }
    //var_dump($skip);
    //var_dump($uriSegments);
    return $uriSegments[$index+$skip];
}

function current_board()
{
    global $config;
    $board = uri_segment(1);
    if ($board===null || isset($config['boards'][$board])) {
        return $board;
    }
    return null;
}

function current_thread()
{
    if (current_board()===null) {
        return null;
    }
    return uri_segment(2);
}

function path_join()
{
    $args = func_get_args();
    $paths = array();
    foreach ($args as $arg) {
        $paths = array_merge($paths, (array)$arg);
    }

    $prefix = '';
    if (count($args)>0 && is_string($args[0][0]) && strpos($args[0], '/')===0) {
        $prefix = '/';
    }
    $paths = array_map(create_function('$p', 'return trim($p, "/");'), $paths);
    $paths = array_filter($paths);
    return $prefix. join('/', $paths);
}

function anchor()
{
    global $config;
    return call_user_func_array(
        'Yonkoma\Helper\path_join',
        array_merge(['/', $config['url']['base']], func_get_args())
    );
}
