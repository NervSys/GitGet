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

namespace app\server;

use app\enum\error_enum;
use app\library\base;
use app\library\model;
use app\model\server;
use app\model\system_setting;
use ext\http;

class ctrl extends base
{
    public $tz = '*';

    /**
     * 新增或编辑服务器
     *
     * @param string $srv_ip
     * @param string $srv_name
     * @param int    $srv_port
     * @param int    $srv_id
     *
     * @return array
     */
    public function addOrEdit(string $srv_ip, string $srv_name = '', int $srv_port = 80, int $srv_id = 0): array
    {
        $data = [
            'ip'       => $srv_ip,
            'port'     => $srv_port,
            'srv_name' => $srv_name,
        ];
        model::new()->begin();
        try {
            if ($srv_id == 0) {
                server::new()->value($data)->insert_data();
            } else {
                server::new()->value($data)->where(['srv_id', $srv_id])->update_data();
            }
            model::new()->commit();
        } catch (\PDOException $e) {
            model::new()->rollback();
            return $this->response(error_enum::SQL_ERROR);
        }
        return $this->succeed();
    }

    /**
     * 删除服务器
     *
     * @param int $srv_id 服务器id
     *
     * @return array
     */
    public function delete_serv(int $srv_id): array
    {
        $res = server::new()->where(['srv_id', $srv_id])->value(['status' => 2])->update_data();
        return $this->succeed();
    }

    public function system_setting(string $key, string $value)
    {
        $setting = [
            'key'   => $key,
            'value' => $value,
        ];
        if (system_setting::new()->where(['key', $key])->exist()) {
            system_setting::new()->value($setting)->where(['key', $key])->update_data();
        } else {
            system_setting::new()->value($setting)->insert_data();
        }
        $servers = server::new()->get();
        $data    = [
            'cmd'   => 'project/proj_git-setting_receive',
            'key'   => $key,
            'value' => $value
        ];
        foreach ($servers as $server) {
            $ip   = $server['ip'];
            $port = $server['port'];
            $url  = "http://" . $ip . ":" . $port . "/api.php";
            http::new()->add(['url' => $url, 'data' => $data])->fetch();
        }
        return $this->succeed();
    }
}