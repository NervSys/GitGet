<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 2:28 PM
 * Note: show.php
 */

namespace app\project;

use ext\errno;
use app\library\model;
use app\git\ctrl;

class show extends model
{
    public $tz = 'list,info,branch,team_list';

    private $user_id = 0;

    /**
     * ctrl constructor.
     */
    public function __construct()
    {
        parent::__construct();

        errno::load('app', 'proj_ctrl');

        if (0 === $this->user_id = $this->get_user_id()) {
            errno::set(3000);
            parent::stop();
        }
    }

    /**
     * @api 项目列表
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function list(int $page = 1, int $page_size = 10): array
    {
        errno::set(3002);
        $cnt_data = $this->select('project_team')->where(['user_id',$this->user_id])->field('count(*)')->fetch(true)[0];
        $cnt_page = ceil($cnt_data/$page_size);
        $lim_start = ($page-1) * $page_size;
        $list =  $this->select('project_team AS a')
            ->join('project AS b', ['a.proj_id', 'b.proj_id'])
            ->field('a.proj_id', 'b.proj_name', 'b.proj_desc','b.proj_git_url','b.proj_local_path','b.proj_user_name','b.proj_user_email','b.proj_backup_files', 'b.add_time')
            ->where([['a.user_id', $this->user_id],['status',1]])
            ->order(['b.add_time' => 'desc'])
            ->limit($lim_start,$page_size)
            ->fetch();
        foreach ($list as &$item) {
            $item['add_time']       = date('Y-m-d H:i:s', $item['add_time']);
            $proj_name=$item['proj_name'];
            $operate = '<a style="text-decoration:none" class="ml-5" onClick="proj_edit(\'编辑\', \'./project_edit.php?proj_id=' . $item['proj_id'] . '\', 1300)" href="javascript:;" title="编辑">编辑</a>';
            $operate .= '&nbsp;&nbsp;&nbsp;&nbsp;<a style="text-decoration:none" class="ml-5" onClick="proj_edit(\'编辑\', \'./proj_checkout.php?proj_id=' . $item['proj_id'] . '&proj_name=' . $proj_name.'\', 1300)" href="javascript:;" title="切换">切换</a>';
            $operate .= '&nbsp;&nbsp;&nbsp;&nbsp;<a style="text-decoration:none" style="color:red" class="ml-5" onClick="proj_del(\'删除\', \'./proj_del.php?proj_id=' . $item['proj_id'] . '\', 1300)" href="javascript:;" title="删除">删除</a>';
            $item['option'] = $operate;
        }
        return [
            'cnt_data' => $cnt_data,
            'cnt_page' => $cnt_page,
            'curr_page' => $page,
            'list' => $list
        ];
    }

    /**
     * @api 详细信息
     * @param int $proj_id
     * @return array
     */
    public function info(int $proj_id):array
    {
        errno::set(3002);
        $project = $this->select('project')
            ->field('*')
            ->where(['proj_id',$proj_id])
            ->limit(1)
            ->fetch();
        if (empty($project)){
            return [];
        }
        $project[0]['proj_backup_files']=json_decode($project[0]['proj_backup_files'],true);
        return $project[0];
    }

    public function branch(int $proj_id):array
    {
        errno::set(3002);
        $conf = $this->conf($proj_id);
        $output = ctrl::new($conf)->branch();
        $branch_names = [];
        $active_branch = '';
        foreach ($output as $value) {
            $branch_name = substr($value,2);
            $branch_name_arr = explode('/',$branch_name);
            if ($branch_name_arr[0] == 'remotes'){
                if (!empty($branch_name_arr[2])){
                    if ( strpos($branch_name_arr[2],'HEAD') === 0 ){
                        continue;
                    }
                    $branch_name = $branch_name_arr[2];
                }
            }
            if (substr($value,0,1) == '*'){
                $active_branch = $branch_name;
            }
            if ( in_array($branch_name,$branch_names)){
                continue;
            }
            $branch_names[] = $branch_name;
        }
        return [
            'branch_names'=>$branch_names,
            'active_branch' => $active_branch
        ];
    }

    /**
     * @api 团队用户列表
     * @param $proj_id
     * @return array
     */
    public function team_list($proj_id):array
    {
        errno::set(3002);
        $user_list  = $this->select('user')
            ->field('user_id','user_acc')
            ->fetch();
        $selected_user_ids = $this->select('project_team')
            ->field('user_id')
            ->where(['proj_id',$proj_id])
            ->fetch(true);
        foreach ($user_list as &$user) {
            $user['selected'] = false;
            if (in_array($user['user_id'],$selected_user_ids)){
                $user['selected'] = true;
            }
        }
        return $user_list;
    }

    public function conf(int $proj_id) :array
    {
        $project = $this->select('project')
            ->field('*')
            ->where(['proj_id',$proj_id])
            ->fetch();
        if (empty($project)){
            return [];
        }
        $project = $project[0];
        $conf = [
            'git_url' => $project['proj_git_url'],
            'local_path' => $project['proj_local_path'],
            'user_name' => $project['proj_user_name'],
            'user_email' => $project['proj_user_email'],
            'proj_backup_files' => $project['proj_backup_files'],
        ];
        return $conf;
    }
}