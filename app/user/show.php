<?php
/**
 * Git Remote Deploy
 *
 * Copyright 2019-2020 leo <2579186091@qq.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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