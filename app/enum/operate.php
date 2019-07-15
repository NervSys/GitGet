<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/14
 * Time: 13:19
 * Note: operate.php
 */

namespace app\enum;
class operate
{
    const OPERATE_GET        = 1;       //查看列表
    const OPERATE_ADD        = 2;       //新增项目
    const OPERATE_EDIT       = 3;       //编辑项目
    const OPERATE_DEPLOY     = 4;       //部署
    const OPERATE_CHECKOUT   = 5;       //切换
    const OPERATE_PULL       = 6;       //更新
    const OPERATE_RESET      = 7;       //重置
    const OPERATE_GET_STATUS = 8;       //获取状态
}

