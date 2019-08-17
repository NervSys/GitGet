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

class show extends base
{
    public $tz = '*';

    /**
     * @api 菜单列表
     * @return array
     * @throws \Exception
     */
    public function user_menu()
    {
        //固定菜单
        $menu = [
            [
                'menu_id' => 1,
                'parent_id' => 0,
                'menu_name' => '项目管理',
                'menu_icon' => 'cubes',
                'child' => [
                    ['menu_id' => 11,
                        'parent_id' => 1,
                        'menu_name' => '项目列表',
                        'menu_icon' => '',
                        'menu_url' => 'project_list.php'],
                ],
            ],
            [
                'menu_id' => 2,
                'parent_id' => 0,
                'menu_name' => '服务器管理',
                'menu_icon' => 'server',
                'child' => [
                    ['menu_id' => 21,
                        'parent_id' => 2,
                        'menu_name' => '服务器列表',
                        'menu_icon' => '',
                        'menu_url' => 'serv_list.php'],
                ],
            ],
        ];
        return $this->succeed($menu);
    }
}