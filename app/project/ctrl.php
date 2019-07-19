<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 2:28 PM
 * Note: ctrl.php
 */

namespace app\project;

use app\enum\operate;
use app\model\auth;
use app\model\base_model;
use app\model\proj_srv;
use app\model\project;
use app\model\user;
use ext\errno;
use app\git\ctrl as git_ctrl;

class ctrl extends base_model
{
    public $tz = 'add,checkout,del,team_edit,reset,pull';

    private $user_id = 0;

    /**
     * ctrl constructor.
     */
    public function __construct()
    {
        parent::__construct();

        errno::load('app', 'project');

        if (-1 === $this->user_id = user::new()->get_user_id()) {
            errno::set(3000);
            parent::stop();
        }
    }

    /**
     * @param string $proj_name
     * @param string $proj_desc
     * @param string $proj_git_url
     * @param int    $proj_id
     * @param int    $srv_ids
     *
     * @return array
     * @api 新增或编辑项目
     */
    public function add(string $proj_name,string $proj_desc,string $proj_git_url,array $srv_ids,int $proj_id = 0): array
    {
        /*if ($proj_id == 0) {
            //新增时判断
            $proj_local_paths = $this->select('project')
                ->field('proj_local_path')
                ->where(['status', 1])
                ->fetch(\PDO::FETCH_COLUMN);
            if (in_array($proj_local_path, $proj_local_paths)) {
                return errno::get(3005, 1);
            }
        }*/
            if ($proj_id == 0) {
                /*$conf          = [
                    'git_url'       => $proj_git_url,
                    'proj_id'       => $proj_id,
                    'user_id'       => $this->user_id,
                    'active_branch' => 'master'
                ];
                $active_branch = git_ctrl::new($conf)->active_branch_name();*/
                //新增
                $data=[
                    'proj_name'         => $proj_name,
                    'proj_desc'         => $proj_desc,
                    'proj_git_url'      => $proj_git_url,
                ];
                $proj_id=project::new()->addProject($data);
                $operate_ids=operate::getAllAuth();
                auth::new()->addAuth(['user_id'=>0,'proj_id'=>$proj_id,'operate_ids'=>$operate_ids]);
            } else {
                $data=[
                    'proj_name'         => $proj_name,
                    'proj_desc'         => $proj_desc,
                ];
               project::new()->updateProject($data,$proj_id);
               proj_srv::new()->delSrv($proj_id);
            }

        if($srv_ids){
            foreach($srv_ids as $srv_id){
                $srvdata=[
                    'proj_id'=>$proj_id,
                    'srv_id'=>$srv_id,
                ];
                proj_srv::new()->addProjectSrv($srvdata);
            }
        }
        return errno::get(3002);
    }


    /**
     * @param int    $proj_id
     * @param string $branch
     *
     * @return array
     * @api 切换分支
     */
    public function checkout(int $proj_id, string $branch): array
    {
        $conf = show::new()->conf($proj_id);
        $res  = git_ctrl::new($conf)->deploy($branch);
        if ($res) {
            $this->update('project')
                ->value(['active_branch' => $branch])
                ->where(['proj_id', $proj_id])
                ->execute();
            return errno::get(3002);
        }
        return errno::get(3003);
    }

    /**
     * @param int $proj_id
     *
     * @return array
     * @api 删除分支
     */
    public function del(int $proj_id): array
    {
        $conf = show::new()->conf($proj_id);
        if (empty($conf)) {
            return errno::get(3003, 1);
        }
        $local_path = $conf['local_path'];
        $this->deldir($local_path);
        if (is_dir($local_path)) {
            @rmdir($local_path);
        }
        $this->update('project')
            ->value(['status' => 0])
            ->where(['proj_id', $proj_id])
            ->execute();
        return errno::get(3002);
    }

    /**
     * @param int $proj_id
     *
     * @return array
     * @api 更新分支
     */
    public function pull(int $proj_id): array
    {
        $conf = show::new()->conf($proj_id);
        $res  = git_ctrl::new($conf)->pull();
        if ($res) {
            return errno::get(3002);
        }
        return errno::get(3007, 1);
    }

    /**
     * @param int    $proj_id
     * @param string $commit
     *
     * @return array
     * @api 重置某项目的某分支到某节点
     */
    public function reset(int $proj_id, string $commit): array
    {
        $conf = show::new()->conf($proj_id);
        $res  = git_ctrl::new($conf)->reset($commit);
        if ($res) {
            return errno::get(3002);
        }
        return errno::get(3003, 1);
    }

    /**
     * @param int   $proj_id
     * @param array $user_ids
     *
     * @return array
     * @api 编辑团队用户
     */
    public function team_edit(int $proj_id, array $user_ids): array
    {
        if (empty($user_ids)) {
            return errno::get(3006, 1);
        }
        $this->delete('project_team')
            ->where([
                ['proj_id', $proj_id],
                ['user_id', 'IN', $user_ids]
            ])
            ->execute();
        foreach ($user_ids as $user_id) {
            $this->insert('project_team')
                ->value([
                    'proj_id'  => $proj_id,
                    'user_id'  => $user_id,
                    'add_time' => time()
                ])
                ->execute();
        }
        return errno::get(3002);
    }

    private function deldir($path)
    {
        //如果不是斜线结尾，加上斜线
        $last = substr($path, -1);
        if ($last !== '/') {
            $path .= '/';
        }
        if (is_dir($path)) {
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach ($p as $val) {
                //排除目录中的.和..
                if ($val != "." && $val != "..") {
                    //如果是目录则递归子目录，继续操作
                    if (is_dir($path . $val)) {
                        //子目录中操作删除文件夹和文件
                        $this->deldir($path . $val . '/');
                        //目录清空后删除空文件夹
                        @rmdir($path . $val);
                    } else {
                        //如果是文件直接删除
                        chmod($path . $val, 0777);
                        unlink($path . $val);
                    }
                }
            }
        }
    }

    public function add_log(int $proj_id, int $user_id, array $proj_log, int $log_type, string $branch)
    {
        return $this->insert('project_log')
            ->value([
                'proj_id'  => $proj_id,
                'user_id'  => $user_id,
                'proj_log' => json_encode($proj_log),
                'log_type' => $log_type,
                'branch'   => $branch,
                'add_time' => time()
            ])
            ->execute();
    }
}