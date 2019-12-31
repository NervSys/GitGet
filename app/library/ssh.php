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

class ssh
{
    private        $host     = '127.0.0.1';
    private        $user     = 'test';
    private        $password = 'test';
    private        $port     = 22;
    private static $connect;

    public function __construct(array $config = [])
    {
        if (!extension_loaded("ssh2")) {
            die("needs ssh2 extension!");
        }
        if (!empty($config)) {
            $this->host     = $config['host'];
            $this->user     = $config['user'];
            $this->password = $config['password'];
            $this->port     = $config['port'];
        };
        $this->connect();
    }

    private function connect()
    {
        self::$connect = ssh2_connect($this->host, $this->port);
        $isAuth        = ssh2_auth_password(self::$connect, $this->user, $this->password);
        if (!$isAuth) {
            die('connect fail');
        }

    }

    // $cmd="cd /tmp; git pull;ls -a;";
    public function exec($cmd): bool
    {
        $ret = ssh2_exec(self::$connect, $cmd);
        stream_set_blocking($ret, true);
        return stream_get_contents($ret);
    }

}