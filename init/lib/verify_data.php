<?php
/**
 * Git Remote Deploy
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

namespace init\lib;

use core\lib\std\pool;
use ext\core;
use ext\errno;
use ext\factory;
use ext\log;

class verify_data extends factory
{
    //签名密钥
    const API_KEY = '28S4KStjTR0j8zSreJZc';

    //不参与生成sign的字段
    const VERIFY_ESCAPE = ['s', 'v', 'user_id', 'user_acc', 'zhj_token'];

    //验证中间数据
    private $verify_data = [];

    /**
     * 数据验签
     */
    public function __construct()
    {
        /** @var \core\lib\std\pool $unit_pool */
        $unit_pool = \core\lib\stc\factory::build(pool::class);

        //接收参数
        $input_data = $unit_pool->data;

        //签名参数丢失
        if (!isset($input_data['s']) || !isset($input_data['t'])) {
            errno::set('401', 1, '数据丢失，请核实数据包是否完整');
            http_response_code(500);
            core::stop();
        }

        $input_data['s'] = (string)$input_data['s'];
        $input_data['t'] = (string)$input_data['t'];

        $skip_char = substr($input_data['t'], -1, 1);
        $key_arr   = str_split(self::API_KEY);

        unset($key_arr[(int)$skip_char]);

        $pass_result = $this->sign_validate(implode($key_arr), $input_data);

        if (!$pass_result) {
            //记录验签数据
            $ext_log = log::new('verify');

            //增加ip和ua记录
            $this->verify_data['cmd'] = $unit_pool->cmd;
            $this->verify_data['ip']  = $unit_pool->ip;
            $this->verify_data['ua']  = $_SERVER['HTTP_USER_AGENT'] ?? 'NONE';

            //写入日志
            $ext_log->add($this->verify_data)->save();

            //报错终止
            errno::set('402', 1, '数据错误，请核实数据包是否完整');
            http_response_code(500);
            core::stop();
        }

        return $pass_result;
    }

    /**
     * 检查数据签名
     *
     * @param string $key
     * @param array  $data
     *
     * @return bool
     */
    private function sign_validate(string $key, array $data): bool
    {
        //Get all needed fields
        $fields = array_diff_key($data, array_flip(self::VERIFY_ESCAPE));

        //remove array content to avoid error
        foreach ($fields as $k => $v) {
            if (is_array($v) || is_object($v)) {
                unset($fields[$k]);
            }
        }

        //Add API_KEY
        $fields['k'] = &$key;

        //Sort
        ksort($fields);

        $string = implode($fields);
        $hash   = hash('md5', $string);

        //记录到验证池
        $this->verify_data = [
            'recv_sign'    => &$data['s'],
            'local_hash'   => &$hash,
            'local_string' => &$string,
            'input_data'   => &$data,
            'verify_data'  => &$fields
        ];

        //返回比对结果
        return $hash === $data['s'];
    }
}