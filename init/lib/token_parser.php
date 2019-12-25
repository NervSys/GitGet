<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 11/1/2019
 * Time: 3:07 PM
 * Note: token_parser.php
 */

namespace init\lib;

use app\enum\cache_key;
use core\lib\std\pool;
use ext\conf;
use ext\core;
use ext\crypt;
use ext\errno;
use ext\factory;
use ext\redis;

class token_parser extends factory
{
    //用户标识键
    const ID_KEY = 'zhj_token';
    const LIFE   = 86400 * 7;

    /**
     * token_parser constructor.
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        /** @var \core\lib\std\pool $unit_pool */
        $unit_pool = \core\lib\stc\factory::build(pool::class);
        //无用户标识，重置用户token区信息
        if (!isset($unit_pool->data[self::ID_KEY])) {
            errno::set('216', 1, 'token不能为空');
            core::stop();
        }

        /** @var \ext\crypt $unit_crypt */
        $unit_crypt = crypt::create(conf::get('crypt'));

        //解析token
        $token_data = $unit_crypt->verify($unit_pool->data[self::ID_KEY]);
        if ('' === $token_data) {
            errno::set('212', 1, '登录超时');
            core::stop();
        }
        $user_data = json_decode($token_data, true);
        //无用户标识，重置用户token区信息
        if (!is_array($user_data) || !isset($user_data['user_id'])) {
            errno::set('212', 1, '登录超时');
            core::stop();
        }

        $redis      = redis::create(conf::get('redis'))->connect();
        if (!empty($unit_pool->data['deal'])) {
            $token_key  = cache_key::DEAL_TOKEN . $user_data['user_id'];
        }else{
            $token_key  = cache_key::TOKEN . $user_data['user_id'];
        }
        $token_hash = $redis->get($token_key);
        //fake token or expired
        if (false === $token_hash) {
            errno::set('212', 1, '登录超时');
            core::stop();
        }
        //login in another place
        if ($token_hash !== hash('md5', $unit_pool->data[self::ID_KEY])) {
            errno::set('213', 1, '您已在别处登录，请重新登录');
            core::stop();
        }

        //set new ttl to token
        $redis->expire($token_key, self::LIFE);

        //填充token信息
        $unit_pool->data['user_id']  = $user_data['user_id'];
        $unit_pool->data['user_acc'] = $user_data['user_acc'];
    }
}