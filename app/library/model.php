<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:07 PM
 * Note: model.php
 */

namespace app\library;

use ext\conf;
use ext\crypt;
use ext\pdo_mysql;

class model extends pdo_mysql
{
    /**
     * model constructor.
     */
    public function __construct()
    {
        conf::load('/', 'mysql');
        conf::set('openssl', ['conf' => ROOT . 'conf' . DIRECTORY_SEPARATOR . 'openssl.conf']);
        $this->instance = $this->config(conf::get('mysql'))->connect()->get_pdo();
    }

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

        return $data['user_id'] ?? 0;
    }


}