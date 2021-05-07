<?php
/**
 * Git Get
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

namespace api;


use app\lib\base;
use app\lib\model\svr as model_svr;

class svr extends base
{
    /**
     * 列表
     *
     * @param int $page
     * @param int $page_size
     *
     * @return array
     */
    public function list(int $page, int $page_size): array
    {
        return model_svr::new()->where(['status', 1])->get_page($page, $page_size);
    }

    /**
     * 信息
     *
     * @param int $id
     *
     * @return array
     */
    public function info(int $id): array
    {
        return model_svr::new()->where([['id', $id], ['status', 1]])->get_one();
    }

    /**
     * 编辑或新增
     *
     * @param string $url
     * @param string $name
     * @param string $home_path
     * @param int    $id
     *
     * @return bool
     */
    public function edit(string $url, string $name = '', string $home_path = '', int $id = 0): bool
    {
        $value = [
            'url'       => $url,
            'name'      => $name,
            'home_path' => $home_path
        ];
        if ($id) {
            return model_svr::new()->update($value)->where(['id', $id])->execute();
        } else {
            return model_svr::new()->insert($value)->execute();
        }
    }

    /**
     * 删除
     *
     * @param int $svr_id
     *
     * @return bool
     */
    public function del(int $svr_id): bool
    {
        return model_svr::new()->where(['id', $svr_id])->update(['status' => 2])->execute();
    }
}