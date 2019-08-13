<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 5:15 PM
 * Note: project.php
 */

namespace app\model;

use app\library\model;

class user extends model
{
    public function get_user_info(int $user_id)
    {
        return $this->where(['user_id',$user_id])->field('*')->get_one();
    }

    public function get_user_key(int $user_id, string $key)
    {
        return $this->where(['user_id', $user_id])->field($key)->get_value();
    }

    public function get_user(int $user_id, array $info_keys)
    {
        $fields = implode(',', $info_keys);
        return $this->field($fields)->where(['user_id', $user_id])->get_one();
    }
}