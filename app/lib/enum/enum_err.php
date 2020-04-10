<?php
/**
 * Created by PhpStorm.
 * User: 25791
 * Date: 2020/4/7
 * Time: 15:01
 * Note: enum_err.php
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