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


namespace app\project;

use app\library\base;
use app\model\branch_list;
use app\model\project;

class ctrl extends base
{
    public $tz = '*';

    /**
     * 新增或编辑项目
     *
     * @param string $proj_name
     * @param string $proj_desc
     * @param string $git_url
     * @param string $local_path
     * @param array  $srv_ids
     * @param array  $backup_files
     * @param int    $proj_id
     *
     * @return array
     */
    public function add(string $proj_name, string $proj_desc, string $git_url, string $local_path, array $srv_ids, array $backup_files = [], int $proj_id = 0)
    {
        foreach ($srv_ids as &$srv_id) {
            $srv_id = (int)$srv_id;
        }
        $data = [
            'proj_name'         => $proj_name,
            'proj_desc'         => $proj_desc,
            'proj_git_url'      => $git_url,
            'proj_local_path'   => $local_path,
            'srv_list'          => json_encode($srv_ids),
            'proj_backup_files' => json_encode($backup_files),
        ];
        if ($proj_id == 0) {
            project::new()->value($data)->insert_data();
            $proj_id = project::new()->lastInsertId();
            $data    = [
                'branch_name' => 'master',
                'proj_id'     => (int)$proj_id,
                'active'      => 1,
            ];
            branch_list::new()->value($data)->insert_data();
        } else {
            project::new()->value($data)->where(['proj_id', $proj_id])->update_data();
        }
        return $this->succeed();
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
        project::new()->value(['status' => 2])->where(['proj_id', $proj_id])->update_data();
        return $this->succeed();
    }
}