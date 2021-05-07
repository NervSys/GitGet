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

namespace app\project\service;


use app\lib\base;
use app\lib\model\branch;
use app\lib\model\proj;
use app\lib\model\proj_log;
use app\lib\model\svr;
use app\lib\dir;
use app\lib\git;
use ext\http;
use ext\log;

class service_git extends base
{
    public $copy_files;
    public $local_path;
    public $stash_files;
    const TEMP_PATH             = ".git" . DIRECTORY_SEPARATOR . 'temp';
    const GIT_CMD_TYPE_PULL     = 1;    //更新
    const GIT_CMD_TYPE_CHECKOUT = 2;    //切换分支
    const GIT_CMD_TYPE_RESET    = 3;    //回滚

    public function update(int $proj_id, string $home_path)
    {
        $this->pre_option($proj_id, $home_path);
        $this->stash_file();
        $lib_git = git::new();
        if (!$lib_git->clean() || !$lib_git->clear() || !$lib_git->pull()) {
            $this->gg_error($proj_id, $lib_git->output);
        }
        $this->apply_file();
        $this->update_branch($proj_id);
        $this->add_log($proj_id, self::GIT_CMD_TYPE_PULL);
        $this->unlock($proj_id);
    }

    public function checkout(int $proj_id, string $branch_name, string $home_path)
    {
        $this->pre_option($proj_id, $home_path);
        $this->stash_file();
        $lib_git = git::new();
        if (!$lib_git->clean() || !$lib_git->clear() || !$lib_git->checkout($branch_name)) {
            $this->gg_error($proj_id, $lib_git->output);
        }
        $this->apply_file();
        $this->update_branch($proj_id);
        $this->add_log($proj_id, self::GIT_CMD_TYPE_CHECKOUT);
        $this->unlock($proj_id);
    }

    public function reset(int $proj_id, string $commit, string $home_path)
    {
        $this->pre_option($proj_id, $home_path);
        $this->stash_file();
        $lib_git = git::new();
        if (!$lib_git->clean() || !$lib_git->clear() || !$lib_git->reset($commit)) {
            $this->gg_error($proj_id, $lib_git->output);
        }
        $this->apply_file();
        $this->update_branch($proj_id);
        $this->add_log($proj_id, self::GIT_CMD_TYPE_RESET);
        $this->unlock($proj_id);
    }

    /**
     * 更新本地分支
     *
     * @param int $proj_id
     *
     * @return bool
     */
    public function update_branch(int $proj_id)
    {
        //获取远程分支
        $branch_list  = git::new()->branch_list();
        $branch_names = [];
        foreach ($branch_list as $value) {
            $branch_name     = substr($value, 2);
            $branch_name_arr = explode('/', $branch_name);
            if (!empty($branch_name_arr[1]) && $branch_name_arr[1] != 'HEAD -> origin') {
                $branch_names[] = $branch_name_arr[1];
            }
        }
        if (empty($branch_names)) {
            $branch_names = ['master'];
        }
        //获取本地分支
        $local_branch = branch::new()->where(['proj_id', $proj_id])->fields('name')->get(\PDO::FETCH_COLUMN);
        $remove       = [];
        foreach ($local_branch as $branch) {
            if (!in_array($branch, $branch_names)) {
                $remove[] = $branch;
            }
        }
        //更新数据库分支列表
        if (!empty($remove)) {
            branch::new()->where([['name', $remove]])->del();
        }
        foreach ($branch_names as $branch_name) {
            if (!in_array($branch_name, $local_branch)) {
                $add = [
                    'name'    => $branch_name,
                    'proj_id' => $proj_id
                ];
                branch::new()->value($add)->add();
            }
        }

        $curr_branch = git::new($proj_id)->curr_branch();
        if (empty($curr_branch)) {
            $curr_branch = ['master'];
        }
        branch::new()->where(['proj_id', $proj_id])->value(['active' => 0])->save();
        branch::new()->where([['proj_id', $proj_id], ['name', $curr_branch[0]]])->value(['active' => 1])->save();
        return true;
    }

    /**
     * 增加日志
     *
     * @param int $proj_id
     * @param int $log_type
     */
    private function add_log(int $proj_id, int $log_type)
    {
        $data              = [];
        $curr_branch       = git::new()->curr_branch();
        $data['proj_id']   = $proj_id;
        $data['log']       = trim($curr_branch[1] ?? '');
        $data['log_type']  = $log_type;
        $data['commit_id'] = git::new()->curr_commit_id();;
        $data['branch_id'] = branch::new()->where([['proj_id', $proj_id], ['active', 1]])->fields('id')->get_val();
        $data['active']    = 1;
        proj_log::new()->where(['proj_id', $proj_id])->value(['active' => 0])->save();
        if (!proj_log::new()->where([['proj_id', $proj_id], ['branch_id', $data['branch_id']], ['commit_id', $data['commit_id']]])->exist()) {
            proj_log::new()->value($data)->add();
        }
        proj_log::new()->where([['proj_id', $proj_id], ['commit_id', $data['commit_id']]])->value(['active' => 1])->save();
    }


    /**
     * 操作前预处理
     *
     * @param int    $proj_id
     * @param string $home_path
     */
    private function pre_option(int $proj_id, string $home_path)
    {
        $conf             = proj::new()->where(['id', $proj_id])->get_one();
        $git_url          = $conf['git_url'];
        $local_path       = $conf['local_path'];
        $this->local_path = $home_path . $local_path;
        $this->copy_files = json_decode($conf['backup_files'], true);
        if (!is_dir($local_path)) {
            mkdir($local_path, 0777, true);
            chmod($local_path, 0777);
        }
        if (!is_dir($local_path . DIRECTORY_SEPARATOR . '.git')) {
            $res = git::new()->clone($git_url, $local_path);
            if (!$res) {
                $this->gg_error($proj_id, git::new()->output);
            }
        }
        chdir($local_path);
    }

    /**
     * 贮藏文件
     */
    private function stash_file()
    {
        if (empty($this->copy_files)) {
            return;
        }
        foreach ($this->copy_files as $item) {
            $path_from = $this->local_path . DIRECTORY_SEPARATOR . $item;
            $path_to   = $this->local_path . DIRECTORY_SEPARATOR . self::TEMP_PATH . DIRECTORY_SEPARATOR . $item;
            dir::new()->copy_to($path_from, $path_to);

            $this->stash_files[] = [
                'source' => $path_from,
                'dest'   => $path_to
            ];
        }
    }

    /**
     * 恢复文件
     */
    private function apply_file(): void
    {
        if (empty($this->stash_files)) {
            return;
        }
        //copy files
        foreach ($this->stash_files as $item) {
            dir::new()->copy_to($item['dest'], $item['source']);
        }
        dir::new()->del_dir($this->local_path . DIRECTORY_SEPARATOR . self::TEMP_PATH);
    }

    /**
     * @param int    $proj_id
     * @param string $error_msg
     */
    private function gg_error(int $proj_id, string $error_msg)
    {
        $key = 'gg_error:' . $proj_id;
        $this->redis->setex($key, 3600, $error_msg);
    }

    /**
     * 通知各服务器
     *
     * @param int   $proj_id
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function request(int $proj_id, array $data)
    {
        $svr_list = proj::new()->fields('svr_list')->where(['id', $proj_id])->get_val();
        $svr_list = json_decode($svr_list, true);
        $count    = count($svr_list);
        if ($count <= 0) {
            return false;
        }
        $key = "proj_lock:" . $proj_id;
        if ($this->redis->exists($key)) {
            return false;
        }
        $this->redis->incrBy($key, $count);
        $this->redis->expire($key, 60);
        $servers = svr::new()->where([['id', 'IN', $svr_list]])->get();
        foreach ($servers as $server) {
            $url                       = $server['url'] . "/api.php";
            $data['data']['home_path'] = $server['home_path'];
            $res                       = http::new()->add(['url' => $url, 'data' => $data, 'with_header' => true])->fetch();
            if (!$res) {
                $this->gg_error($proj_id, '服务器请求出错');
            }
        }
        return true;
    }

    /**
     * 解锁
     *
     * @param int $proj_id
     *
     * @return bool
     */
    private function unlock(int $proj_id)
    {
        $key = "proj_lock:" . $proj_id;
        if (!$this->redis->exists($key)) {
            return false;
        }
        $res = $this->redis->decrBy($key, 1);
        if ($res <= 0) {
            $this->redis->del($key);
        }
    }
}