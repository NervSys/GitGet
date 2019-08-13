<?php

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

    public static $table = [
        self::OK             => 'ok',
        self::SQL_ERROR      => '数据库错误',
        self::INVALID_PARAMS => '无效参数',
        self::SYSTEM_ERROR   => '系统错误',
        self::NO_USER        => '用户不存在'
    ] + [
        self::PW_ERROR         => '密码错误',
        self::TOKEN_MUST_EXIST => 'token必须存在',
    ];
}

