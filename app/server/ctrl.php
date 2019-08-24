<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:45 PM
 * Note: ctrl.php
 */

namespace app\server;

use app\enum\error_enum;
use app\library\base;
use app\library\model;
use app\model\project;
use app\model\server;
use app\model\system_setting;
use app\project\proj_git;

class ctrl extends base
{
    public $tz = '*';

    /**
     * 新增或编辑服务器
     *
     * @param string $srv_ip
     * @param string $srv_name
     * @param int    $srv_port
     * @param int    $srv_id
     *
     * @return array
     */
    public function addOrEdit(string $srv_ip, string $srv_name = '', int $srv_port = 80, int $srv_id = 0): array
    {
        $data = [
            'ip'       => $srv_ip,
            'port'     => $srv_port,
            'srv_name' => $srv_name,
        ];
        model::new()->begin();
        try {
            if ($srv_id == 0) {
                server::new()->value($data)->insert_data();
            } else {
                server::new()->value($data)->where(['srv_id', $srv_id])->update_data();
            }
            model::new()->commit();
        } catch (\PDOException $e) {
            model::new()->rollback();
            return $this->response(error_enum::SQL_ERROR);
        }
        return $this->succeed();
    }

    /**
     * 删除服务器
     *
     * @param int $srv_id 服务器id
     *
     * @return array
     */
    public function delete_serv(int $srv_id): array
    {
        $res = server::new()->where(['srv_id', $srv_id])->value(['status' => 2])->update_data();
        return $this->succeed();
    }

    public function system_setting(string $key, string $value)
    {
        $setting = [
            'key'   => $key,
            'value' => $value,
        ];
        if (system_setting::new()->where(['key', $key])->exist()) {
            system_setting::new()->value($setting)->where(['key', $key])->update_data();
        } else {
            system_setting::new()->value($setting)->insert_data();
        }
        $servers = server::new()->get();
        $data    = [
            'cmd'   => 'project/proj_git-setting_receive',
            'key'   => $key,
            'value' => $value
        ];
        $proj    = proj_git::new();
        foreach ($servers as $server) {
            $ip   = $server['ip'];
            $port = $server['port'];
            $url  = $ip . ":" . $port . "/api.php";
            $proj->curl_post($url, $data);
        }
        return $this->succeed();
    }
}