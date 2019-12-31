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
use ext\factory;

/**
 * Class input_escape
 *
 * @package init\lib
 */
class input_escape extends factory
{
    //设置不过滤字段
    private $exclude = [
        'zhj_token'
    ];

    /**
     * input_escape constructor.
     */
    public function __construct()
    {
        /** @var \core\lib\std\pool $unit_pool */
        $unit_pool = \core\lib\stc\factory::build(pool::class);
        $this->escape($unit_pool, $unit_pool->data);
    }

    /**
     * 过滤输入
     *
     * @param \core\lib\std\pool $unit_pool
     * @param array              $input
     */
    private function escape(pool $unit_pool, array $input): void
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $this->escape($unit_pool, $value);
                continue;
            }

            if (in_array($key, $this->exclude, true)) {
                continue;
            }

            if (is_object($value)) {
                continue;
            }

            $unit_pool->data[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }
}