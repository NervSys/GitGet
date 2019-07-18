<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 5:15 PM
 * Note: project.php
 */

namespace app\model;

use ext\conf;
use ext\crypt;

class user extends base_model
{
    /**
     * @return int
     * @throws \Exception
     */
    public function get_user_id(): int
    {
        if (!isset(parent::$data['token'])) {
            return 0;
        }

        $unit_crypt = crypt::new(conf::get('openssl'));

        $json_data = $unit_crypt->verify(parent::$data['token']);
        if ('' === $json_data || !is_array($data = json_decode($json_data, true))) {
            return 0;
        }
//var_dump($data);die;
        return $data['user_id'] ?? 0;
    }

    public function del_user(int $user_id){
        $this->where(['user_id',$user_id])->delete()->execute();
        return $this->last_affect();
    }
}