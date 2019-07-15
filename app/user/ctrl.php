<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:45 PM
 * Note: ctrl.php
 */

namespace app\user;

use app\model\project;
use app\model\user;
use ext\conf;
use ext\crypt;
use ext\errno;
use ext\misc;

class ctrl
{
    public $tz = 'login,user_edit,delete_user';

    private $unit_crypt = null;

    public $root_acc = 'root';
    public $root_pwd = 'root';

    /**
     * ctrl constructor.
     */
    public function __construct()
    {
        errno::load('app', 'user');
        $this->unit_crypt = crypt::new(conf::get('openssl'));
    }

    /**
     * @api 登录
     * @param string $acc
     * @param string $pwd
     *
     * @return array
     * @throws \Exception
     */
    public function login(string $acc, string $pwd): array
    {
        $check_res = $this->check_root($acc,$pwd);
        if ($check_res !== false){
            return $check_res;
        }
        $user_data = user::new()
            ->field('user_id', 'user_uuid', 'user_acc', 'user_pwd', 'user_key')
            ->where(['user_uuid', misc::uuid($acc)])
            ->limit(1)
            ->get_one();

        if (empty($user_data)) {
            return errno::get(2003, 1);
        }

        $pass_verify = $this->unit_crypt->check_pwd($pwd, $user_data['user_key'], $user_data['user_pwd']);
        if (!$pass_verify) {
            return errno::get(2004, 1);
        }

        errno::set(2005);

        unset($user_data['user_key'], $user_data['user_pwd']);

        return [
            'name'  => $user_data['user_acc'],
            'token' => $this->unit_crypt->sign(json_encode($user_data))
        ];
    }

    public function user_edit(int $user_id, string $user_acc, string $user_pwd = '')
    {
        $data = [
            'user_uuid' => misc::uuid($user_acc),
            'user_acc'  => $user_acc,
            'add_time'  => time()
        ];
        if ($user_pwd) {
            $user_key         = $this->unit_crypt->get_key();
            $u_pwd            = $this->unit_crypt->hash_pwd($user_pwd, $user_key);
            $data['user_pwd'] = $u_pwd;
            $data['user_key'] = $user_key;
        }
        try {
            if ($user_id) {
                //更新
                user::new()
                    ->value($data)
                    ->where(['user_id', $user_id])
                    ->update();
            } else {
                //新增
                user::new()
                    ->value($data)
                    ->insert();
            }
        } catch (\PDOException $e) {
            return errno::get(2008, 1);
        }
        errno::set(2007);
        return [];
    }

    /**
     * @param int $user_id 用户id
     *
     * @return array
     * @api 删除用户
     */
    public function delete_user(int $user_id): array
    {
        if ($user_id == 1) return errno::get(2009, 1);
        $this->begin();
        try {
            user::new()->where(['user_id', $user_id])->delete();
        } catch (\PDOException $e) {
            $this->rollback();
            return errno::get(2008, 1);
        }
        $this->commit();
        return errno::get(2007, 0);
    }

    private function check_root($acc, $pwd)
    {
        if ($acc != $this->root_acc || $pwd != $this->root_pwd){
            return false;
        }
        $user_key = $this->unit_crypt->get_key();
        $user_data = [
            'user_id' => 0,
            'user_uuid' => misc::uuid($acc),
            'user_acc' => $acc,
            'user_pwd' => $pwd,
            'user_key' => $user_key
        ];
        errno::set(2005);
        return [
            'name'  => $user_data['user_acc'],
            'token' => $this->unit_crypt->sign(json_encode($user_data))
        ];
    }
}