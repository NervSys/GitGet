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

use app\library\base;
use app\model\server;
use app\model\system_setting;

class show extends base
{
    public $tz = '*';

    /**
     * 服务器列表
     *
     * @param int $page
     * @param int $page_size
     *
     * @return array
     * @throws \Exception
     */
    public function srv_list(int $page = 1, int $page_size = 10)
    {
        $srv_list = server::new()->where(['status', 1])->get_page($page, $page_size);
        foreach ($srv_list['list'] as &$srv) {
            $srv['operate'] = '<a style="text-decoration:none" class="ml-5 btn btn-xs btn-primary" onClick="info_edit(\'编辑\', \'./serv_edit.php?uid=' . $srv['srv_id'] . '\', 1300)" href="javascript:;" title="编辑">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="suoding mar-R btn btn-xs btn-danger" onclick="info_del(this, ' . $srv['srv_id'] . ')" href="javascript:;" title="删除">删除</a>';
        }
        return $this->succeed($srv_list);
    }


    /**
     * 获取服务器信息
     *
     * @param int $srv_id
     *
     * @return array
     */
    public function serv_detail(int $srv_id)
    {
        return $this->succeed(server::new()->where([['srv_id', $srv_id], ['status', 1]])->get_one());
    }

    public function system_setting()
    {
        $setting  = system_setting::new()->get();
        $key      = array_column($setting, 'key');
        $value    = array_column($setting, 'value');
        $settings = array_combine($key, $value);
        return $this->succeed($settings);
    }
}