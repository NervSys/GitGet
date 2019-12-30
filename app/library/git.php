<?php

namespace app\library;

use app\model\project;
use ext\conf;
use ext\factory;
use ext\redis;

class git extends factory
{
    public $proj_id;
    public $copy_files;
    public $local_path;
    public $stash_files;
    public $path_temp;
    const TEMP_PATH = ".git" . DIRECTORY_SEPARATOR . 'temp';

    public function __construct(int $proj_id)
    {
        $this->proj_id    = $proj_id;
        $conf             = project::new()->where(['proj_id', $proj_id])->get_one();
        $git_url          = $conf['proj_git_url'];
        $local_path       = $conf['proj_local_path'];
        $this->local_path = $local_path;
        $this->copy_files = json_decode($conf['proj_backup_files'], true);

        if (!is_dir($local_path)) {
            mkdir($local_path, 0777, true);
            chmod($local_path, 0777);
        }
        if (!is_dir($local_path . DIRECTORY_SEPARATOR . '.git')) {
            $this->execute($this->build_cmd('git clone --recursive %s %s', $git_url, $local_path), $output);
        }
        chdir($local_path);
    }

    /**
     * @return array
     */
    public function pull()
    {
        $this->stash_file();
        $this->clean();
        $this->checkout('.');
        $this->execute($this->build_cmd('git pull'), $output);
        $this->apply_file();
        return $output;
    }

    /**
     * @param string $commit
     *
     * @return array
     */
    public function reset(string $commit)
    {
        $this->stash_file();
        $this->clean();
        $this->checkout('.');
        $this->execute($this->build_cmd('git reset --hard %s', $commit), $output);
        $this->apply_file();
        return $output;
    }

    /**
     * @return array
     */
    public function clean(): array
    {
        $this->execute($this->build_cmd('git clean -df'), $output);
        return $output;
    }

    /**
     * @return array
     */
    public function status(): array
    {
        $this->execute($this->build_cmd('git status'), $output);
        return $output;
    }

    public function check(string $branch)
    {
        $this->stash_file();
        $this->clean();
        $this->checkout('.');
        $output = $this->checkout($branch);
        $this->apply_file();
        return $output;
    }

    /**
     * @param string $branch
     *
     * @return array
     */
    public function checkout(string $branch)
    {
        $this->execute($this->build_cmd('git checkout --force %s', $branch), $output);
        return $output;
    }

    /**
     * @return string
     */
    public function current_commit(): string
    {
        $this->execute($this->build_cmd('git rev-parse --short HEAD'), $output);
        return $output[0] ?? '';
    }

    /**
     * @return array
     */
    public function local_branch(): array
    {
        $this->execute($this->build_cmd('git branch -vv'), $output);
        return $output;
    }

    public function curr_branch()
    {
        $this->execute($this->build_cmd('git branch -vv'), $output);
        $result = [];
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

    public function branch_list()
    {
        $output = [];
        $this->execute($this->build_cmd('git branch -r'), $output);
        return $output;
    }

    public function curr_commit_id()
    {
        $this->execute($this->build_cmd('git rev-parse --short HEAD'), $output);
        return $output[0] ?? '';
    }

    private function stash_file(): void
    {
        if (empty($this->copy_files)) {
            return;
        }
        foreach ($this->copy_files as $item) {
            $path_from = $this->local_path . DIRECTORY_SEPARATOR . $item;
            $path_to   = $this->local_path . DIRECTORY_SEPARATOR . self::TEMP_PATH . DIRECTORY_SEPARATOR . $item;
            dir_handle::new()->copy_to($path_from, $path_to);

            $this->stash_files[] = [
                'source' => $path_from,
                'dest'   => $path_to
            ];
        }
    }

    private function apply_file(): void
    {
        if (empty($this->stash_files)) {
            return;
        }
        //copy files
        foreach ($this->stash_files as $item) {
            dir_handle::new()->copy_to($item['dest'], $item['source']);
        }
        dir_handle::new()->del_dir($this->local_path . DIRECTORY_SEPARATOR . self::TEMP_PATH);
    }

    /**
     * @param string $cmd
     * @param string ...$params
     *
     * @return string
     */
    private function build_cmd(string $cmd, string ...$params): string
    {
        return escapeshellcmd(sprintf($cmd, ...$params));
    }

    /**
     * @param $cmd
     * @param $output
     */
    private function execute($cmd, &$output)
    {
        exec($cmd . " 2>&1", $output, $res);
        if ($res != 0) {
            $output = is_array($output) ? json_encode($output) : $output;
            $this->gg_error($output);
        }
    }

    private function gg_error(string $error_msg)
    {
        $redis = redis::create(conf::get('redis'))->connect();
        $key   = 'gg_error:' . $this->proj_id;
        $redis->setex($key, 3600, $error_msg);
    }
}