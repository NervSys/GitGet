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
use app\library\base;
use app\model\auth;
use app\model\base_model;
use app\model\branch_list;
use app\model\proj_srv;
use app\model\project;
use app\model\user;
use ext\errno;
use app\git\ctrl as git_ctrl;

class ctrl extends base
{
    public $tz = 'add,checkout,del,team_edit,reset,pull';

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
    public function add(string $proj_name, string $proj_desc, string $git_url, string $local_path, array $srv_ids, array $backup_files, int $proj_id = 0)
    {
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
     * @param int $proj_id
     *
     * @return array
     * @api 删除分支
     */
    public function del(int $proj_id): array
    {
        project::new()->value(['status' => 2])->where(['proj_id', $proj_id])->update_data();
        //暂时先不删除文件
//        $this->deldir($local_path);
//        if (is_dir($local_path)) {
//            @rmdir($local_path);
//        }
        return $this->succeed();
    }

    /**
     * @param int $proj_id
     *
     * @return array
     * @api 更新分支
     */
    public function pull(int $proj_id): array
    {
        project::new()->where(['proj_id',$proj_id])->value(['is_lock'=>1])->update_data();
        return $this->succeed();
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