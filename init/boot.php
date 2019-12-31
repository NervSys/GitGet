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

namespace init;

use ext\conf;
use init\lib\input_escape;

/**
 * Class boot
 *
 * @package init
 */
class boot
{
    //ENV default value
    public static $env = 'prod';

    /**
     * 加载环境配置
     */
    public function env()
    {
        //get new .env
        if (is_file($env_file = ROOT . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . '.env')) {
            $env_conf = trim(file_get_contents($env_file));

            if (in_array($env_conf, ['prod', 'test', 'dev'])) {
                self::$env = &$env_conf;
            }
        }

        //Load conf
        conf::load('conf', self::$env);
    }

    /**
     * 数据预备
     */
    public function prep()
    {
        //全局过滤
        input_escape::new();
    }
}