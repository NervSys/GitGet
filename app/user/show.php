<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:45 PM
 * Note: ctrl.php
 */

namespace app\user;

use app\library\base;
use app\model\user;

class show extends base
{
    public $tz = '*';

    /**
     * @return array
     * @throws \Exception
     * @api 菜单列表
     */
    public function user_menu()
    {
        //固定菜单
        $menu = [
            [
                'menu_id'   => 1,
                'parent_id' => 0,
                'menu_name' => '项目管理',
                'menu_icon' => 'cubes',
                'child'     => [
                    [
                        'menu_id'   => 11,
                        'parent_id' => 1,
                        'menu_name' => '项目列表',
                        'menu_icon' => '',
                        'menu_url'  => 'project_list.php'
                    ],
                ],
            ],
            [
                'menu_id'   => 2,
                'parent_id' => 0,
                'menu_name' => '初始配置',
                'menu_icon' => 'server',
                'child'     => [
                    [
                        'menu_id'   => 21,
                        'parent_id' => 2,
                        'menu_name' => '服务器列表',
                        'menu_icon' => '',
                        'menu_url'  => 'serv_list.php'
                    ],
                    [
                        'menu_id'   => 22,
                        'parent_id' => 2,
                        'menu_name' => '系统设置',
                        'menu_icon' => '',
                        'menu_url'  => 'system_setting.php'
                    ],
                ],
            ],
        ];
        return $this->succeed($menu);
    }

    /**
     * 用户信息
     *
     * @return array
     */
    public function user_info()
    {
        $user_info = user::new()->where(['user_id', $this->user_id])->fields('user_acc')->get_one();
        return $this->succeed($user_info);
    }
}