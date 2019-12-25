<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 2:28 PM
 * Note: show.php
 */

namespace app\project;

use app\library\base;
use app\model\branch_list;
use app\model\project;
use app\model\project_log;
use app\model\server;

class show extends base
{
    public $tz = '*';

    /**
     * 项目列表
     *
     * @param int $page
     * @param int $page_size
     *
     * @return array
     */
    public function list(int $page = 1, int $page_size = 10): array
    {
        $res = project::new()->fields('proj_id', 'proj_name', 'status', 'is_lock')->where([['status', '<>', 2]])->get_page($page, $page_size);
        foreach ($res['list'] as &$item) {
            $branch         = branch_list::new()->where([['proj_id', $item['proj_id']], ['active', 1]])->fields('branch_id', 'branch_name')->get_one();
            $item['branch'] = $branch['branch_name'];
            $item['commit'] = project_log::new()->where([['proj_id', $item['proj_id']], ['branch_id', $branch['branch_id']], ['active', 1]])->fields('proj_log')->get_value();
            $key            = "proj_lock:" . $item['proj_id'];
            $is_lock        = $this->redis->exists($key);
            $btn_type       = $is_lock ? 'default disabled' : 'primary';
            $html           = $is_lock ? '进行中' : '更新';
            $git_type       = $is_lock ? 'default disabled' : 'warning';
            $option         = '<a style="text-decoration:none" class="ml-5 btn btn-xs btn-success" onClick="proj_edit(\'编辑\', \'./project_edit.php?proj_id=' . $item['proj_id'] . '\', 1300)" href="javascript:;" title="编辑">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;';
            $option         .= '<a style="text-decoration:none" id="' . $item['proj_id'] . '" class="ml-5 deploy btn btn-xs btn-' . $btn_type . '" onClick="proj_update(this,' . $item['proj_id'] . ')">' . $html . '</a>&nbsp;&nbsp;&nbsp;&nbsp;';
            $option         .= '<a style="text-decoration:none" class="ml-5 btn btn-xs btn-' . $git_type . '" onClick="git(\'编辑\', \'./project_git.php?proj_id=' . $item['proj_id'] . '\', 1300)" href="javascript:;" title="编辑">git</a>&nbsp;&nbsp;&nbsp;&nbsp;';
            $item['option'] = $option;
        }
        return $this->succeed($res);
    }

    /**
     * 详细信息
     *
     * @param int $proj_id
     *
     * @return array
     */
    public function info(int $proj_id): array
    {
        $proj_info                      = project::new()->where(['proj_id', $proj_id])->get_one();
        $srv_list                       = !empty($proj_info['srv_list']) ? json_decode($proj_info['srv_list'], true) : [];
        $proj_info['proj_backup_files'] = !empty($proj_info['proj_backup_files']) ? json_decode($proj_info['proj_backup_files'], true) : [];
        $all_srv                        = server::new()->where(['status', 1])->get();
        foreach ($all_srv as &$srv) {
            $srv['is_check'] = 0;
            if (in_array($srv['srv_id'], $srv_list)) {
                $srv['is_check'] = 1;
            }
        }
        $proj_info['srv_list'] = $all_srv;
        return $this->succeed($proj_info);
    }
}