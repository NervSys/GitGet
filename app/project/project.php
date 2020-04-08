<?php
/**
 * Created by PhpStorm.
 * User: 25791
 * Date: 2020/4/7
 * Time: 16:00
 * Note: project.php
 */

namespace app\project;


use app\lib\api;
use app\lib\model\branch_list;
use app\lib\model\project as model_project;
use app\lib\model\project_log;
use app\lib\model\server;

class project extends api
{
    /**
     * 项目列表
     *
     * @param int $page
     * @param int $page_size
     *
     * @return array
     */
    public function list(int $page, int $page_size)
    {
        $res = model_project::new()->fields('proj_id', 'proj_name', 'status', 'is_lock')->where([['status', '<>', 2]])->get_page($page, $page_size);
        foreach ($res['list'] as &$item) {
            $branch         = branch_list::new()->where([['proj_id', $item['proj_id']], ['active', 1]])->fields('branch_id', 'branch_name')->get_one();
            $item['branch'] = $branch['branch_name'];
            $item['commit'] = project_log::new()->where([['proj_id', $item['proj_id']], ['branch_id', $branch['branch_id']], ['active', 1]])->fields('proj_log')->get_val();
        }
        return $res;
    }

    /**
     * 项目信息
     *
     * @param int $proj_id
     *
     * @return array
     */
    public function info(int $proj_id): array
    {
        $proj_info                      = model_project::new()->where(['proj_id', $proj_id])->get_one();
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
        return $proj_info;
    }

    /**
     * 新增编辑
     *
     * @param string $proj_name
     * @param string $proj_desc
     * @param string $git_url
     * @param string $local_path
     * @param array  $srv_ids
     * @param array  $backup_files
     * @param int    $proj_id
     *
     * @return mixed
     */
    public function edit(string $proj_name, string $proj_desc, string $git_url, string $local_path, array $srv_ids, array $backup_files = [], int $proj_id = 0)
    {
        foreach ($srv_ids as &$srv_id) {
            $srv_id = (int)$srv_id;
        }
        $data = [
            'proj_name'         => $proj_name,
            'proj_desc'         => $proj_desc,
            'proj_git_url'      => $git_url,
            'proj_local_path'   => $local_path,
            'srv_list'          => json_encode($srv_ids),
            'proj_backup_files' => json_encode($backup_files),
        ];
        if ($proj_id == 0) {
            model_project::new()->value($data)->add();
            $proj_id = model_project::new()->get_last_insert_id();
            $data    = [
                'branch_name' => 'master',
                'proj_id'     => (int)$proj_id,
                'active'      => 1,
            ];
            branch_list::new()->value($data)->add();
        } else {
            model_project::new()->value($data)->where(['proj_id', $proj_id])->save();
        }
        return true;
    }

    /**
     * 删除项目（软删除）
     *
     * @param int $proj_id
     *
     * @return array
     */
    public function del(int $proj_id): array
    {
        return model_project::new()->value(['status' => 2])->where(['proj_id', $proj_id])->save();
    }

    /**
     * 项目状态
     * @param int $proj_id
     *
     * @return mixed
     */
    public function proj_status(int $proj_id)
    {
        $key       = "proj_lock:" . $proj_id;
        $status    = $this->redis->get($key);
        $res       = ['status' => (int)$status, 'msg' => ''];
        $error_key = 'gg_error:' . $proj_id;
        $error     = $this->redis->get($error_key);
        if (!empty($error)) {
            $res['msg'] = $error;
            $this->redis->del($key);
            $this->redis->del($error_key);
        }
        return $res;
    }
}