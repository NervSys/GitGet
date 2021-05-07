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

namespace app\lib;


use Ext\libMySQL;

class model extends libMySQL
{
    /**
     * model constructor.
     */
    public function __construct()
    {
        $this->setTablePrefix('git_')->bindPdo(base::new()->mysql->pdo);
    }

    /**
     * 表重命名
     *
     * @param $alias
     *
     * @return $this
     */
    public function alias($alias): self
    {
        $this->table_name = get_called_class() . " AS " . $alias;
        return $this;
    }

    /**
     * 读取多行，与where，limit连用
     *
     * @param int $fetch_style
     *
     * @return array
     */
    public function get(int $fetch_style = \PDO::FETCH_ASSOC): array
    {
        return $this->select()->getAll($fetch_style);
    }

    /**
     * 读取一行，与where连用
     *
     * @param int $fetch_style
     *
     * @return array
     */
    public function get_one(int $fetch_style = \PDO::FETCH_ASSOC): array
    {
        return $this->select()->getRow($fetch_style);
    }

    /**
     * 读取值，与where连用
     *
     * @return string
     */
    public function get_val(): string
    {
        $data = $this->getRow(\PDO::FETCH_COLUMN);
        return (string)$data[0] ?? '';
    }

    /**
     * @param int $page
     * @param int $page_size
     *
     * @return array
     */
    public function get_page(int $page, int $page_size): array
    {
        $data             = [
            'curr_page' => $page,
            'cnt_data'  => 0,
            'cnt_page'  => 0,
            'list'      => []
        ];
        $count_obj        = clone $this;
        $data['cnt_data'] = $count_obj->select('COUNT(*) AS C')->getRow(\PDO::FETCH_COLUMN)[0];
        $data['cnt_page'] = ceil($data['cnt_data'] / $page_size);

        $page         = $page < 1 ? 1 : $page;
        $page_size    = $page_size < 1 ? 1 : $page_size;
        $data['list'] = $this->limit(($page - 1) * $page_size, $page_size)->select()->getAll();
        return $data;
    }

    /**
     * 是否存在，与where连用
     *
     * @return bool
     */
    public function exist(): bool
    {
        return !empty($this->select()->limit(1)->getRow());
    }

    /**
     * 总行数
     *
     * @return mixed
     */
    public function cnt()
    {
        return current($this->select('COUNT(*) AS C')->getRow(\PDO::FETCH_COLUMN));
    }

    /**
     * 求和
     *
     * @return mixed
     */
    public function sum()
    {
        $field = $this->runtime_data['cols'][0];
        unset($this->runtime_data['cols']);

        $res = $this->select("sum(" . $field . ")")->getRow(\PDO::FETCH_COLUMN);
        return $res[0] ?? 0;
    }

    /**
     * @return bool
     * @deprecated
     */
    public function save(): bool
    {
        return $this->update()->execute();
    }

    /**
     * @return bool
     * @deprecated
     */
    public function add(): bool
    {
        return $this->insert()->execute();
    }

    public function del(): bool
    {
        return $this->delete()->execute();
    }
}