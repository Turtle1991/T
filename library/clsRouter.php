<?php
/**
 * Created by PhpStorm.
 * User: Turtle
 * Date: 2016/5/24
 * Time: 10:00
 */
class clsRouter
{
    /**
     * 项目
     * @var string
     */
    public $entry = '';

    /**
     * 模块
     * @var string
     */
    public $module = '';

    /**
     * 方法
     * @var string
     */
    public $method = '';

    public function __construct()
    {
        $this->_rewrite($_GET['_RW_']);
        unset($_GET['_RW_']);
    }

    /**
     * 初始化路由
     * @param $url
     * @return bool
     */
    private function _rewrite($url)
    {
        if ($url{0} == '/') {
            $url = substr($url, 1);
        }
        $data = explode('/', $url);

        $len = count($data);
        if ($len == 0) {
            return $this->_halt(95, 'Invalid url!');
        }

        //强制不缓存
        if (isset($data[0]) && $data[0] != 'resource') {
            header("Cache-Control: no-cache, private, max-age=0");
            header("Expires: Thu, 02 Apr 2009 05:14:08 GMT");
        }

        $this->entry = $this->_filter($data[0]);
        $module = isset($data[1]) ? $data[1] : 'index';
        $m = explode('-', $module);
        $this->module = $this->_filter($m[0]);
        $len = count($m);
        if ($len % 2 == 0) {
            $this->method = $this->_filter($m[1]);
            unset($m[1]);
        } else {
            $this->method = '';
        }
        unset($m[0]);
        $m = array_values($m);
        $len = count($m);
        for ($i = 0; $i < $len; $i += 2) {
            $_GET[$m[$i]] = $m[$i + 1];
            $_REQUEST[$m[$i]] = $m[$i + 1];
        }
    }

    /**
     * 解析路由
     * @return bool
     */
    public function parse()
    {
        $moduleName = '';
        $base = SYS_MODULE . "/{$this->entry}";

        if (!file_exists($base)) {
            return $this->_halt(99, 'Invalid entry!');
        }

        $moduleName .= "\\{$this->entry}";
        $comFile = "{$base}/common.php"; //对应项目的单独配置文件
        empty($this->module) && $this->module = 'index';
        empty($this->method) && $this->method = 'index';

        $ctlModule = "ctl" . ucfirst($this->module);
        $ctlModuleFile = "{$base}/{$ctlModule}.php";
        if (!is_file($ctlModuleFile)) {
            return $this->_halt(98, 'Invalid module!');
        }

        $moduleName .= "\\{$ctlModule}";

        if (!is_file($comFile)) {
            require_once $comFile;
        }
        require_once $ctlModuleFile;

        if (!class_exists($moduleName, FALSE)) {
            return $this->_halt(97, 'Invalid module!');
        }

        $control = new $moduleName();

        $methods = [];
        $methods[] = [
            $this->method,
            'func' . $this->method,
            'index',
            'funcIndex'
        ];

        $method = NULL;

        foreach ($methods as $m) {
            if (method_exists($control, $m)) {
                $method = $m;
                break;
            }
        }
        if (empty($method)) {
            return $this->_halt(96, 'Invalid method!');
        }

        $control->$method();

        $control = NULL;
        unset($control);
        return TRUE;
    }

    /**
     * 过滤参数非法字符
     * @param string $param
     * @param bool $require
     * @return string
     */
    private function _filter($param, $require = TRUE)
    {
        $param = preg_replace('/^\W$/is', '', $param);
        if (!$param && $require) {
            $param = 'index';
        }
        return $param;
    }

    /**
     * @param $code
     * @param $message
     * @return bool
     */
    private function _halt($code, $message)
    {
        die("code:{$code}, mes:{$message}");
    }
}