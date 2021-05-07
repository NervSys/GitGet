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

namespace api;


use app\lib\base;
use app\lib\model\branch;
use app\lib\model\proj_log;
use app\project\service\service_git;
use Ext\libMPC;

class git extends base
{
    /**
     * 更新操作
     *
     * @param int $proj_id
     *
     * @return bool
     * @throws \Exception
     */
    public function update(int $proj_id): bool
    {
        //test1
        $data = [
            'c'     => 'project/git-local_receive',
            'cli_c' => 'project/git-update_cli',
            'data'  => ['proj_id' => $proj_id]
        ];
        return service_git::new()->request($proj_id, $data);
    }

    public function update_cli(int $proj_id, string $home_path)
    {
        service_git::new()->update($proj_id, $home_path);
    }

    /**
     * 切换分支
     *
     * @param int $proj_id
     * @param int $branch_id
     *
     * @return bool
     * @throws \Exception
     */
    public function checkout(int $proj_id, int $branch_id): bool
    {
        $branch_name = branch::new()->where([['proj_id', $proj_id], ['id', $branch_id]])->select('name')->get_val();
        $data        = [
            'c'     => 'project/git-local_receive',
            'cli_c' => 'project/git-checkout_cli',
            'data'  => ['proj_id' => $proj_id, 'branch_name' => $branch_name]
        ];
        return service_git::new()->request($proj_id, $data);
    }

    public function checkout_cli(int $proj_id, string $branch_name, string $home_path)
    {
        service_git::new()->checkout($proj_id, $branch_name, $home_path);
    }

    /**
     * 回滚到某个节点
     *
     * @param int $proj_id
     * @param int $log_id
     *
     * @return bool
     * @throws \Exception
     */
    public function reset(int $proj_id, int $log_id): bool
    {
        $commit_id = proj_log::new()->where(['id', $log_id])->select('commit_id')->get_val();
        $data      = [
            'c'     => 'project/git-local_receive',
            'cli_c' => 'project/git-reset_cli',
            'data'  => ['proj_id' => $proj_id, 'commit' => $commit_id]
        ];
        return service_git::new()->request($proj_id, $data);
    }

    public function reset_cli(int $proj_id, string $commit, string $home_path)
    {
        service_git::new()->reset($proj_id, $commit, $home_path);
    }

    /**
     * 本地接收请求,调起mpc,执行cli
     *
     * @param string $cli_c
     * @param array  $data
     *
     * @return bool
     * @throws \Exception
     */
    public function local_receive(string $cli_c, array $data): bool
    {
        $mpc = libMPC::new();

        $mpc->addJob($cli_c, $data + ['nohup' => true]);

        return true;
    }

    /**
     * 分支列表
     *
     * @param int $proj_id
     *
     * @return array
     */
    public function branch_list(int $proj_id): array
    {
        return branch::new()->where(['proj_id', $proj_id])->get();
    }

    /**
     *
     * @param int $proj_id
     * @param int $page
     * @param int $page_size
     *
     * @return array
     */
    public function log_list(int $proj_id, int $page, int $page_size): array
    {
        $branch_id = branch::new()->where([['proj_id', $proj_id], ['active', 1]])->select('id')->get_val();
        return proj_log::new()->where([['proj_id', $proj_id], ['branch_id', $branch_id]])->order(['id' => 'desc'])->get_page($page, $page_size);
    }
}