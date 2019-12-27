<?php
/**
 * Created by PhpStorm.
 * User: liu
 * Date: 2019/12/27
 * Time: 11:30
 * Note: test.php
 */

namespace cli;


class test
{
    public $tz         = '*';
    public $local_path = 'D:\shared\GitGet';

    public function go()
    {
        mkdir($this->local_path . '\logs\test\test1',0777,true);
    }
}