<?php

namespace app\git;

use ext\conf;
use core\handler\factory;

class cmd extends factory
{
    private $git_cmd = [];

    public function __construct(string $git_url, string $local_path)
    {
        conf::load('app/git', 'git_cmd');
        
        $this->git_cmd = conf::get('cmd');

        if (!is_dir($local_path)) {
            mkdir($local_path, 0777, true);
            chmod($local_path, 0777);

            exec($this->build_cmd($this->git_cmd['clone'], $git_url, $local_path), $output);
        }

        chdir($local_path);
    }


    public function pull(string $branch): array
    {
        exec($this->build_cmd($this->git_cmd['pull'], $branch), $output);

        return $output;
    }


    public function reset(string $commit): array
    {
        exec($this->build_cmd($this->git_cmd['reset'], $commit), $output);

        return $output;
    }

    public function clean(): array
    {
        exec($this->build_cmd($this->git_cmd['clean']), $output);

        return $output;
    }

    public function delete(string $branch): array
    {
        exec($this->build_cmd($this->git_cmd['delete'], $branch), $output);

        return $output;
    }


    public function checkout(string $branch): array
    {
        exec($this->build_cmd($this->git_cmd['checkout'], $branch), $output);

        return $output;
    }

    public function stash_save(): array
    {
        exec($this->build_cmd($this->git_cmd['stash_save']), $output);

        return $output;
    }

    public function stash_apply(): array
    {
        exec($this->build_cmd($this->git_cmd['stash_apply']), $output);

        return $output;
    }

    public function local_branch(): array
    {
        exec($this->build_cmd($this->git_cmd['local_branch']), $output);

        return $output;
    }

    public function remote_branch(): array
    {
        exec($this->build_cmd($this->git_cmd['remote_branch']), $output);

        return $output;
    }

    public function set_config(string $name, string $email): array
    {
        exec($this->build_cmd($this->git_cmd['set_name'], $name), $output);
        exec($this->build_cmd($this->git_cmd['set_email'], $email), $output);

        return $output;
    }


    private function build_cmd(string $cmd, string ...$params): string
    {
        return escapeshellcmd(sprintf($cmd, ...$params));
    }
}