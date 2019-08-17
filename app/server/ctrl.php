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
use app\model\base_model;
use app\model\server;
use ext\errno;

class ctrl extends base
{
    public $tz = 'addOrEdit,delete_serv';

    /**
     * 新增或编辑服务器
     * @param string $srv_ip
     * @param string $srv_name
     * @param int $port
     * @param int $srv_id
     * @return array
     */
    public function addOrEdit(string $srv_ip, string $srv_name = '', int $port = 80, int $srv_id = 0): array
    {
        $data = [
            'ip' => $srv_ip,
            'port' => $port,
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
     * @param int $srv_id 服务器id
     *
     * @return array
     * @api 删除服务器
     */
    public function delete_serv(int $srv_id): array
    {
        $res = server::new()->where(['srv_id', $srv_id])->value(['status' => 2])->update_data();
        return $this->succeed();
    }
}