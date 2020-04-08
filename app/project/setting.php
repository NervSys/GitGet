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
            return system_setting::new()->value(['value' => $home_path])->where(['key', 'home_path'])->save();
        } else {
            return system_setting::new()->value(['key' => 'home_path', 'value' => $home_path])->add();
        }
    }

    /**
     * 设置信息
     *
     * @return array
     */
    public function info()
    {
        $keys = [
            "user_name",
            "user_email",
            "pri_key",
            "pub_key"
        ];
        return system_setting::new()->where([['key', $keys]])->fields('key', 'value')->get(\PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    /**
     * 初始配置
     *
     * @param string $user_name
     * @param string $user_email
     * @param string $pri_key
     * @param string $pub_key
     */
    public function initial(string $user_name, string $user_email, string $pri_key, string $pub_key)
    {
        $home_path = system_setting::new()->where(['key', 'home_path'])->fields('value')->get_val();
        if (empty($home_path)) {
            $this->fail(enum_err::PRE_INIT);
        }

        exec(escapeshellcmd('git config --global user.name "' . $user_name . '"'), $output);
        if (system_setting::new()->where(['key', 'user_name'])->exist()) {
            system_setting::new()->value(['value' => $user_name])->where(['key', 'user_name'])->save();
        } else {
            system_setting::new()->value(['key' => 'user_name', 'value' => $user_name])->add();
        }

        exec(escapeshellcmd('git config --global user.email "' . $user_email . '"'), $output);
        if (system_setting::new()->where(['key', 'user_email'])->exist()) {
            system_setting::new()->value(['value' => $user_email])->where(['key', 'user_email'])->save();
        } else {
            system_setting::new()->value(['key' => 'user_email', 'value' => $user_email])->add();
        }

        $path = $home_path . "/.ssh/id_rsa";
        file_put_contents($path, $pri_key);
        chmod($path, 0600);
        if (system_setting::new()->where(['key', 'pri_key'])->exist()) {
            system_setting::new()->value(['value' => $pri_key])->where(['key', 'pri_key'])->save();
        } else {
            system_setting::new()->value(['key' => 'pri_key', 'value' => $pri_key])->add();
        }


        $path = $home_path . "/.ssh/id_rsa.pub";
        file_put_contents($path, $pub_key);
        chmod($path, 0600);
        exec("ssh -T git@gitee.com");
        if (system_setting::new()->where(['key', 'pub_key'])->exist()) {
            system_setting::new()->value(['value' => $pub_key])->where(['key', 'pub_key'])->save();
        } else {
            system_setting::new()->value(['key' => 'pub_key', 'value' => $pub_key])->add();
        }
    }
}