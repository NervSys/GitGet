<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:45 PM
 * Note: ctrl.php
 */

namespace app\server;

use app\model\server;
use app\model\user;
use ext\errno;

class show
{
    public $tz = 'serv_list,serv_detail';


    /**
     * @api 服务器列表
     * @param int    $page
     * @param int    $page_size
     *
     * @return array
     * @throws \Exception
     */
    public function serv_list(int $page = 1, int $page_size = 10)
    {
        $where = [1,1];
        $offset=($page-1)*$page_size;
        $serv_list =server::new()->getList($where,$offset,$page_size);
        $cnt_data = server::new()->getCount($where);
        $cnt_page = ceil($cnt_data / $page_size);
        foreach ($serv_list as &$serv) {
            $option           = '<a style="text-decoration:none" class="ml-5 btn btn-xs btn-primary" onClick="info_edit(\'编辑\', \'./serv_edit.php?uid=' . $serv['srv_id'] . '\', 1300)" href="javascript:;" title="编辑">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="suoding mar-R btn btn-xs btn-danger" onclick="info_del(this, ' . $serv['srv_id'] . ')" href="javascript:;" title="删除">删除</a>';
            if (user::new()->get_user_id() == 0) {
                $user['option'] = $option;
            } else {
                $user['option'] = '';
            }
        }
        errno::set(2006);
        return [
            'cnt_data'  => $cnt_data,
            'data'      => $serv_list,
            'cnt_page'  => $cnt_page,
            'curr_page' => $page
        ];
    }

    /**
     * @api 获取服务器信息
     * @param int $srv_id
     *
     * @return array
     */
    public function serv_detail(int $srv_id)
    {
        $srv_info = server::new()->getInfo($srv_id);
        errno::set(2006);
        return $srv_info;
    }
}