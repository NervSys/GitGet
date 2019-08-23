<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 2:28 PM
 * Note: ctrl.php
 */

namespace app\project;

use app\enum\error_enum;
use app\library\base;
use app\library\git;
use app\model\branch_list;
use app\model\project;
use app\model\project_log;
use app\model\project_srv;
use app\model\server;
use core\helper\log;
use ext\mpc;

class proj_git extends base
{
    public $tz          = '*';
    public $check_token = false;
    const GIT_CMD_TYPE_PULL     = 1;
    const GIT_CMD_TYPE_CHECKOUT = 2;
    const GIT_CMD_TYPE_RESET    = 3;

    /**
     * 更新
     *
     * @param int $proj_id
     *
     * @return array
     * @throws \Exception
     */
    public function update(int $proj_id)
    {
        $data = [
            'cmd'     => 'project/proj_git-local_update',
            'proj_id' => $proj_id
        ];
        $this->lock($proj_id, $data);
        $this->update_branch($proj_id);
        return $this->succeed();
    }

    /**
     * 本地接收处理
     *
     * @param int $proj_id
     *
     * @throws \Exception
     */
    public function local_update(int $proj_id)
    {
        mpc::new()->add([
            'cmd'  => 'project/proj_git-update_cli',
            'data' => [
                'proj_id' => $proj_id
            ]
        ])->go(false);
    }

    /**
     * 后台更新脚本
     *
     * @param int $proj_id
     */
    public function update_cli(int $proj_id)
    {
        git::new($proj_id)->pull();
        $this->add_log($proj_id, self::GIT_CMD_TYPE_PULL);
        $this->unlock($proj_id);
    }

    /**
     * 本地分支列表
     *
     * @param int $proj_id
     *
     * @return array
     */
    public function branch_list(int $proj_id)
    {
        $branch_list = branch_list::new()->where(['proj_id', $proj_id])->get();
        return $this->succeed($branch_list);
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
        $branch_list  = git::new($proj_id)->branch_list();
        $branch_names = [];
        foreach ($branch_list as $value) {
            $branch_name     = substr($value, 2);
            $branch_name_arr = explode('/', $branch_name);
            if (!empty($branch_name_arr[1]) && $branch_name_arr[1] != 'HEAD -> origin') {
                $branch_names[] = $branch_name_arr[1];
            }
        }
        //获取本地分支
        $local_branch = branch_list::new()->where(['proj_id', $proj_id])->field('branch_name')->get_col();
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
        branch_list::new()->where(['proj_id', $proj_id])->value(['active' => 0])->update_data();
        branch_list::new()->where([['proj_id', $proj_id], ['branch_name', $curr_branch[0]]])->value(['active' => 1])->update_data();
        return $this->succeed();
    }

    /**
     * 切换分支
     *
     * @param int $proj_id
     * @param int $branch_id
     *
     * @return array|void
     * @throws \Exception
     */
    public function checkout(int $proj_id, int $branch_id)
    {
        $this->update_branch($proj_id);
        $branch_info = branch_list::new()->where([['proj_id', $proj_id], ['branch_id', $branch_id]])->get_one();
        if (empty($branch_info)) {
            return $this->response(error_enum::BRANCH_NOT_EXIST);
        }
        if ($branch_info['active']) {
            return $this->response(error_enum::BRANCH_NO_CHECK);
        }
        $data = [
            'cmd'         => 'project/proj_git-local_checkout',
            'proj_id'     => $proj_id,
            'branch_name' => $branch_info['branch_name']
        ];
        $this->lock($proj_id, $data);
        return $this->succeed();
    }

    /**
     * 本地接收处理
     *
     * @param int    $proj_id
     * @param string $branch_name
     *
     * @throws \Exception
     */
    public function local_checkout(int $proj_id, string $branch_name)
    {
        mpc::new()->add([
            'cmd'  => 'project/proj_git-checkout_cli',
            'data' => [
                'proj_id'     => $proj_id,
                'branch_name' => $branch_name
            ]
        ])->go(false);
    }

    /**
     * 切换分支后台进程
     *
     * @param int    $proj_id
     * @param string $branch_name
     */
    public function checkout_cli(int $proj_id, string $branch_name)
    {
        git::new($proj_id)->checkout($branch_name);
        $this->update_branch($proj_id);
        $this->add_log($proj_id, self::GIT_CMD_TYPE_CHECKOUT);
        $this->unlock($proj_id);
    }

    /**
     * log列表
     *
     * @param int $proj_id
     * @param int $branch_id
     * @param int $page
     * @param int $page_size
     *
     * @return array
     */
    public function log_list(int $proj_id, int $branch_id, int $page = 1, int $page_size = 10)
    {
        $logs = project_log::new()->where([['proj_id', $proj_id], ['branch_id', $branch_id]])->order(['log_id' => 'desc'])->get_page($page, $page_size);
        foreach ($logs['list'] as &$log) {
            $btn_type      = $log['active'] == 1 ? 'default disabled' : 'success';
            $log['option'] = '<button class="btn btn-' . $btn_type . '" type="button" onclick="reset_commit(' . $proj_id . ',' . $log['log_id'] . ')">回滚</button>';
        }
        return $this->succeed($logs);
    }

    /**
     * 回滚
     *
     * @param int $proj_id
     * @param int $log_id
     *
     * @return array
     * @throws \Exception
     */
    public function reset(int $proj_id, int $log_id)
    {
        $data = [
            'cmd'     => 'project/proj_git-local_reset',
            'proj_id' => $proj_id,
            'log_id'  => $log_id
        ];
        $this->lock($proj_id, $data);
        return $this->succeed();
    }

    /**
     * 本地接收处理
     *
     * @param int $proj_id
     * @param int $log_id
     *
     * @throws \Exception
     */
    public function local_reset(int $proj_id, int $log_id)
    {
        mpc::new()->add([
            'cmd'  => 'project/proj_git-reset_cli',
            'data' => [
                'proj_id' => $proj_id,
                'log_id'  => $log_id
            ]
        ])->go(false);
    }

    /**
     * 后台回滚进程
     *
     * @param int $proj_id
     * @param int $log_id
     *
     * @return array
     */
    public function reset_cli(int $proj_id, int $log_id)
    {
        $commit_id = project_log::new()->where(['log_id', $log_id])->field('commit_id')->get_value();
        git::new($proj_id)->reset($commit_id);
        $this->add_log($proj_id, self::GIT_CMD_TYPE_RESET);
        $this->unlock($proj_id);
        return $this->succeed();
    }

    /**
     * 加锁
     *
     * @param int   $proj_id
     * @param array $data
     *
     * @return bool
     */
    private function lock(int $proj_id, array $data)
    {
        $srv_list = project::new()->field('srv_list')->where(['proj_id', $proj_id])->get_value();
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
        $this->redis->expire($key, 3600 * 5);
        $servers = server::new()->where([['srv_id', $srv_list]])->get();
        foreach ($servers as $server) {
            $ip   = $server['ip'];
            $port = $server['port'];
            $url  = $ip . ":" . $port . "/api.php";
            $this->curl_post($url, $data);
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
            $this->redis->delete($key);
        }
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
        $curr_branch       = git::new($proj_id)->curr_branch();
        $data['proj_id']   = $proj_id;
        $data['proj_log']  = trim($curr_branch[1] ?? '');
        $data['log_type']  = $log_type;
        $data['commit_id'] = git::new($proj_id)->curr_commit_id();;
        $data['branch_id'] = branch_list::new()->where([['proj_id', $proj_id], ['active', 1]])->field('branch_id')->get_value();
        $data['active']    = 1;
        project_log::new()->where(['proj_id', $proj_id])->value(['active' => 0])->update_data();
        if (!project_log::new()->where([['proj_id', $proj_id], ['branch_id', $data['branch_id']], ['commit_id', $data['commit_id']]])->exist()) {
            project_log::new()->value($data)->insert_data();
        }
        project_log::new()->where([['proj_id', $proj_id], ['commit_id', $data['commit_id']]])->value(['active' => 1])->update_data();
    }

    private function curl_post($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        return $result;
    }
}