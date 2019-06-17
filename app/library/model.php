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
use ext\pdo_mysql;

class model extends pdo_mysql
{
    /**
     * model constructor.
     */
    public function __construct()
    {
        $this->instance = $this->config(conf::get('mysql'))->connect()->get_pdo();
    }
}