<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:45 PM
 * Note: ctrl.php
 */

namespace app\user;

use ext\conf;
use ext\crypt;
use ext\errno;
use ext\misc;
use app\library\model;

class ctrl extends model
{
    public $tz = 'init,login,user_menu,login_info,user_list,user_detail,user_edit,delete_user';

    private $unit_crypt = null;

    /**
     * ctrl constructor.
     */
    public function __construct()
    {
        parent::__construct();

        errno::load('app', 'user_ctrl');
        $this->unit_crypt = crypt::new(conf::get('openssl'));
    }

    /**
     * @return array
     */
    public function init(): array
    {
        $users = $this->select('user')->field('user_id')->fetch(true);

        if (!empty($users)) {
            return errno::get(2001, 1);
        }

        $user_acc = 'admin';
        $user_pwd = 'admin';

        $user_key = $this->unit_crypt->get_key();

        $init = $this->insert('user')
            ->value([
                'user_uuid' => misc::uuid($user_acc),
                'user_acc'  => $user_acc,
                'user_pwd'  => $this->unit_crypt->hash_pwd($user_pwd, $user_key),
                'user_key'  => $user_key,
                'add_time'  => time(),

            ])
            ->execute();

        return $init ? errno::get(2000) : errno::get(2001, 1);
    }

    /**
     * @param string $acc
     * @param string $pwd
     *
     * @return array
     * @throws \Exception
     */
    public function login(string $acc, string $pwd): array
    {
        $user_data = $this->select('user')
            ->field('user_id', 'user_uuid', 'user_acc', 'user_pwd', 'user_key')
            ->where(['user_uuid', misc::uuid($acc)])
            ->limit(1)
            ->fetch();

        if (empty($user_data)) {
            return errno::get(2003, 1);
        }

        $user_data = current($user_data);

        $pass_verify = $this->unit_crypt->check_pwd($pwd, $user_data['user_key'], $user_data['user_pwd']);

        if (!$pass_verify) {
            return errno::get(2004, 1);
        }

        errno::set(2005);

        unset($user_data['user_key'], $user_data['user_pwd']);

        return [
            'name'  => $user_data['user_acc'],
            'token' => $this->unit_crypt->sign(json_encode($user_data))
        ];
    }

    /**
     * @api 获取登录信息
     * @param string $token
     */
    public function login_info(string $token){
        $user_id=$this->get_user_id();
        errno::set(2006);
        return [];
    }

    public function user_menu(){
        //固定菜单
        $menu=[
            [
                'menu_id'=>1,
                'parent_id'=>0,
                'menu_name'=>'用户管理',
                'menu_icon'=>'user',
                'child'=>[
                    ['menu_id'=>2,
                    'parent_id'=>1,
                    'menu_name'=>'用户列表',
                    'menu_icon'=>'',
                    'menu_url'=>'user_list.php'],
                ],
            ]
        ];
        errno::set(2006);
        return $menu;
    }

    /**
     * @api 会员列表
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function  user_list(int $page = 1, int $page_size = 10){
        $user_list  = [];
        $cnt_user   = $cnt_page = 0;
        $where["1"] = "1";
        $result = $this->user_list_data($where, $page_size);
        extract($result);
        foreach ($user_list as &$user) {
            $user['projects']= $this->select('project_team AS a')
                ->join('project AS b', ['a.proj_id', 'b.proj_id'])
                ->where(['a.user_id',$user['user_id']])
                ->field('group_concat(b.proj_name)')
                ->fetch(true)[0] ?? '';
            $user['add_time']       = date('Y-m-d H:i:s', $user['add_time']);
            $option = '<a style="text-decoration:none" class="ml-5" onClick="member_edit(\'编辑\', \'./user_edit.php?uid=' . $user['user_id'] . '\', 1300)" href="javascript:;" title="编辑">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="suoding mar-R" style="color:red;" onclick="user_del(this, ' . $user['user_id'] . ')" href="javascript:;" title="删除">删除</a>';
            if($user['user_id']!=1){
                $user['option'] = $option;
            }else{
                $user['option'] = '';
            }
        }
        errno::set(2006);
        return [
                'cnt_data'  => $cnt_user,
                'data'      => $user_list,
                'cnt_page'  => $cnt_page,
                'curr_page' => $page
            ];
    }

    /**
     * 用户列表函数
     *
     * @param $where
     * @param $page_size
     *
     * @return array
     */
    private function user_list_data($where, $page_size)
    {
        $page          = parent::$data['page'] ?? 1;
        $user_list=$this->select('user AS a')
            ->field('a.user_acc', 'a.user_id', 'a.add_time')
            ->order(['a.add_time' => 'asc'])
            ->limit(($page - 1) * $page_size,$page_size)
            ->fetch();
        $cnt_user=$this->select('user')
            ->field('count(*)')
            ->fetch(true)[0];
        $cnt_page  = ceil($cnt_user / $page_size);
        return compact('user_list', 'cnt_user', 'cnt_page');
    }

    /**
     * @api 获取用户信息
     * @param int $user_id
     */
    public function user_detail(int $user_id){
        $user_info=$this->select('user AS a')
            ->field('a.user_acc', 'a.user_id')
            ->where(['a.user_id',$user_id])
            ->fetch()[0];
        errno::set(2006);
        return $user_info;
    }

    public function user_edit(int $user_id,string $user_acc,string $user_pwd=''){
        $data=[
            'user_uuid' => misc::uuid($user_acc),
            'user_acc'  => $user_acc,
            'add_time' => time()
        ];
        if($user_pwd){
            $user_key = $this->unit_crypt->get_key();
            $u_pwd=$this->unit_crypt->hash_pwd($user_pwd, $user_key);
            $data['user_pwd']=$u_pwd;
            $data['user_key']=$user_key;
        }
        try{
            if($user_id){
                //更新
                $this->update('user')
                    ->value($data)
                    ->where(['user_id',$user_id])
                    ->execute();
            }else{
                //新增
                $this->insert('user')
                    ->value($data)
                    ->execute();
            }
        }catch (\PDOException $e){
            return errno::get(2008, 1);
        }
        errno::set(2007);
        return [];
    }

    /**
     * @param int $user_id 用户id
     *
     * @return array
     * @api 删除用户
     */
    public function delete_user(int $user_id): array
    {
        if ($user_id == 1) return errno::get(2009,1);
        $this->begin();
        try {
            $this->delete('user')->where(['user_id',$user_id])->execute();
            $this->delete('project_team')->where(['user_id',$user_id])->execute();
        } catch (\PDOException $e) {
            $this->rollback();
            return errno::get(2008,1);
        }
        $this->commit();
        return errno::get(2007,0);
    }


}