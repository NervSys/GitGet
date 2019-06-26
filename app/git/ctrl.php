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

class ctrl extends factory
{
    const PROJ_CONF_KEY = ['git_url', 'local_path', 'user_name', 'user_email'];

    const TEMP_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'temp';

    public $tz = 'deploy,get_status';

    private $local_path  = '';
    private $user_name   = '';
    private $user_email  = '';
    private $copy_files  = [];
    private $stash_files = [];

    private $git_instance = null;

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

        if (isset($conf['copy_files'])) {
            $this->copy_files = &$conf['copy_files'];
        }

        unset($conf);
    }

    /**
     * @param string $branch
     *
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
            $logs = $this->git_instance->checkout($branch);
        }

        $logs = array_merge($logs, $this->git_instance->pull($branch));

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

    /**
     * @return array
     */
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

    public function branch():array
    {
        $output = $this->git_instance->all_branch_name();
        return $output;
    }

    public function pull($branch):array
    {
        return $this->git_instance->pull($branch);
    }
    /**
     *
     */
    private function stash_file(): void
    {
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

    /**
     *
     */
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