<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:03 PM
 * Note: start.php
 */

namespace app;


use ext\conf;

class start
{
    /**
     * Add to system INIT section
     *
     * start constructor.
     */
    public function __construct()
    {
        conf::load('/', 'mysql');
        conf::set('openssl', ['conf' => ROOT . 'conf' . DIRECTORY_SEPARATOR . 'openssl.conf']);
    }
}