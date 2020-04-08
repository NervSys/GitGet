<?php
/**
 * Created by PhpStorm.
 * User: 25791
 * Date: 2020/4/8
 * Time: 9:40
 * Note: git.php
 */

namespace app\project;


use app\lib\api;
use app\lib\enum\enum_err;
use app\lib\model\branch_list;
use app\lib\model\project_log;
use app\project\service\service_git;
use ext\mpc;

class git extends api
{
    /**
     * 更新操作
     *
     * @param int $proj_id
     *
     * @return bool
     * @throws \Exception
     */
    public function update(int $proj_id)
    {
        $data = [
            'c'     => 'project/git-local_receive',
            'cli_c' => 'project/git-update_cli',
            'data'  => ['proj_id' => $proj_id]
        ];
        return service_git::new()->request($proj_id, $data);
    }

    public function update_cli(int $proj_id)
    {
        return service_git::new()->update($proj_id);
    }

    /**
     * 切换分支
     *
     * @param int $proj_id
     * @param int $branch_id
     *
     * @return bool
     * @throws \Exception
     */
    public function checkout(int $proj_id, int $branch_id)
    {
        $branch_info = branch_list::new()->where([['proj_id', $proj_id], ['branch_id', $branch_id]])->get_one();
        $data        = [
            'c'     => 'project/git-local_receive',
            'cli_c' => 'project/git-checkout_cli',
            'data'  => ['proj_id' => $proj_id, 'branch_name' => $branch_info['branch_name']]
        ];
        return service_git::new()->request($proj_id, $data);
    }

    public function checkout_cli(int $proj_id, string $branch_name)
    {
        return service_git::new()->checkout($proj_id, $branch_name);
    }

    /**
     * 回滚到某个节点
     *
     * @param int $proj_id
     * @param int $log_id
     *
     * @return bool
     * @throws \Exception
     */
    public function reset(int $proj_id, int $log_id)
    {
        $commit_id = project_log::new()->where(['log_id', $log_id])->fields('commit_id')->get_val();
        $data      = [
            'c'     => 'project/git-local_receive',
            'cli_c' => 'project/git-reset_cli',
            'data'  => ['proj_id' => $proj_id, 'commit' => $commit_id]
        ];
        return service_git::new()->request($proj_id, $data);
    }

    public function reset_cli(int $proj_id, string $commit)
    {
        return service_git::new()->reset($proj_id, $commit);
    }

    /**
     * 本地接收请求,调起mpc,执行cli
     *
     * @param string $cli_c
     * @param array  $data
     *
     * @return bool
     * @throws \Exception
     */
    public function local_receive(string $cli_c, array $data)
    {
        mpc::new()->add([
            'c' => $cli_c,
            'd' => $data
        ])->go(false);
        return true;
    }

}