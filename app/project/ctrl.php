<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 2:28 PM
 * Note: ctrl.php
 */

namespace app\project;

use ext\errno;
use app\library\model;
use app\git\ctrl as git_ctrl;

class ctrl extends model
{
    public $tz = 'add,edit,checkout,del,team_edit';

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
     * @api 新增或编辑项目
     * @param string $proj_name
     * @param string $proj_desc
     * @param string $proj_git_url
     * @param string $proj_local_path
     * @param string $proj_user_name
     * @param string $proj_user_email
     * @param array $proj_backup_files
     * @return array
     */
    public function add(
        string $proj_name,
        string $proj_desc,
        string $proj_git_url,
        string $proj_local_path,
        string $proj_user_name,
        string $proj_user_email,
        array $proj_backup_files = []
    ): array
    {
        $proj_local_paths = $this->select('project')
            ->field('proj_local_path')
            ->where(['status',1])
            ->fetch(true);
        if (in_array($proj_local_path,$proj_local_paths)){
            return errno::get(3005,1);
        }
        $this->begin();
        try {
            $time = time();
            $this->insert('project')
                ->value([
                    'proj_name' => $proj_name,
                    'proj_desc' => $proj_desc,
                    'proj_git_url' => $proj_git_url,
                    'proj_local_path' => $proj_local_path,
                    'proj_user_name' => $proj_user_name,
                    'proj_user_email' => $proj_user_email,
                    'proj_backup_files' => json_encode($proj_backup_files),
                    'add_time' => $time
                ])
                ->execute();
            $proj_id = $this->last_insert();
            $this->insert('project_team')
                ->value([
                    'proj_id' => $proj_id,
                    'user_id' => $this->user_id,
                    'add_time' => $time
                ])
                ->execute();
            $conf = [
                'git_url' => $proj_git_url,
                'local_path' => $proj_local_path,
                'user_name' => $proj_user_name,
                'user_email' => $proj_user_email,
                'proj_id' => $proj_id,
                'user_id' => $this->user_id
            ];
            git_ctrl::new($conf);
            $this->commit();
        } catch (\PDOException $e) {
            $this->rollback();
            $err = $e->getMessage();
            errno::set(3003, 1);
            return ['err' => $err];
        }
        return errno::get(3002);
    }

    /**
     * @api 编辑项目
     * @param int $proj_id
     * @param array $update
     * @return array
     */
    public function edit(int $proj_id, array $update = []): array
    {
        $this->begin();
        try {
            if (!empty($update['proj_backup_files'])){
                $update['proj_backup_files'] = json_encode($update['proj_backup_files']);
            }
            $this->update('project')
                ->value($update)
                ->where(['proj_id', $proj_id])
                ->execute();
            $this->commit();
        } catch (\PDOException $e) {
            $this->rollback();
            return errno::get(3003,1);
        }
        return errno::get(3002);
    }

    /**
     * @api 切换分支
     * @param int $proj_id
     * @param string $branch
     * @return array
     */
    public function checkout(int $proj_id,string $branch):array
    {
        $conf = show::new()->conf($proj_id);
        $res = git_ctrl::new($conf)->deploy($branch);
        return $res;
    }

    /**
     * @api 删除分支
     * @param int $proj_id
     * @return array
     */
    public function del(int $proj_id):array
    {
        $conf = show::new()->conf($proj_id);
        if (empty($conf)){
            return errno::get(3003,1);
        }
        $local_path = $conf['local_path'];
        $this->deldir($local_path);
        if (is_dir($local_path)){
            @rmdir($local_path);
        }
        $this->update('project')
            ->value(['status'=>0])
            ->where(['proj_id',$proj_id])
            ->execute();
        return errno::get(3002);
    }

    /**
     * @api 编辑团队用户
     * @param int $proj_id
     * @param array $user_ids
     * @return array
     */
    public function team_edit(int $proj_id,array $user_ids):array
    {
        if (empty($user_ids)){
            return errno::get(3006,1);
        }
        $this->delete('project_team')
            ->where([
                ['proj_id',$proj_id],
                ['user_id','IN',$user_ids]
            ])
            ->execute();
        foreach ($user_ids as $user_id) {
            $this->insert('project_team')
                ->value([
                    'proj_id'=>$proj_id,
                    'user_id'=>$user_id,
                    'add_time'=>time()
                ])
                ->execute();
        }
        return errno::get(3002);
    }

    private function deldir($path)
    {
        //如果不是斜线结尾，加上斜线
        $last = substr($path,-1);
        if ($last !== '/'){
            $path.='/';
        }
        if(is_dir($path)){
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach($p as $val){
                //排除目录中的.和..
                if($val !="." && $val !=".."){
                    //如果是目录则递归子目录，继续操作
                    if(is_dir($path.$val)){
                        //子目录中操作删除文件夹和文件
                        $this->deldir($path.$val.'/');
                        //目录清空后删除空文件夹
                        @rmdir($path.$val);
                    }else{
                        //如果是文件直接删除
                        chmod($path.$val,0777);
                        unlink($path.$val);
                    }
                }
            }
        }
    }

    public function add_log(int $proj_id, int $user_id, array $proj_log, int $log_type, string $branch)
    {
        return $this->insert('project_log')
            ->value([
                'proj_id' => $proj_id,
                'user_id' => $user_id,
                'proj_log' => json_encode($proj_log),
                'log_type' => $log_type,
                'branch' => $branch,
                'add_time' => time()
            ])
            ->execute();
    }
}