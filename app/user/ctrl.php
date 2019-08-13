<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:45 PM
 * Note: ctrl.php
 */

namespace app\user;

use app\enum\error_enum;
use app\library\base;
use app\library\base_func;
use app\library\error_code;
use app\model\base_model;
use app\model\user;

class ctrl extends base
{
    public $tz          = '*';
    public $check_token = false;

    /**
     * @param string $acc
     * @param string $pwd
     *
     * @return array
     * @throws \Exception
     * @api 登录
     */
    public function login(string $acc, string $pwd): array
    {
        $cnt = user::new()->count();
        if ($cnt == 0) {
            $this->make_user($acc, $pwd);
        }
        $user = user::new()->where(['user_acc', $acc])->field('*')->get_one();
        if (empty($user)) {
            return $this->response(error_enum::NO_USER);
        }
        if ($user['user_pwd'] != $this->get_pwd($pwd, $user['user_entry'])) {
            return $this->response(error_enum::PW_ERROR);
        }
        $token = $this->make(['user_id' => $user['user_id'], 'user_acc' => $user['user_acc']]);
        return $this->succeed(['gg_token' => $token]);
    }

    public function make_user($acc, $pwd)
    {
        $entry = $this->get_rand_str();
        $pwd   = $this->get_pwd($pwd, $entry);
        user::new()->value(['user_acc' => $acc, 'user_pwd' => $pwd, 'user_entry' => $entry])->insert_data();
    }

    public function get_pwd($pwd, $salt)
    {
        return md5(md5($pwd) . md5($salt));
    }

    public function get_rand_str($len = 6, $type = 'str')
    {
        if ($type == 'str') {
            $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        } else {
            $arr = array_merge(range(0, 9));
        }
        shuffle($arr);
        $sub_arr = array_slice($arr, 0, $len);
        return implode('', $sub_arr);
    }
}