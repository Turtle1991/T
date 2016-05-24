<?php

/**
 * Created by PhpStorm.
 * User: Turtle
 * Date: 2016/5/24
 * Time: 10:00
 */
class clsRouter
{
    public $entry = '';

    public $module = '';

    public $method = '';

    public function __construct()
    {

    }

    private function _rewrite($url)
    {
        if ($url{0} == '/') {
            $url = substr($url, 1);
        }
        $data = explode('/', $url);
        //强制不缓存
        if (isset($data[0]) && $data[0] != 'resource') {
            header("Cache-Control: no-cache, private, max-age=0");
            header("Expires: Thu, 02 Apr 2009 05:14:08 GMT");
        }

    }

    public function parse()
    {

    }

    private function _filter($param, $require = TRUE)
    {
        $param = preg_replace('/^\W$/is', '', $param);
        if (!$param && $require) {
            $param = 'index';
        }
        return $param;
    }
}