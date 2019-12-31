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

namespace app\enum;
class error_enum
{
    const OK = 200;

    const SQL_ERROR      = 201;
    const INVALID_PARAMS = 202;
    const SYSTEM_ERROR   = 203;
    const NO_USER        = 204;

    const PW_ERROR         = 1001;
    const TOKEN_MUST_EXIST = 1002;
    const TOKEN_ERROR      = 1003;
    const BRANCH_NO_CHECK  = 1004;
    const BRANCH_NOT_EXIST = 1005;
    const SETTING_PATH     = 1006;
    const CLONE_SELF_ERROR = 1007;
    const TIMING_ERROR     = 1008;

    public static $table = [
        self::OK             => 'ok',
        self::SQL_ERROR      => '数据库错误',
        self::INVALID_PARAMS => '无效参数',
        self::SYSTEM_ERROR   => '系统错误',
        self::NO_USER        => '用户不存在'
    ] + [
        self::PW_ERROR         => '密码错误',
        self::TOKEN_MUST_EXIST => 'token必须存在',
        self::TOKEN_ERROR      => 'token错误,请重新登录！',
        self::BRANCH_NO_CHECK  => '已在当前分支',
        self::BRANCH_NOT_EXIST => '该分支已经不存在',
        self::SETTING_PATH     => '请先设置项目根目录',
        self::CLONE_SELF_ERROR => '该项目无法设置保护文件',
        self::TIMING_ERROR     => '请选择一个未来的时间',
    ];
}

