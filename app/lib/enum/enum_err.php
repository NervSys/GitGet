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

namespace app\lib\enum;


use ext\errno;

class enum_err
{
    const SUCCESS = '200-OK';

    const BRANCH_NO_CHECK  = '1001-已在当前分支';
    const BRANCH_NO_EXIST  = '1002-该分支已经不存在';
    const SETTING_PATH     = '1003-请先设置项目根目录';
    const CLONE_SELF_ERROR = '1004-该项目无法设置保护文件';
    const TIMING_ERROR     = '1005-请选择一个未来的时间';
    const PRE_INIT         = '1006-请先执行init.sh脚本';

    public static function get_code($msg)
    {
        $arr = explode('-', $msg);
        return [
            'code' => $arr[0],
            'msg'  => $arr[1]
        ];
    }
}