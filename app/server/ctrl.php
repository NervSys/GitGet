<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:45 PM
 * Note: ctrl.php
 */

namespace app\server;

use app\model\base_model;
use app\model\server;
use ext\errno;

class ctrl extends base_model
{
    public $tz = 'addOrEdit';

    /**
     * ctrl constructor.
     */
    public function __construct()
    {
        parent::__construct();
        errno::load('app', 'server');
    }

    /**
     * @param string $srv_ip
     * @param string $srv_name
     * @param int $srv_port
     * @param string $srv_desc
     * @param int $srv_id
     *
     * @return array
     * @api 新增或编辑服务器
     */
    public function addOrEdit(string $srv_ip, string $srv_name, int $srv_port=80, string $srv_desc='', int $srv_id = 0): array
    {
        $data = [
            'srv_ip' => $srv_ip,
            'srv_name' => $srv_name,
            'srv_port' => $srv_port,
            'srv_desc' => $srv_desc,
        ];
        $this->begin();
        try {
            if ($srv_id == 0) {
                //新增
                server::new()->addSrv($data);
            } else {
                server::new()->updateSrv($data, ['srv_id', $srv_id]);
            }
            $this->commit();
        } catch (\PDOException $e) {
            $this->rollback();
            $err = $e->getMessage();
            errno::set(3003, 1);
            return ['err' => $err];
        }
        return errno::get(3002);
    }

    /**
     * @param int $srv_id 服务器id
     *
     * @return array
     * @api 删除服务器
     */
    public function delete_serv(int $srv_id): array
    {
        $affect_rows = server::new()->del_serv($srv_id);
        if ($affect_rows > 0) {
            return errno::get(3002, 0);
        } else {
            return errno::get(3003, 1);
        }

    }
}