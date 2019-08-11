<?php

namespace app\library;

use core\handler\factory;

class git extends factory
{
    /**
     * @param string $branch
     *
     * @return array
     */
    public function pull(string $branch): array
    {
        exec($this->build_cmd('git pull --force origin %s:%s', $branch, $branch), $output);
        return $output;
    }

    /**
     * @param string $commit
     *
     * @return array
     */
    public function reset(string $commit): array
    {
        exec($this->build_cmd('git reset --hard %s', $commit), $output);
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

    /**
     * @param string $cmd
     * @param string ...$params
     * @return string
     */
    private function build_cmd(string $cmd, string ...$params): string
    {
        return escapeshellcmd(sprintf($cmd, ...$params));
    }
}