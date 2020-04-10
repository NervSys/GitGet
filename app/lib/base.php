<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2/6/2020
 * Time: 11:44 AM
 * Note: base.php
 */

namespace app\lib;

use app\lib\enum\enum_err;
use app\lib\enum\error_code;
use ext\conf;
use ext\core;
use ext\errno;
use ext\factory;
use ext\mysql;
use ext\pdo;
use ext\redis;

/**
 * Class base
 *
 * @package app
 */
class base extends factory
{
    /** @var \ext\mysql $mysql */
    public $mysql;

    /** @var \Redis $redis */
    public $redis;

    /** @var string $env */
    public $env = 'prod';

    /**
     * base constructor.
     */
    public function __construct()
    {
        //判断环境设置
        if (is_file($env_file = realpath(ROOT . '/conf/.env'))) {
            $env = trim(file_get_contents($env_file));

            if (is_file($conf_file = realpath(ROOT . '/conf/' . $env . '.ini'))) {
                $this->env = &$env;
            }
        }

        //加载配置
        conf::load('conf', $this->env);

        //初始化配置
        self::init();

        //默认操作成功，具体状态码在业务中修改
        $code = enum_err::get_code(enum_err::SUCCESS);
        errno::set($code['code'], 0, $code['msg']);
    }

    /**
     * 初始化配置
     */
    public function init(): void
    {
        $this->mysql = mysql::new()->use_pdo(pdo::create(conf::get('mysql'))->connect());
        $this->redis = redis::create(conf::get('redis'))->connect();
    }

    /**
     * 失败返回
     *
     * @param string $code
     * @param string $msg
     */
    public function fail(string $code, string $msg = '')
    {
        $code_arr = enum_err::get_code($code);
        $msg      = empty($msg) ? $code_arr['msg'] : $msg;
        errno::set($code_arr['code'], 1, $msg);
        core::stop();
    }
}