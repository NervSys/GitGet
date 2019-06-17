<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 12:58 PM
 * Note: install.php
 */

namespace db;

use ext\file;
use app\library\model;

class install extends model
{
    const SQL_PATH = __DIR__ . '/pending';

    public function __construct()
    {
        parent::__construct();

        $files = file::get_list(self::SQL_PATH, '*.sql', true);

        foreach ($files as $file) {
            $ret  = $this->exec(file_get_contents($file));
            $name = basename($file);

            echo -1 !== $ret
                ? '"' . $name . '" import succeed!'
                : '"' . $name . '" import FAILED!!!';

            echo PHP_EOL;
        }
    }
}