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
namespace app\project;


use app\lib\api;
use app\lib\model\branch;
use app\lib\model\proj;
use app\lib\model\proj_log;
use app\lib\model\svr as mod_svr;

class project extends api
{
    /**
     * 项目列表
     *
     * @param int $page
     * @param int $page_size
     *
     * @return array
     */
    public function list(int $page, int $page_size)
    {
        $res = proj::new()->fields('id', 'name', 'status', 'is_lock')->where([['status', '<>', 2]])->get_page($page, $page_size);
        foreach ($res['list'] as &$item) {
            $branch         = branch::new()->where([['proj_id', $item['id']], ['active', 1]])->fields('id', 'name')->get_one();
            $item['branch'] = $branch['name'];
            $item['commit'] = proj_log::new()->where([['proj_id', $item['id']], ['branch_id', $branch['id']], ['active', 1]])->fields('log')->get_val();
        }
        return $res;
    }

    /**
     * 项目信息
     *
     * @param int $id
     *
     * @return array
     */
    public function info(int $id): array
    {
        $proj_info                 = proj::new()->where(['id', $id])->get_one();
        $svr_list                  = !empty($proj_info['svr_list']) ? json_decode($proj_info['svr_list'], true) : [];
        $proj_info['backup_files'] = !empty($proj_info['backup_files']) ? json_decode($proj_info['backup_files'], true) : [];
        $all_svr                   = mod_svr::new()->where(['status', 1])->get();
        foreach ($all_svr as &$svr) {
            $svr['is_check'] = 0;
            if (in_array($svr['id'], $svr_list)) {
                $svr['is_check'] = 1;
            }
        }
        $proj_info['svr_list'] = $all_svr;
        return $proj_info;
    }

    /**
     * 新增编辑
     *
     * @param string $name
     * @param string $desc
     * @param string $git_url
     * @param string $local_path
     * @param array  $svr_ids
     * @param array  $backup_files
     * @param int    $id
     *
     * @return mixed
     */
    public function edit(string $name, string $desc, string $git_url, string $local_path, array $svr_ids, array $backup_files = [], int $id = 0)
    {
        $data = [
            'name'         => $name,
            'desc'         => $desc,
            'git_url'      => $git_url,
            'local_path'   => $local_path,
            'svr_list'     => json_encode($svr_ids),
            'backup_files' => json_encode($backup_files),
        ];
        if ($id == 0) {
            proj::new()->value($data)->add();
            $proj_id = proj::new()->get_last_insert_id();
            $data    = [
                'name'    => 'master',
                'proj_id' => (int)$proj_id,
                'active'  => 1,
            ];
            branch::new()->value($data)->add();
        } else {
            proj::new()->value($data)->where(['id', $id])->save();
        }
        return true;
    }

    /**
     * 删除项目（软删除）
     *
     * @param int $proj_id
     *
     * @return array
     */
    public function del(int $proj_id): array
    {
        return proj::new()->value(['status' => 2])->where(['id', $proj_id])->save();
    }

    /**
     * 项目状态
     *
     * @param int $proj_id
     *
     * @return mixed
     */
    public function proj_status(int $proj_id)
    {
        $key       = "proj_lock:" . $proj_id;
        $status    = $this->redis->get($key);
        $res       = ['status' => (int)$status, 'msg' => ''];
        $error_key = 'gg_error:' . $proj_id;
        $error     = $this->redis->get($error_key);
        if (!empty($error)) {
            $res['msg'] = $error;
            $this->redis->del($key);
            $this->redis->del($error_key);
        }
        return $res;
    }
}