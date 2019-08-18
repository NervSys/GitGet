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
use app\model\proj_log;
use app\model\project;
use app\model\project_log;
use core\helper\log;
use ext\mpc;

class proj_git extends base
{
    public $tz = '*';
    public $check_token = false;
    const GIT_CMD_TYPE_PULL = 1;
    const GIT_CMD_TYPE_CHECKOUT = 2;
    const GIT_CMD_TYPE_RESET = 3;

    public function update(int $proj_id)
    {
        project::new()->where(['proj_id', $proj_id])->value(['is_lock' => 1])->update_data();
        mpc::new()->add([
            'cmd'  => 'project/proj_git-update_cli',
            'data' => [
                'proj_id' => $proj_id
            ]
        ])->go(false);
        return $this->succeed();
    }

    public function update_cli(int $proj_id)
    {
        $this->update_branch_cli($proj_id);
        git::new($proj_id)->pull();
        $this->add_log($proj_id, self::GIT_CMD_TYPE_PULL);
        $this->unlock($proj_id);
    }

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
                log::alert('add', [$add]);
                branch_list::new()->value($add)->insert_data();
            }
        }

        $curr_branch = git::new($proj_id)->curr_branch();
        branch_list::new()->where(['proj_id', $proj_id])->value(['active' => 0])->update_data();
        branch_list::new()->where([['proj_id', $proj_id], ['branch_name', $curr_branch[0]]])->value(['active' => 1])->update_data();
        return $this->succeed();
    }


    public function checkout(int $proj_id, int $branch_id)
    {

    }

    public function reset(int $proj_id, int $commit_id)
    {

    }

    public function branch_list(int $proj_id)
    {
        $branch_list = branch_list::new()->where(['proj_id', $proj_id])->get();
        return $this->succeed($branch_list);
    }

    public function git_info(int $proj_id, int $page = 1, int $page_size = 10)
    {
        $active_branch_id = branch_list::new()->get_active_branch_id($proj_id);

        $git_logs = proj_log::new()
            ->field('*')
            ->where([['proj_id', $proj_id], ['branch_id', $active_branch_id]])
            ->get_page($page, $page_size);
        return $this->succeed($git_logs);
    }

    private function unlock(int $proj_id)
    {
        project::new()->where(['proj_id', $proj_id])->value(['is_lock' => 0])->update_data();
    }

    private function add_log(int $proj_id, int $log_type)
    {
        $data              = [];
        $curr_branch       = git::new($proj_id)->curr_branch();
        $data['proj_id']   = $proj_id;
        $data['proj_log']  = $curr_branch[1] ?? '';
        $data['log_type']  = $log_type;
        $data['commit_id'] = git::new($proj_id)->curr_commit_id();;
        $data['branch_id'] = branch_list::new()->where([['proj_id', $proj_id], ['active', 1]])->field('branch_id')->get_value();
        if (!project_log::new()->where([['proj_id', $proj_id], ['branch_id', $data['branch_id']], ['commit_id', $data['commit_id']]])->exist()) {
            project_log::new()->value($data)->insert_data();
        }
    }
}