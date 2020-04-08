<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2/6/2020
 * Time: 11:28 AM
 * Note: install.php
 */

namespace sql_db;

use app\lib\base;
use ext\file;

class install extends base
{
    public $tz = 'db';

    /**
     * 建表
     */
    public function db(): void
    {
        $sql_files = file::get_list(__DIR__ . '/sql', '*.sql');

        foreach ($sql_files as $file) {
            $sql = trim(file_get_contents($file));

            $file_name = basename($file);

            if ('' === $sql) {
                echo $file_name . ' is empty!';
                echo PHP_EOL;
                continue;
            }

            if (-1 < $this->mysql->exec($sql)) {
                echo $file_name . ' imported!';
            } else {
                echo $file_name . ' failed to import!';
            }

            echo PHP_EOL;
        }
    }
}