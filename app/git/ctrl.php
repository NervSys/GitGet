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
        'before_commit_id'=>'',
        'after_commit_id'=>'',
        'current_commit_data'=>'',
        'log_json' => '',
        'log_desc'=>''
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

        if (isset($conf['proj_backup_files'])) {
            $this->copy_files = json_decode($conf['proj_backup_files'],true);
        }

        unset($conf);
    }

    /**
     * @api 部署为某分支
     * @param string $branch
     * @return array
     */
    public function deploy(string $branch): array
    {
        //get current branch
        $curr = $this->current_branch();

        //Cannot find current branch
        if (empty($curr)) {
            return errno::get(1001, 1);
        }

        //current branch info
        list($curr_branch, $curr_commit) = $curr;

        //stash copy files
        $this->stash_file();

        //checkout branch
        $logs = [];
        if ($curr[0] !== $branch) {
            $logs = $this->git_checkout($curr_branch,$branch,$curr_branch."切换到".$branch);
        }

        $logs = array_merge($logs, $this->git_pull($branch,$curr_branch."切换到".$branch."并更新"));

        $this->apply_file();

        //get current branch
        $now = $this->current_branch();

        //Cannot find current branch
        if (empty($now)) {
            return errno::get(1001, 1);
        }

        //current branch info
        list($now_branch, $now_commit) = $now;

        errno::set(1000);

        return [
            'branch' => $curr_branch . ' => ' . $now_branch,
            'commit' => $curr_commit . ' => ' . $now_commit,
            'logs'   => $logs
        ];
    }

    /**
     * @api 更新分支
     * @param string $branch
     * @return array
     */
    public function pull():array
    {
        $curr_branch = $this->active_branch_name();
        $this->stash_file();
        $logs = $this->git_pull($curr_branch,$curr_branch.'更新');
        $this->apply_file();
        errno::set(1000);
        return $logs;
    }

    public function reset(string $commit):array
    {
        $this->stash_file();
        $before_commit_id = $this->git_instance->current_commit();
        $log = $this->git_instance->reset($commit);
        if ($before_commit_id != $commit){
            $this->git_log_stack['before_commit_id'] = $before_commit_id;
            $this->git_log_stack['after_commit_id'] = $commit;
            $curr_branch = $this->current_branch();
            list($curr_branch_name,$curr_branch_data) = $curr_branch;
            $this->git_log_stack['current_commit_data'] = trim($curr_branch_data);
            $this->git_log_stack['log_json'] = json_encode($log);
            $this->git_log_stack['log_desc'] = $curr_branch_name."节点重置";
            proj_ctrl::new()->add_log($this->proj_id,$this->user_id,$this->git_log_stack,self::GIT_CMD_TYPE_RESET,$curr_branch_name);
        }
        $this->apply_file();
        errno::set(1000);
        return $log;
    }

    /**
     * @api 分支状态
     * @return array
     */
    public function get_status(): array
    {
        $curr = $this->current_branch();
        $logs = $this->git_instance->status();

        list($curr_branch, $curr_commit) = $curr;

        errno::set(1000);

        return [
            'branch' => $curr_branch,
            'commit' => $curr_commit,
            'logs'   => $logs
        ];
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

    //所有分支名称
    public function branch():array
    {
        $output = $this->git_instance->all_branch_name();
        return $output;
    }

    //获取当前分支名称
    public function active_branch_name():string
    {
        $curr = $this->current_branch();
        return $curr[0]??'';
    }

    //获取当前提交
    public function active_branch_commit():string
    {
        $curr = $this->current_branch();
        return $curr[1]??'';
    }

    //切换分支
    private function git_checkout(string $curr_branch, string $branch,string $desc):array
    {
        $before_commit_id = $this->git_instance->current_commit();
        $log = $this->git_instance->checkout($branch);
        $after_commit_id = $this->git_instance->current_commit();
        if ($before_commit_id != $after_commit_id){
            $this->git_log_stack['before_commit_id'] = $before_commit_id;
            $this->git_log_stack['after_commit_id'] = $after_commit_id;
            $this->git_log_stack['current_commit_data'] = trim($this->active_branch_commit());
            $this->git_log_stack['log_json'] = json_encode($log);
            $this->git_log_stack['log_desc'] = $desc;
            proj_ctrl::new()->add_log($this->proj_id,$this->user_id,$this->git_log_stack,self::GIT_CMD_TYPE_CHECKOUT,$curr_branch);
        }
        return $log;
    }

    //更新分支
    private function git_pull(string $branch,string $desc):array
    {
        $before_commit_id = $this->git_instance->current_commit();
        $log = $this->git_instance->pull($branch);
        $after_commit_id = $this->git_instance->current_commit();
        if ($before_commit_id != $after_commit_id){
            $this->git_log_stack['before_commit_id'] = $before_commit_id;
            $this->git_log_stack['after_commit_id'] = $after_commit_id;
            $this->git_log_stack['current_commit_data'] = trim($this->active_branch_commit());
            $this->git_log_stack['log_json'] = json_encode($log);
            $this->git_log_stack['log_desc'] = $desc;
            proj_ctrl::new()->add_log($this->proj_id,$this->user_id,$this->git_log_stack,self::GIT_CMD_TYPE_PULL,$branch);
        }
        return $log;
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