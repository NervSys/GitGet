<?php
/**
 * Created by PhpStorm.
 * User: 25791
 * Date: 2020/4/8
 * Time: 14:48
 * Note: setting.php
 */

namespace app\project;


use app\lib\api;
use app\lib\enum\enum_err;
use app\lib\model\system_setting;

class setting extends api
{
    /**
     * 保存home目录地址
     *
     * @param $home_path
     *
     * @return bool
     */
    public function set_home_path($home_path)
    {
        if (system_setting::new()->where(['key', 'home_path'])->exist()) {
            return system_setting::new()->value(['value', $home_path])->where(['key', 'home_path'])->save();
        } else {
            return system_setting::new()->value(['value', $home_path])->add();
        }
    }

    /**
     * 系统设置
     *
     * @param string $user_name
     * @param string $user_email
     * @param string $pri_key
     * @param string $pub_key
     */
    public function system_setting(string $user_name, string $user_email, string $pri_key, string $pub_key)
    {
        $home_path = system_setting::new()->where(['key', 'home_path'])->fields('value')->get_val();
        if (empty($home_path)) {
            $this->fail(enum_err::PRE_INIT);
        }

        system_setting::new()->where(['key', 'user_name'])->value(['value' => $user_name])->save();
        exec(escapeshellcmd('git config --global user.name "' . $user_name . '"'), $output);

        system_setting::new()->where(['key', 'user_email'])->value(['value' => $user_email])->save();
        exec(escapeshellcmd('git config --global user.email "' . $user_email . '"'), $output);

        $path = $home_path . "/.ssh/id_rsa";
        file_put_contents($path, $pri_key);
        chmod($path, 0600);
        system_setting::new()->where(['key', 'pri_key'])->value(['value' => $pri_key])->save();


        $path = $home_path . "/.ssh/id_rsa.pub";
        file_put_contents($path, $pub_key);
        chmod($path, 0600);
        exec("ssh -T git@gitee.com");
        system_setting::new()->where(['key', 'pub_key'])->value(['value' => $pub_key])->save();
    }
}