<?php
/**
 * Created by PhpStorm.
 * User: liu
 * Date: 2019/12/27
 * Time: 11:30
 * Note: test.php
 */

namespace cli;


use app\library\dir_handle;

class test
{
    public $tz         = '*';
    public $local_path = 'D:\shared\GitGet';

    public function go()
    {
        $a = $this->local_path . DIRECTORY_SEPARATOR . "conf";
        $b = $this->local_path . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "logs";
        dir_handle::new()->copy_to($a, $b);
    }
}