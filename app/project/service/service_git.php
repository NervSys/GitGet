<?php
/**
 * Created by PhpStorm.
 * User: 25791
 * Date: 2020/4/8
 * Time: 9:41
 * Note: service_git.php
 */

namespace app\project\service;


use app\lib\base;
use app\lib\model\branch_list;
use app\lib\model\project;
use app\lib\model\project_log;
use app\lib\model\server;
use app\library\dir;
use app\library\git;
use ext\http;

class service_git extends base
{
    public $copy_files;
    public $local_path;
    public $stash_files;
    const TEMP_PATH = ".git" . DIRECTORY_SEPARATOR . 'temp';
    const GIT_CMD_TYPE_PULL     = 1;
    const GIT_CMD_TYPE_CHECKOUT = 2;
    const GIT_CMD_TYPE_RESET    = 3;

    public function update(int $proj_id)
    {
        $this->pre_option($proj_id);
        $this->stash_file();
        $lib_git = git::new();
        if (!$lib_git->clean() || !$lib_git->checkout('.') || !$lib_git->pull()) {
            $this->gg_error($proj_id, $lib_git->output);
        }
        $this->apply_file();
        $this->update_branch($proj_id);
        $this->add_log($proj_id, self::GIT_CMD_TYPE_PULL);
        $this->unlock($proj_id);
    }

    public function checkout(int $proj_id, string $branch_name)
    {
        $this->pre_option($proj_id);
        $this->stash_file();
        $lib_git = git::new();
        if (!$lib_git->clean() || !$lib_git->checkout('.') || !$lib_git->checkout($branch_name)) {
            $this->gg_error($proj_id, $lib_git->output);
        }
        $this->apply_file();
        $this->update_branch($proj_id);
        $this->add_log($proj_id, self::GIT_CMD_TYPE_CHECKOUT);
        $this->unlock($proj_id);
    }

    public function reset(int $proj_id, string $commit)
    {
        $this->pre_option($proj_id);
        $this->stash_file();
        $lib_git = git::new();
        if (!$lib_git->clean() || !$lib_git->checkout('.') || !$lib_git->reset($commit)) {
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
     * @return array
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
        $local_branch = branch_list::new()->where(['proj_id', $proj_id])->fields('branch_name')->get(\PDO::FETCH_COLUMN);
        $remove       = [];
        foreach ($local_branch as $branch) {
            if (!in_array($branch, $branch_names)) {
                $remove[] = $branch;
            }
        }
        //更新数据库分支列表
        if (!empty($remove)) {
            branch_list::new()->where([['branch_name', $remove]])->del();
        }
        foreach ($branch_names as $branch_name) {
            if (!in_array($branch_name, $local_branch)) {
                $add = [
                    'branch_name' => $branch_name,
                    'proj_id'     => $proj_id
                ];
                branch_list::new()->value($add)->insert_data();
            }
        }

        $curr_branch = git::new($proj_id)->curr_branch();
        if (empty($curr_branch)) {
            $curr_branch = ['master'];
        }
        branch_list::new()->where(['proj_id', $proj_id])->value(['active' => 0])->update_data();
        branch_list::new()->where([['proj_id', $proj_id], ['branch_name', $curr_branch[0]]])->value(['active' => 1])->update_data();
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
        $data['proj_log']  = trim($curr_branch[1] ?? '');
        $data['log_type']  = $log_type;
        $data['commit_id'] = git::new()->curr_commit_id();;
        $data['branch_id'] = branch_list::new()->where([['proj_id', $proj_id], ['active', 1]])->fields('branch_id')->get_value();
        $data['active']    = 1;
        project_log::new()->where(['proj_id', $proj_id])->value(['active' => 0])->update_data();
        if (!project_log::new()->where([['proj_id', $proj_id], ['branch_id', $data['branch_id']], ['commit_id', $data['commit_id']]])->exist()) {
            project_log::new()->value($data)->insert_data();
        }
        project_log::new()->where([['proj_id', $proj_id], ['commit_id', $data['commit_id']]])->value(['active' => 1])->update_data();
    }


    /**
     * 操作前预处理
     *
     * @param int $proj_id
     */
    private function pre_option(int $proj_id)
    {
        $conf             = project::new()->where(['proj_id', $proj_id])->get_one();
        $git_url          = $conf['proj_git_url'];
        $local_path       = $conf['proj_local_path'];
        $this->local_path = $local_path;
        $this->copy_files = json_decode($conf['proj_backup_files'], true);
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
        $srv_list = project::new()->fields('srv_list')->where(['proj_id', $proj_id])->get_val();
        $srv_list = json_decode($srv_list, true);
        $count    = count($srv_list);
        if ($count <= 0) {
            return false;
        }
        $key = "proj_lock:" . $proj_id;
        if ($this->redis->exists($key)) {
            return false;
        }
        $this->redis->incrBy($key, $count);
        $this->redis->expire($key, 60);
        $servers = server::new()->where([['srv_id', 'IN', $srv_list]])->get();
        foreach ($servers as $server) {
            $url = $server['url'] . "/api.php";
            $res = http::new()->add(['url' => $url, 'data' => $data, 'with_header' => true])->fetch();
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