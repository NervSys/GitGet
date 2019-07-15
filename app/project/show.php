<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 2:28 PM
 * Note: show.php
 */

namespace app\project;

use app\enum\operate;
use app\model\auth;
use app\model\user;
use core\helper\log;
use ext\errno;
use app\git\ctrl;

class show
{
    public $tz = 'list,info,branch,team_list,pull_logs';

    private $user_id = 0;

    /**
     * ctrl constructor.
     */
    public function __construct()
    {
        errno::load('app', 'project');
        $this->user_id = user::new()->get_user_id();
    }

    /**
     * @api 项目列表
     *
     * @param int    $page
     * @param int    $page_size
     * @param string $proj_name
     *
     * @return array
     * @throws \Exception
     */
    public function list(int $page = 1, int $page_size = 10, string $proj_name = ''): array
    {
        log::debug('test',['test1']);
        errno::set(3002);
        $where   = [
            ['operate_id', operate::OPERATE_GET]
        ];
        $user_id = $this->user_id;
        if ($user_id) {
            $where[] = ['user_id', $user_id];
        }
        if ($proj_name){
            $where[] = ['b.proj_name',$proj_name];
        }
        $cnt_data = auth::new()
            ->alias('a')
            ->join('project as b', ['a.proj_id', 'b.proj_id'], 'LEFT')
            ->where($where)
            ->count();

        $list = auth::new()
            ->alias('a')
            ->join('project as b', ['a.proj_id', 'b.proj_id'], 'LEFT')
            ->where($where)
            ->limit(($page - 1) * 10, $page_size)
            ->field('b.proj_id', 'b.proj_name', 'b.proj_desc', 'b.proj_git_url', 'b.create_time')
            ->get();

        $cnt_page = ceil($cnt_data / $page_size);
        foreach ($list as &$item) {
            $operate[]      = $this->get_operate($item['proj_id'], operate::OPERATE_GET);
            $operate[]      = $this->get_operate($item['proj_id'], operate::OPERATE_DEPLOY);
            $item['option'] = implode('&nbsp;&nbsp;&nbsp;&nbsp;', $operate);
        }
        return [
            'cnt_data'  => $cnt_data,
            'cnt_page'  => $cnt_page,
            'curr_page' => $page,
            'list'      => $list
        ];
    }

    /**
     * @param int $proj_id
     *
     * @return array
     * @api 详细信息
     */
    public function info(int $proj_id): array
    {
        errno::set(3002);
        $project = $this->select('project')
            ->field('*')
            ->where(['proj_id', $proj_id])
            ->limit(1)
            ->fetch();
        if (empty($project)) {
            return [];
        }
        $project[0]['proj_backup_files'] = json_decode($project[0]['proj_backup_files'], true);
        return $project[0];
    }

    public function branch(int $proj_id): array
    {
        errno::set(3002);
        $conf          = $this->conf($proj_id);
        $output        = ctrl::new($conf)->branch();
        $branch_names  = [];
        $active_branch = $conf['active_branch'];
        foreach ($output as $value) {
            $branch_name     = substr($value, 2);
            $branch_name_arr = explode('/', $branch_name);
            if (!empty($branch_name_arr[1])) {
                $branch_names[] = $branch_name_arr[1];
            }
        }
        return [
            'branch_names'  => $branch_names,
            'active_branch' => $active_branch
        ];
    }

    /**
     * @param $proj_id
     *
     * @return array
     * @api 团队用户列表
     */
    public function team_list($proj_id): array
    {
        errno::set(3002);
        $user_list         = $this->select('user')
            ->field('user_id', 'user_acc')
            ->fetch();
        $selected_user_ids = $this->select('project_team')
            ->field('user_id')
            ->where(['proj_id', $proj_id])
            ->fetch(\PDO::FETCH_COLUMN);
        foreach ($user_list as &$user) {
            $user['selected'] = false;
            if (in_array($user['user_id'], $selected_user_ids)) {
                $user['selected'] = true;
            }
        }
        return $user_list;
    }

    public function pull_logs(int $proj_id, int $page = 1, int $page_size = 10): array
    {
        errno::set(3002);
        $branch = $this->active_branch($proj_id);
        $count  = $this->select('project_log a')
            ->join('user b', ['a.user_id', 'b.user_id'], 'LEFT')
            ->where([['a.proj_id', $proj_id], ['a.branch', $branch], ['log_type', 'IN', [ctrl::GIT_CMD_TYPE_PULL, ctrl::GIT_CMD_TYPE_RESET]]])
            ->field('count(*) as cnt')->fetch(\PDO::FETCH_COLUMN);
        $count  = $count[0] ?? 0;
        $list   = $this->select('project_log a')
            ->join('user b', ['a.user_id', 'b.user_id'], 'LEFT')
            ->where([['a.proj_id', $proj_id], ['a.branch', $branch], ['log_type', 'IN', [ctrl::GIT_CMD_TYPE_PULL, ctrl::GIT_CMD_TYPE_RESET]]])
            ->field('a.proj_id', 'a.user_id', 'a.proj_log', 'a.log_type', 'b.user_acc', 'a.add_time')->order(['add_time' => 'DESC'])->fetch();

        if (!empty($list)) {
            foreach ($list as &$re) {
                $re['add_time'] = date('Y-m-d H:i:s', $re['add_time']);
                $proj_log       = json_decode($re['proj_log'], true);
                unset($re['proj_log']);
                $re['current_commit_id']   = $proj_log['after_commit_id'];
                $re['current_commit_data'] = $proj_log['current_commit_data'];
                $re['radio_html']          = '<input type="radio" name="commit" value="' . $proj_log['after_commit_id'] . '" />';
            }
        }
        $res = [
            'cnt_data'  => $count,
            'cnt_page'  => ceil($count / $page_size),
            'curr_page' => $page,
            'branch'    => $branch,
            'list'      => $list
        ];
        return $res;
    }

    public function active_branch(int $proj_id): string
    {
        $proj = $this->select('project')
            ->field('active_branch')
            ->where(['proj_id', $proj_id])
            ->fetch(\PDO::FETCH_COLUMN);
        return $proj[0] ?? '';
    }

    public function conf(int $proj_id): array
    {
        $project = $this->select('project')
            ->field('*')
            ->where(['proj_id', $proj_id])
            ->fetch();
        if (empty($project)) {
            return [];
        }
        $project = $project[0];
        $conf    = [
            'git_url'           => $project['proj_git_url'],
            'local_path'        => $project['proj_local_path'],
            'user_name'         => $project['proj_user_name'],
            'user_email'        => $project['proj_user_email'],
            'proj_backup_files' => $project['proj_backup_files'],
            'proj_id'           => $proj_id,
            'user_id'           => $this->user_id,
            'active_branch'     => $project['active_branch']
        ];
        return $conf;
    }

    //获得用户是否拥有某个权限
    public function get_operate($proj_id, $operate_id)
    {
        $user_id = $this->user_id;
        $exist   = auth::new()->where([['user_id', $user_id], ['proj_id', $proj_id], ['operate_id', $operate_id]])->exist();
        if (!$exist) {
            return '';
        }
        switch ($operate_id) {
            case operate::OPERATE_GET:
                return '<a style="text-decoration:none" class="ml-5" onClick="proj_edit(\'编辑\', \'./project_edit.php?proj_id=' . $proj_id . '\', 1300)" href="javascript:;" title="编辑">编辑</a>';
            case operate::OPERATE_DEPLOY:
                return '<a style="text-decoration:none" class="ml-5" href = "./project_deploy.php?proj_id="' . $proj_id . ' title="部署">部署</a>';
        }
    }
}