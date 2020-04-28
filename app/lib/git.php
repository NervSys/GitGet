<?php
/**
 * Git Get
 *
 * Copyright 2019-2020 leo <2579186091@qq.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace app\lib;

use ext\factory;
use ext\log;

class git extends factory
{
    public $output;

    /**
     * 清除未添加文件
     *
     * @return bool
     */
    public function clean(): bool
    {
        return $this->execute($this->build_cmd('git clean -df'), $this->output);
    }

    /**
     * 清理
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->execute($this->build_cmd('git checkout --force .'), $this->output);
    }

    /**
     * 克隆
     *
     * @param string $url
     * @param string $path
     *
     * @return bool
     */
    public function clone(string $url, string $path): bool
    {
        return $this->execute($this->build_cmd('git clone --recursive %s %s', $url, $path), $this->output);
    }

    /**
     * 更新
     *
     * @return bool
     */
    public function pull(): bool
    {
        return $this->execute($this->build_cmd('git pull'), $this->output);
    }

    /**
     * @param string $branch
     *
     * @return bool
     */
    public function checkout(string $branch): bool
    {
        if (!$this->execute($this->build_cmd('git checkout -b %s', $branch), $this->output)) {
            return $this->execute($this->build_cmd('git checkout --force %s', $branch), $this->output);
        }
        return true;
    }

    /**
     * 分支列表
     *
     * @return array
     */
    public function branch_list()
    {
        $res = [];
        $this->execute($this->build_cmd('git branch -r'), $res);
        return $res;
    }

    /**
     * 当前节点
     *
     * @return mixed|string
     */
    public function curr_commit_id()
    {
        $this->execute($this->build_cmd('git rev-parse --short HEAD'), $output);
        return $output[0] ?? '';
    }

    /**
     * 当前分支
     *
     * @return array
     */
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

    /**
     * 回滚
     *
     * @param string $commit
     *
     * @return bool
     */
    public function reset(string $commit): bool
    {
        return $this->execute($this->build_cmd('git reset --hard %s', $commit), $this->output);
    }

    /**
     * 执行
     *
     * @param $cmd
     * @param $output
     *
     * @return bool
     */
    private function execute($cmd, &$output)
    {
        exec($cmd . " 2>&1", $output, $res);
        log::new("exec")->add([$cmd, $output, $res])->save();
        if ($res != 0) {
            $output = is_array($output) ? json_encode($output) : $output;
            return false;
        }
        return true;
    }

    /**
     * build
     *
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