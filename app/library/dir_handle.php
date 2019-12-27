<?php
/**
 * Created by PhpStorm.
 * User: liu
 * Date: 2019/12/27
 * Time: 11:30
 * Note: dir.php
 */

namespace app\library;


use ext\factory;

class dir_handle extends factory
{
    /**
     * 复制文件
     *
     * @param $from_file
     * @param $to_file
     */
    public function copy_file($from_file, $to_file)
    {
        $folder1 = opendir($from_file);
        while ($f1 = readdir($folder1)) {
            if ($f1 != "." && $f1 != "..") {
                $path2 = $from_file . DIRECTORY_SEPARATOR . $f1;
                if (is_file($path2)) {
                    $file     = $path2;
                    $new_file = $to_file . DIRECTORY_SEPARATOR . $f1;
                    copy($file, $new_file);
                } elseif (is_dir($path2)) {
                    $to_files = $to_file . DIRECTORY_SEPARATOR . $f1;
                    $this->copy_file($path2, $to_files);
                }
            }
        }
    }

    /**
     * 删除文件夹
     *
     * @param $path
     */
    public function del_dir($path)
    {
        $last = substr($path, -1);
        if ($last !== '/') {
            $path .= '/';
        }
        if (is_dir($path)) {
            $p = scandir($path);
            foreach ($p as $val) {
                if ($val != "." && $val != "..") {
                    if (is_dir($path . $val)) {
                        $this->del_dir($path . $val . '/');
                        @rmdir($path . $val);
                    } else {
                        chmod($path . $val, 0777);
                        unlink($path . $val);
                    }
                }
            }
            if (is_dir($path)){
                @rmdir($path);
            }
        }
    }
}