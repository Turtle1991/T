<?php
/**
 * Created by PhpStorm.
 * User: Turtle
 * Date: 2016/5/23
 * Time: 17:44
 */

error_reporting(7);
ob_start();

// 系统常量
// 系统根目录
define('SYS_ROOT', dirname(dirname(__FILE__)));
// 系统配置文件所在目录
define('SYS_CONFIG', SYS_ROOT . '/config');
// 系统业务逻辑文件目录
define('SYS_SOURCE', SYS_ROOT . '/source');
// 系统库文件目录
define('SYS_LIBRARY', SYS_ROOT . '/library');
// 系统模块所在目录
define('SYS_MODULE', SYS_ROOT . '/module');
// 系统缓存目录
define('SYS_CACHE', SYS_ROOT . '/cache');
// 外部访问目录
define('SYS_WWWROOT', SYS_ROOT . '/wwwroot');
// 系统定时任务所在目录
define('SYS_CRON', SYS_ROOT . '/cron');

include_once SYS_CONFIG . '/config.php';
define('SYS_DEBUG', SYS_ENV_MODE != 'PRO');

function __autoload($clsName)
{
    $clsName = trim($clsName, "\\");

    $clsInfo = explode("\\", $clsName);
    $len = count($clsInfo);
    $clsName = $clsInfo[$len - 1]; // 类名

    if ($len == 1) {
        $clsInfo[0] = SYS_LIBRARY;
    } else {
        $pre = substr($clsName, 0, 3);
        $clsInfo[0] = SYS_MODULE . '/' . strtolower($clsInfo[0]);
        if ($pre == 'mdl') {
            $clsInfo[$len - 1] = 'model';
        } elseif ($pre == 'ctl') {
            $clsInfo[$len - 1] = 'source';
        }
    }
    $base = sprintf('%s/%s.php', strtolower(implode('/', $clsInfo)), $clsName);

    $file = realpath($base);
    if ($file && file_exists($file)) {
        include_once $file;
    } else {

    }
}

spl_autoload_register("__autoload");

function __d($var, $file = '', $split = "\n")
{
    $var = var_export($var, 1);
    $file = preg_replace('/\W+/', '', $file);
    if ($file) {
        // 写入文件
        file_put_contents('/tmp/' . $file, $var, $split, FILE_APPEND);
    } else {
        if (defined('SYS_ENV_MODE') && SYS_ENV_MODE != 'PRO') {
            // 非线上
            echo "<pre>" . $var . "</pre>";
        }
    }
}














