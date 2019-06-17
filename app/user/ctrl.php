<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:45 PM
 * Note: ctrl.php
 */

namespace app\user;

use ext\conf;
use ext\crypt;
use ext\errno;
use ext\misc;
use app\library\model;

class ctrl extends model
{
    public $tz = 'init,login';

    private $unit_crypt = null;

    /**
     * ctrl constructor.
     */
    public function __construct()
    {
        parent::__construct();

        errno::load('app', 'user_ctrl');
        $this->unit_crypt = crypt::new(conf::get('openssl'));
    }

    /**
     * @return array
     */
    public function init(): array
    {
        $users = $this->select('user')->field('user_id')->fetch(true);

        if (!empty($users)) {
            return errno::get(2001, 1);
        }

        $user_acc = 'admin';
        $user_pwd = 'admin';

        $user_key = $this->unit_crypt->get_key();

        $init = $this->insert('user')
            ->value([
                'user_uuid' => misc::uuid($user_acc),
                'user_acc'  => $user_acc,
                'user_pwd'  => $this->unit_crypt->hash_pwd($user_pwd, $user_key),
                'user_key'  => $user_key,
                'add_time'  => time(),

            ])
            ->execute();

        return $init ? errno::get(2000) : errno::get(2001, 1);
    }

    /**
     * @param string $acc
     * @param string $pwd
     *
     * @return array
     * @throws \Exception
     */
    public function login(string $acc, string $pwd): array
    {
        $user_data = $this->select('user')
            ->field('user_id', 'user_uuid', 'user_acc', 'user_pwd', 'user_key')
            ->where(['user_uuid', misc::uuid($acc)])
            ->limit(1)
            ->fetch();

        if (empty($user_data)) {
            return errno::get(2003, 1);
        }

        $user_data = current($user_data);

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
}