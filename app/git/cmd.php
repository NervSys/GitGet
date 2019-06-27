<?php

namespace app\git;

use ext\conf;
use core\handler\factory;

class cmd extends factory
{
    private $git_cmd = [];

    /**
     * cmd constructor.
     *
     * @param string $git_url
     * @param string $local_path
     */
    public function __construct(string $git_url, string $local_path)
    {
        conf::load('app/git', 'git_cmd');

        $this->git_cmd = conf::get('git_cmd');

        if (!is_dir($local_path)) {
            mkdir($local_path, 0777, true);
            chmod($local_path, 0777);
        }

        if (!is_dir($local_path . DIRECTORY_SEPARATOR . '.git')) {
            exec($this->build_cmd($this->git_cmd['clone'], $git_url, $local_path), $output);
        }

        chdir($local_path);
    }

    /**
     * @param string $branch
     *
     * @return array
     */
    public function pull(string $branch): array
    {
        exec($this->build_cmd($this->git_cmd['pull'], $branch, $branch), $output);

        return $output;
    }

    /**
     * @param string $commit
     *
     * @return array
     */
    public function reset(string $commit): array
    {
        exec($this->build_cmd($this->git_cmd['reset'], $commit), $output);

        return $output;
    }

    /**
     * @return array
     */
    public function fetch(): array
    {
        exec($this->build_cmd($this->git_cmd['fetch']), $output);

        return $output;
    }

    /**
     * @return array
     */
    public function clean(): array
    {
        exec($this->build_cmd($this->git_cmd['clean']), $output);

        return $output;
    }

    /**
     * @return array
     */
    public function status(): array
    {
        exec($this->build_cmd($this->git_cmd['status']), $output);

        return $output;
    }

    /**
     * @param string $branch
     *
     * @return array
     */
    public function delete(string $branch): array
    {
        exec($this->build_cmd($this->git_cmd['delete'], $branch), $output);

        return $output;
    }

    /**
     * @param string $branch
     *
     * @return array
     */
    public function checkout(string $branch): array
    {
        exec($this->build_cmd($this->git_cmd['checkout'], $branch), $output);

        return $output;
    }

    /**
     * @return string
     */
    public function current_commit() :string
    {
        exec($this->build_cmd($this->git_cmd['current_commit']), $output);

        return $output[0]??'';
    }

    /**
     * @return array
     */
    public function stash_save(): array
    {
        exec($this->build_cmd($this->git_cmd['stash_save']), $output);

        return $output;
    }

    /**
     * @return array
     */
    public function stash_apply(): array
    {
        exec($this->build_cmd($this->git_cmd['stash_apply']), $output);

        return $output;
    }

    /**
     * @return array
     */
    public function local_branch(): array
    {
        exec($this->build_cmd($this->git_cmd['local_branch']), $output);

        return $output;
    }

    /**
     * @return array
     */
    public function remote_branch(): array
    {
        exec($this->build_cmd($this->git_cmd['remote_branch']), $output);

        return $output;
    }

    /**
     * @return array
     */
    public function all_branch_name():array
    {
        exec($this->build_cmd($this->git_cmd['all_branch_name']), $output);

        return $output;
    }

    /**
     * @param string $name
     * @param string $email
     *
     * @return array
     */
    public function set_config(string $name, string $email): array
    {
        exec($this->build_cmd($this->git_cmd['set_name'], $name), $output);
        exec($this->build_cmd($this->git_cmd['set_email'], $email), $output);

        return $output;
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