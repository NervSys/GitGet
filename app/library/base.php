<?php
/**
 * Git Remote Deploy
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

namespace app\library;

use app\enum\error_enum;
use app\library\enum\cache_key;
use ext\conf;
use ext\core;
use ext\crypt;
use ext\errno;
use ext\factory;
use ext\mysql;
use ext\pdo;
use ext\queue;
use ext\redis;

class base extends factory
{
    public    $user_id;
    protected $check_token = true;
    protected $redis       = null;
    protected $crypt       = null;
    protected $mysql       = null;
    protected $queue       = null;

    public function __construct()
    {
		$this->mysql = mysql::new()->use_pdo(pdo::create(conf::get('mysql'))->connect());
        $this->redis = redis::create(conf::get('redis'))->connect();
        $this->queue = queue::new()->use_redis($this->redis)->set_name('gitRemoteDeploy');
        is_null($this->crypt) && $this->crypt = crypt::new();
        if ($this->check_token) {
            if (empty($_COOKIE['gg_token'])) {
                return $this->response(error_enum::TOKEN_MUST_EXIST);
            }
            $token = $this->parse($_COOKIE['gg_token']);
            if (empty($token['data']['user_id']) || empty($token['data']['expire']) || $token['data']['expire'] < time()) {
                return $this->response(error_enum::TOKEN_ERROR);
            }
            $this->user_id = $token['data']['user_id'];
        }
    }

    public function make($data)
    {
        if (!isset($data['user_id'])) {
            throw new \Exception('Missing "user_id" in token data!');
        }

        $token = $this->crypt->sign(json_encode($data));
        //$this->redis->set('gg_tk:' . $data['user_id'], hash('md5', $token), 86400 * 7);
        return $token;
    }

    public function parse(string $token): array
    {
        $json = $this->crypt->verify($token);
        if ('' === $json) {
            return [
                'sso'  => -1,
                'data' => []
            ];
        }
        $data = json_decode($json, true);
        if (!is_array($data) || !isset($data['user_id'])) {
            return [
                'sso'  => -1,
                'data' => []
            ];
        }
        //$token_key  = 'gg_tk:' . $data['user_id'];
        //$token_hash = $this->redis->get($token_key);
        //if (false === $token_hash) {
        //    return [
        //        'sso'  => -2,
        //        'data' => []
        //    ];
        //}
        //if ($token_hash !== hash('md5', $token)) {
        //    return [
        //        'sso'  => -3,
        //        'data' => []
        //    ];
        //}
        //$this->redis->expire($token_key, 86400 * 7);
        $this->user_id = (int)$data['user_id'];
        return [
            'sso'  => 0,
            'data' => &$data
        ];
    }

    public function succeed(array $data = [])
    {
        errno::set(error_enum::OK, 0, error_enum::$table[error_enum::OK]);
        return $data;
    }

    public function response(int $code)
    {
        errno::set($code, 1, error_enum::$table[$code] ?? '未知提示');
        core::stop();
    }
}