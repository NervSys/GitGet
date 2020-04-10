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

namespace app\project;


use app\lib\api;
use app\lib\enum\enum_err;
use app\lib\model\setting as mod_set;

class setting extends api
{
    /**
     * 保存home目录地址
     *
     * @param $home_path
     *
     * @return bool
     */
    public function set_home_path($home_path)
    {
        if (mod_set::new()->where(['key', 'home_path'])->exist()) {
            return mod_set::new()->value(['value' => $home_path])->where(['key', 'home_path'])->save();
        } else {
            return mod_set::new()->value(['key' => 'home_path', 'value' => $home_path])->add();
        }
    }

    /**
     * 设置信息
     *
     * @return array
     */
    public function info()
    {
        $keys = [
            "user_name",
            "user_email",
            "pri_key",
            "pub_key"
        ];
        return mod_set::new()->where([['key', $keys]])->fields('key', 'value')->get(\PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    /**
     * 初始配置
     *
     * @param string $user_name
     * @param string $user_email
     * @param string $pri_key
     * @param string $pub_key
     */
    public function initial(string $user_name, string $user_email, string $pri_key, string $pub_key)
    {
        $home_path = mod_set::new()->where(['key', 'home_path'])->fields('value')->get_val();
        if (empty($home_path)) {
            $this->fail(enum_err::PRE_INIT);
        }

        exec(escapeshellcmd('git config --global user.name "' . $user_name . '"'), $output);
        if (mod_set::new()->where(['key', 'user_name'])->exist()) {
            mod_set::new()->value(['value' => $user_name])->where(['key', 'user_name'])->save();
        } else {
            mod_set::new()->value(['key' => 'user_name', 'value' => $user_name])->add();
        }

        exec(escapeshellcmd('git config --global user.email "' . $user_email . '"'), $output);
        if (mod_set::new()->where(['key', 'user_email'])->exist()) {
            mod_set::new()->value(['value' => $user_email])->where(['key', 'user_email'])->save();
        } else {
            mod_set::new()->value(['key' => 'user_email', 'value' => $user_email])->add();
        }

        $path = $home_path . "/.ssh/id_rsa";
        file_put_contents($path, $pri_key);
        chmod($path, 0600);
        if (mod_set::new()->where(['key', 'pri_key'])->exist()) {
            mod_set::new()->value(['value' => $pri_key])->where(['key', 'pri_key'])->save();
        } else {
            mod_set::new()->value(['key' => 'pri_key', 'value' => $pri_key])->add();
        }


        $path = $home_path . "/.ssh/id_rsa.pub";
        file_put_contents($path, $pub_key);
        chmod($path, 0600);
        exec("ssh -T git@gitee.com");
        if (mod_set::new()->where(['key', 'pub_key'])->exist()) {
            mod_set::new()->value(['value' => $pub_key])->where(['key', 'pub_key'])->save();
        } else {
            mod_set::new()->value(['key' => 'pub_key', 'value' => $pub_key])->add();
        }
    }
}