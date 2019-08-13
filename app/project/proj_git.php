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
use app\model\branch_list;
use app\model\proj_log;

class proj_git extends base
{
    public  $tz      = '*';
    private $user_id = 0;

    public function __construct()
    {
        errno::load('app', 'project');
        if (-1 === $this->user_id = user::new()->get_user_id()) {
            return $this->response(error_enum::NO_USER);
        }
    }

    public function deploy(int $proj_id)
    {

    }

    public function update(int $proj_id)
    {

    }

    public function update_branch(int $proj_id)
    {

    }

    public function checkout(int $proj_id, int $branch_id)
    {

    }

    public function reset(int $proj_id, int $commit_id)
    {

    }

    public function branch_list(int $proj_id)
    {
        $branch_list = branch_list::new()->field('*')->where(['proj_id', $proj_id])->get();
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
}