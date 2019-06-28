<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 5:44 PM
 * Note: ctrl.php
 */

namespace app\git;

use core\handler\factory;
use ext\errno;
use app\project\ctrl as proj_ctrl;

class ctrl extends factory
{
    const PROJ_CONF_KEY = ['git_url', 'local_path', 'user_name', 'user_email'];

    const GIT_CMD_TYPE_PULL = 1;
    const GIT_CMD_TYPE_CHECKOUT = 2;
    const GIT_CMD_TYPE_RESET = 3;

    public $git_log_stack = [
        'after_commit_id'=>'',
        'current_commit_data'=>'',
    ];

    const TEMP_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'temp';

    public $tz = 'deploy,get_status,pull';

    private $local_path  = '';
    private $user_name   = '';
    private $user_email  = '';
    private $copy_files  = [];
    private $stash_files = [];

    private $git_instance = null;
    private $proj_id;
    private $user_id;
    private $active_branch;

    /**
     * ctrl constructor.
     *
     * @param array $conf
     *
     * @throws \Exception
     */
    public function __construct(array $conf = [])
    {

        errno::load('app', 'git_cmd');

        $keys = array_flip(self::PROJ_CONF_KEY);

        $inter = array_intersect_key($keys, $conf);
        $diff  = array_diff_key($keys, $inter);

        if (!empty($diff)) {
            throw new \Exception('Git config [' . implode(', ', array_keys($diff)) . '] NOT set!', E_USER_ERROR);
        }

        $this->git_instance = cmd::new($conf['git_url'], $conf['local_path']);
        $this->git_instance->fetch();

        $this->local_path = &$conf['local_path'];
        $this->user_name  = &$conf['user_name'];
        $this->user_email = &$conf['user_email'];

        $this->proj_id = $conf['proj_id'];
        $this->user_id = $conf['user_id'];
        $this->active_branch = $conf['active_branch'];

        if (isset($conf['proj_backup_files'])) {
            $this->copy_files = json_decode($conf['proj_backup_files'],true);
        }

        unset($conf);
    }

    /**
     * @api 部署为某分支
     * @param string $branch
     * @return bool
     */
    public function deploy(string $branch): bool
    {
        $this->stash_file();

        $res = $this->git_checkout($branch);
        $this->git_pull();

        $this->apply_file();
        if ($res) {
            return true;
        }
        return false;
    }

    /**
     * @api 更新分支
     * @return bool
     */
    public function pull():bool
    {
        $this->stash_file();
        $res = $this->git_pull();
        $this->apply_file();
        if ($res) {
            return true;
        }
        return false;
    }


    /**
     * @api 回滚
     * @param string $commit
     * @return bool
     */
    public function reset(string $commit):bool
    {
        $this->stash_file();
        $before_commit_id = $this->git_instance->current_commit();
        $this->git_instance->reset($commit);
        $this->apply_file();
        if ($before_commit_id != $commit){
            $this->git_log_stack['after_commit_id'] = $commit;
            $this->git_log_stack['current_commit_data'] = trim($this->active_branch_commit());
            proj_ctrl::new()->add_log($this->proj_id,$this->user_id,$this->git_log_stack,self::GIT_CMD_TYPE_RESET,$this->active_branch);
            return true;
        }
        return false;
    }

    /**
     * @api 所有分支名称
     * @return array
     */
    public function branch():array
    {
        $this->git_instance->update_remote();
        $output = $this->git_instance->all_branch_name();
        return $output;
    }

    //当前分支信息
    public function current_branch(): array
    {
        $result = [];
        $output = $this->git_instance->local_branch();

        foreach ($output as $value) {
            if (0 !== strpos($value, '*')) {
                continue;
            }

            $result = explode(' ', $value, 3);
            array_shift($result);
            break;
        }
        return $result;
    }



    //获取当前提交
    public function active_branch_commit():string
    {
        $curr = $this->current_branch();
        return $curr[1]??'';
    }

    //切换分支
    private function git_checkout(string $branch):bool
    {
        $this->git_instance->checkout($branch);
        $now = $this->current_branch();
        $now_branch = $now[0] ?? '';
        $now_commit = $now[1] ?? '';
        if ($this->active_branch != $now_branch){
            $after_commit_id = $this->git_instance->current_commit();
            $this->git_log_stack['after_commit_id'] = $after_commit_id;
            $this->git_log_stack['current_commit_data'] = trim($now_commit);
            proj_ctrl::new()->add_log($this->proj_id,$this->user_id,$this->git_log_stack,self::GIT_CMD_TYPE_CHECKOUT,$this->active_branch);
            return true;
        }
        return false;
    }

    //更新分支
    private function git_pull():bool
    {
        $before_commit_id = $this->git_instance->current_commit();
        $this->git_instance->pull($this->active_branch);
        $after_commit_id = $this->git_instance->current_commit();
        if ($before_commit_id != $after_commit_id){
            $this->git_log_stack['after_commit_id'] = $after_commit_id;
            $this->git_log_stack['current_commit_data'] = trim($this->active_branch_commit());
            proj_ctrl::new()->add_log($this->proj_id,$this->user_id,$this->git_log_stack,self::GIT_CMD_TYPE_PULL,$this->active_branch);
            return true;
        }
        return false;
    }

    //保存备份文件
    private function stash_file(): void
    {
        if (empty($this->copy_files)){
            return;
        }
        foreach ($this->copy_files as $item) {
            $file_path = $this->local_path . DIRECTORY_SEPARATOR . trim($item, " /\\\t\n\r\0\x0B");

            if (!is_file($file_path)) {
                continue;
            }

            $temp_name = hash('sha1', uniqid(mt_rand(), true));
            $copy_path = self::TEMP_PATH . DIRECTORY_SEPARATOR . $temp_name;

            copy($file_path, $copy_path);

            $this->stash_files[] = [
                'source' => $file_path,
                'dest'   => $copy_path
            ];
        }
    }

    //恢复备份文件
    private function apply_file(): void
    {
        if (empty($this->stash_files)) {
            return;
        }

        //copy files
        foreach ($this->stash_files as $item) {
            rename($item['dest'], $item['source']);
        }
    }
}