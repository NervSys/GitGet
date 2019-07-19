<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:45 PM
 * Note: ctrl.php
 */

namespace app\server;

use app\model\proj_srv;
use app\model\server;
use app\model\user;
use ext\errno;

class show
{
    public $tz = 'serv_list,serv_detail,sel_list,project_serv_list,project_serv_info';

    public function __construct()
    {
        errno::load('app', 'server');
    }

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
                $serv['option'] = $option;
            } else {
                $serv['option'] = '';
            }
        }
        errno::set(3006);
        return [
            'cnt_data'  => $cnt_data,
            'data'      => $serv_list,
            'cnt_page'  => $cnt_page,
            'curr_page' => $page
        ];
    }

    /**
     * @api 项目的服务器管理列表
     * @param int    $proj_id
     * @param int    $page
     * @param int    $page_size
     *
     * @return array
     * @throws \Exception
     */
    public function project_serv_list(int $proj_id, int $page = 1, int $page_size = 10)
    {
        $where = ['b.proj_id',$proj_id];
        $offset=($page-1)*$page_size;
        $serv_list =server::new()->getProjServList($where,$offset,$page_size);
        $cnt_data = server::new()->getProjServListCount($where);
        $cnt_page = ceil($cnt_data / $page_size);
        foreach ($serv_list as &$serv) {
            $option           = '<a style="text-decoration:none" class="ml-5 btn btn-xs btn-primary" onClick="info_edit(\'配置\', \'./proj_serv_edit.php?id=' . $serv['id'] . '\', 800)" href="javascript:;" title="配置">配置</a>';
            if (user::new()->get_user_id() == 0) {
                $serv['option'] = $option;
            } else {
                $serv['option'] = '';
            }
        }
        errno::set(3006);
        return [
            'cnt_data'  => $cnt_data,
            'list'      => $serv_list,
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
        errno::set(3006);
        return $srv_info;
    }

    /**
     * @api 获取服务器列表
     * @param int $proj_id
     *
     * @return array
     */
    public function sel_list(int $proj_id)
    {
        errno::set(3006);
        $serv_list = server::new()->getServList();
        $eproj_serv_list=proj_srv::new()->getListExcProj($proj_id);
        //比较差集
        $res_serv_list=array_udiff($serv_list,$eproj_serv_list,function ($a,$b){
            if($a['srv_id']==$b['srv_id']){
                return 0;
            }
            return $a['srv_id']>$b['srv_id']?1:-1;
        });
        $res_serv_list=array_values($res_serv_list);
        $srvidsarr=proj_srv::new()->getSrvids($proj_id);
        foreach($res_serv_list as &$serv){
            $serv['selected'] = false;
            if (in_array($serv['srv_id'],$srvidsarr)) {
                $serv['selected'] = true;
            }
        }
        return $res_serv_list;
    }

    /**
     * @api 获取项目的服务器配置信息
     * @param int $id
     */
    public function project_serv_info(int $id){

    }
}