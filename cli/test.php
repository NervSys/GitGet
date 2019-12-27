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
use ext\file;

class test
{
    public $tz         = '*';
    public $local_path = "D:\shared\GitGet";
    public $proj_id    = 1;

    const TEMP_PATH = ".git" . DIRECTORY_SEPARATOR . 'temp';

    public function go()
    {
        $item       = 'conf';
        $path_from  = $this->local_path . DIRECTORY_SEPARATOR . trim($item, " /\\\t\n\r\0\x0B");
        $path_temp  = self::TEMP_PATH . DIRECTORY_SEPARATOR . $this->proj_id;
        $path_local = $path_temp . DIRECTORY_SEPARATOR . $item;
        $path_to    = $this->local_path . DIRECTORY_SEPARATOR . file::get_path($path_local, $this->local_path);
        echo $path_from . "\n";
        echo $path_to;
    }

    public function test()
    {
        $dir = $this->local_path . DIRECTORY_SEPARATOR . "logs";
        dir_handle::new()->del_dir($dir);
    }
}