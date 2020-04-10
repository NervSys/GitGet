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

namespace sql_db;

use app\lib\base;
use ext\file;

class install extends base
{
    public $tz = 'db';

    /**
     * 建表
     */
    public function db(): void
    {
        $sql_files = file::get_list(__DIR__ . '/sql', '*.sql');

        foreach ($sql_files as $file) {
            $sql = trim(file_get_contents($file));

            $file_name = basename($file);

            if ('' === $sql) {
                echo $file_name . ' is empty!';
                echo PHP_EOL;
                continue;
            }

            if (-1 < $this->mysql->exec($sql)) {
                echo $file_name . ' imported!';
            } else {
                echo $file_name . ' failed to import!';
            }

            echo PHP_EOL;
        }
    }
}