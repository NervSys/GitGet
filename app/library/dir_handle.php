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
    public function copy_to(string $path_from, string $path_to): bool
    {
        if (is_dir($path_from)) {
            return $this->dir_copy($path_from, $path_to);
        }
        if (is_file($path_from)) {
            return $this->file_copy($path_from, $path_to);
        }
        return false;
    }

    public function file_copy(string $file_from, string $file_to): bool
    {
        if (!file_exists($file_from)) {
            return false;
        }
        $file_name = basename($file_from);
        if (strpos($file_to, $file_name) !== false) {
            $file_to = substr($file_to, 0, strpos($file_to, $file_name));
        }
        $file_to = rtrim($file_to, " /\\\t\n\r\0\x0B");
        if (!file_exists($file_to)) {
            mkdir($file_to, 0777, true);
        }
        $file_to .= DIRECTORY_SEPARATOR . $file_name;
        return copy($file_from, $file_to);
    }


    public function dir_copy(string $dir_from, string $dir_to): bool
    {
        if (!file_exists($dir_from)) {
            return false;
        }
        $dir_name = basename($dir_from);
        if (strpos($dir_to, $dir_name) === false) {
            $dir_to = rtrim($dir_to, " /\\\t\n\r\0\x0B") . DIRECTORY_SEPARATOR . $dir_name;
        }
        if (!file_exists($dir_to)) {
            mkdir($dir_to, 0777, true);
        }
        $handle = opendir($dir_from);
        while (($item = readdir($handle)) !== false) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $path_from_1 = $dir_from . DIRECTORY_SEPARATOR . $item;
            $path_to_1   = $dir_to . DIRECTORY_SEPARATOR . $item;
            $this->copy_to($path_from_1, $path_to_1);
        }
        closedir($handle);
        return true;
    }

    /**
     * 删除文件夹
     *
     * @param $path
     *
     * @return bool
     */
    public function del_dir($path): bool
    {
        $last = substr($path, -1);
        if ($last !== '/') {
            $path .= '/';
        }
        if (is_dir($path)) {
            $paths = scandir($path);
            foreach ($paths as $val) {
                if ($val == '.' || $val == '..') {
                    continue;
                }
                if (is_dir($path . $val)) {
                    if (!$this->del_dir($path . $val . '/')) {
                        return false;
                    }
                } else {
                    chmod($path . $val, 0777);
                    unlink($path . $val);
                }
            }
        }
        if (!file_exists($path)) {
            return false;
        }
        if (rmdir($path)) {
            return true;
        }
        return false;
    }
}