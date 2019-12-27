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
        $str = '/data/wwwroot/admin.izhjapp.cn/.git/temp/3/app/library/base_sms.php';
        echo substr($str,0,strpos($str,basename($str)));
    }
}