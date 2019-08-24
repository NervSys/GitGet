<?php

namespace app\library;

use app\model\project;
use core\handler\factory;
use core\helper\log;
use ext\conf;

class git extends factory
{
    public $proj_id;
    public $copy_files;
    public $local_path;
    public $stash_files;
    const TEMP_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'temp';

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
            exec($this->build_cmd('git clone --recursive %s %s', $git_url, $local_path), $output);
        }
        chdir($local_path);
    }

    /**
     * @return array
     */
    public function pull(): array
    {
        $this->stash_file();
        $this->clean();
        $this->checkout('.');
        exec($this->build_cmd('git pull'), $output);
        $this->apply_file();
        return $output;
    }

    /**
     * @param string $commit
     *
     * @return array
     */
    public function reset(string $commit): array
    {
        $this->stash_file();
        $this->clean();
        $this->checkout('.');
        exec($this->build_cmd('git reset --hard %s', $commit), $output);
        $this->apply_file();
        return $output;
    }

    /**
     * @return array
     */
    public function clean(): array
    {
        exec($this->build_cmd('git clean -dfx'), $output);
        return $output;
    }

    /**
     * @return array
     */
    public function status(): array
    {
        exec($this->build_cmd('git status'), $output);
        return $output;
    }

    /**
     * @param string $branch
     *
     * @return array
     */
    public function checkout(string $branch): array
    {
        exec($this->build_cmd('git checkout --force %s', $branch), $output);
        return $output;
    }

    /**
     * @return string
     */
    public function current_commit(): string
    {
        exec($this->build_cmd('git rev-parse --short HEAD'), $output);
        return $output[0] ?? '';
    }

    /**
     * @return array
     */
    public function local_branch(): array
    {
        exec($this->build_cmd('git branch -vv'), $output);
        return $output;
    }

    public function curr_branch()
    {
        exec($this->build_cmd('git branch -vv'), $output);
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
        exec($this->build_cmd('git branch -r'), $output);
        return $output;
    }

    public function curr_commit_id()
    {
        exec($this->build_cmd('git rev-parse --short HEAD'), $output);
        return $output[0] ?? '';
    }

    private function stash_file(): void
    {
        if (empty($this->copy_files)) {
            return;
        }
        foreach ($this->copy_files as $item) {
            $file_path = $this->local_path . DIRECTORY_SEPARATOR . trim($item, " /\\\t\n\r\0\x0B");

            if (!is_file($file_path)) {
                continue;
            }
            $copy_path = self::TEMP_PATH . DIRECTORY_SEPARATOR;
            if (!is_dir($copy_path)) {
                mkdir($copy_path, 0777, true);
                chmod($copy_path, 0777);
            }
            $temp_name = hash('sha1', uniqid(mt_rand(), true));
            $copy_path .= $temp_name;
            copy($file_path, $copy_path);

            $this->stash_files[] = [
                'source' => $file_path,
                'dest'   => $copy_path
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
            rename($item['dest'], $item['source']);
        }
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
}