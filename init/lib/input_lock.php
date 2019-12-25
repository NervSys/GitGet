<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 7/22/2019
 * Time: 5:49 PM
 * Note: input_lock.php
 */

namespace init\lib;

use core\lib\std\pool;
use core\lib\std\router;
use ext\conf;
use ext\core;
use ext\errno;
use ext\factory;
use ext\lock;
use ext\log;

class input_lock extends factory
{
    //需要上锁的cmd列表
    const LOCK_LIST = [
        '\app\points\points_ex-exchange',
        '\app\points\points_withdraw-withdraw',
    ];

    /**
     * input_lock constructor.
     */
    public function __construct()
    {
        /** @var \core\lib\std\pool $unit_pool */
        $unit_pool = \core\lib\stc\factory::build(pool::class);

        //无用户标识，不加锁
        if (!isset($unit_pool->data['user_id']) || 0 === $unit_pool->data['user_id']) {
            return;
        }

        /** @var \ext\lock $unit_redis_lock */
        $unit_redis_lock = lock::new(conf::get('redis'));

        //获取cmd执行列表
        $cmd_group = core::get_cmd_list();

        foreach ($cmd_group as $item) {
            if (!in_array($item, self::LOCK_LIST, true)) {
                continue;
            }
            if (!$unit_redis_lock->on($item . ':' . $unit_pool->data['user_id'], 600)) {
                errno::set('400', 1, '请求频繁，请稍后重试');
                http_response_code(500);
                core::stop();
            }
        }
    }
}
