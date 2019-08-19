<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 2:28 PM
 * Note: ctrl.php
 */

namespace app\project;

use app\library\base;
use app\model\branch_list;
use app\model\project;

class ctrl extends base
{
    public $tz = '*';

    /**
     * 新增或编辑项目
     *
     * @param string $proj_name
     * @param string $proj_desc
     * @param string $git_url
     * @param string $local_path
     * @param array  $srv_ids
     * @param array  $backup_files
     * @param int    $proj_id
     *
     * @return array
     */
    public function add(string $proj_name, string $proj_desc, string $git_url, string $local_path, array $srv_ids, array $backup_files, int $proj_id = 0)
    {
        $data = [
            'proj_name'         => $proj_name,
            'proj_desc'         => $proj_desc,
            'proj_git_url'      => $git_url,
            'proj_local_path'   => $local_path,
            'srv_list'          => json_encode($srv_ids),
            'proj_backup_files' => json_encode($backup_files),
        ];
        if ($proj_id == 0) {
            project::new()->value($data)->insert_data();
            $proj_id = project::new()->lastInsertId();
            $data    = [
                'branch_name' => 'master',
                'proj_id'     => (int)$proj_id,
                'active'      => 1,
            ];
            branch_list::new()->value($data)->insert_data();
        } else {
            project::new()->value($data)->where(['proj_id', $proj_id])->update_data();
        }
        return $this->succeed();
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
        project::new()->value(['status' => 2])->where(['proj_id', $proj_id])->update_data();
        return $this->succeed();
    }
}