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
    public $tz = 'add,edit,checkout,pull';

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
                $log='添加项目';
            $conf = [
                'git_url' => $proj_git_url,
                'local_path' => $proj_local_path,
                'user_name' => $proj_user_name,
                'user_email' => $proj_user_email,
            ];
            git_ctrl::new($conf);
            $this->add_log($proj_id,$this->user_id,$log);
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
            $this->add_log($proj_id,$this->user_id,'编辑项目');
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
        $this->add_log($proj_id,$this->user_id,'切换到'.$branch);
        return $res;
    }

    /**
     * @api 更新分支
     * @param int $proj_id
     * @param string $branch
     * @return array
     */
    public function pull(int $proj_id,string $branch):array
    {
        errno::set(3002);
        $conf = show::new()->conf($proj_id);
        $res = git_ctrl::new($conf)->pull($branch);
        $this->add_log($proj_id,$this->user_id,'更新'.$branch.'分支');
        return $res;
    }

    private function add_log(int $proj_id,int $user_id , string $proj_log)
    {
        return $this->insert('project_log')
            ->value([
                'proj_id' => $proj_id,
                'proj_log' => $proj_log,
                'user_id' => $user_id,
                'add_time' => time()
            ])
            ->execute();
    }
}