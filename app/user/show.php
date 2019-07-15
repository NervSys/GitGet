<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:45 PM
 * Note: ctrl.php
 */

namespace app\user;

use app\model\user;
use ext\conf;
use ext\crypt;
use ext\errno;
use ext\misc;

class show
{
    public $tz = 'user_menu,user_list,user_detail';

    private $unit_crypt = null;

    public $root_acc = 'root';
    public $root_pwd = 'root';

    /**
     * ctrl constructor.
     */
    public function __construct()
    {
        errno::load('app', 'user');
        $this->unit_crypt = crypt::new(conf::get('openssl'));
    }

    /**
     * @api 菜单列表
     * @return array
     * @throws \Exception
     */
    public function user_menu()
    {
        $user_id = user::new()->get_user_id();
        if ($user_id == 0) {
            //固定菜单
            $menu = [
                [
                    'menu_id'   => 1,
                    'parent_id' => 0,
                    'menu_name' => '用户管理',
                    'menu_icon' => 'user',
                    'child'     => [
                        ['menu_id'   => 2,
                         'parent_id' => 1,
                         'menu_name' => '用户列表',
                         'menu_icon' => 'show',
                         'menu_url'  => 'user_list.php'],
                        ['menu_id'   => 3,
                         'parent_id' => 1,
                         'menu_name' => '权限分配',
                         'menu_icon' => '',
                         'menu_url'  => 'user_auth.php'],
                    ],
                ],
                [
                    'menu_id'   => 3,
                    'parent_id' => 0,
                    'menu_name' => '项目管理',
                    'menu_icon' => 'cubes',
                    'child'     => [
                        ['menu_id'   => 4,
                         'parent_id' => 3,
                         'menu_name' => '项目列表',
                         'menu_icon' => '',
                         'menu_url'  => 'project_list.php'],
                    ],
                ]
            ];
        } else {
            //固定菜单
            $menu = [
                [
                    'menu_id'   => 3,
                    'parent_id' => 0,
                    'menu_name' => '项目管理',
                    'menu_icon' => 'cubes',
                    'child'     => [
                        ['menu_id'   => 4,
                         'parent_id' => 3,
                         'menu_name' => '项目列表',
                         'menu_icon' => '',
                         'menu_url'  => 'project_list.php'],
                    ],
                ]
            ];
        }

        errno::set(2006);
        return $menu;
    }

    /**
     * @api 用户列表
     * @param string $user_id
     * @param string $acc
     * @param int    $page
     * @param int    $page_size
     *
     * @return array
     * @throws \Exception
     */
    public function user_list(string $user_id = '',string $acc = '',int $page = 1, int $page_size = 10)
    {
        $where = [1,1];
        if (!empty($user_id)){
            $where[] = ['user_id',(int)$user_id];
        }
        if (!empty($acc)){
            $where[] = ['user_acc',$acc];
        }
        $user_list = user::new()
            ->field('user_acc', 'user_id', 'add_time')
            ->order(['add_time' => 'asc'])
            ->where($where)
            ->limit(($page - 1) * $page_size, $page_size)
            ->get();
        $cnt_user = user::new()->where($where)->count();
        $cnt_page = ceil($cnt_user / $page_size);
        foreach ($user_list as &$user) {
            $user['add_time'] = date('Y-m-d H:i:s', $user['add_time']);
            $option           = '<a style="text-decoration:none" class="ml-5" onClick="member_edit(\'编辑\', \'./user_edit.php?uid=' . $user['user_id'] . '\', 1300)" href="javascript:;" title="编辑">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" class="suoding mar-R" style="color:red;" onclick="user_del(this, ' . $user['user_id'] . ')" href="javascript:;" title="删除">删除</a>';
            if (user::new()->get_user_id() == 0) {
                $user['option'] = $option;
            } else {
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
     * @api 获取用户信息
     * @param int $user_id
     *
     * @return array
     */
    public function user_detail(int $user_id)
    {
        $user_info = user::new()
            ->field('user_acc','user_id')
            ->where(['user_id',$user_id])
            ->get_one();
        errno::set(2006);
        return $user_info;
    }
}