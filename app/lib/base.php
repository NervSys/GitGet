<?php
/**
 * Git Get
 *
 * Copyright 2019-2020 leo <2579186091@qq.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace app\lib;

use app\lib\enum\enum_err;
use Core\Factory;
use Core\Lib\App;
use Core\Lib\IOUnit;
use Ext\libConfGet;
use Ext\libErrno;
use Ext\libMySQL;
use Ext\libPDO;
use Ext\libRedis;

/**
 * Class base
 *
 * @package app
 */
class base extends Factory
{
    /** @var App $app */
    public App $app;

    /** @var libErrno $errno */
    public libErrno $errno;

    /** @var libConfGet $conf_get */
    public libConfGet $conf_get;

    /** @var libMySQL $mysql */
    public libMySQL $mysql;

    /** @var \Redis $redis */
    public \Redis $redis;

    /** @var string $env */
    public string $env = 'prod';

    /**
     * base constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->app      = App::new();
        $this->errno    = libErrno::new();
        $this->conf_get = libConfGet::new();

        $conf_file = $this->app->root_path . '/conf/prod.ini';

        //判断环境设置
        if (is_file($env_file = realpath($this->app->root_path . '/conf/.env'))) {
            $env = trim(file_get_contents($env_file));

            if (is_file($conf_file = realpath($this->app->root_path . '/conf/' . $env . '.ini'))) {
                $this->env = &$env;
            }
        }

        //加载配置
        $this->conf_get->load($conf_file);

        //初始化配置
        self::init();

        //默认操作成功，具体状态码在业务中修改
        $code = enum_err::get_code(enum_err::SUCCESS);

        $this->errno->set($code['code'], 0, $code['msg']);
    }

    /**
     * 初始化配置
     *
     * @throws \RedisException
     */
    public function init(): void
    {
        $this->mysql = libMySQL::new()->bindPdo(libPDO::new($this->conf_get->use('mysql'))->connect());
        $this->redis = libRedis::new($this->conf_get->use('redis'))->connect();
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

        $this->errno->set($code_arr['code'], 1, $msg);

        IOUnit::new()->output();
        exit;
    }
}