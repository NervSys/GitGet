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

namespace app\library;


use ext\conf;
use ext\mysql;
use ext\pdo;
use ext\pdo_mysql;

class model extends mysql
{

    public function __construct()
    {
		$this->set_prefix('git_')->use_pdo(base::new()->mysql->pdo);
    }

    public function alias(string $table_name)
    {
        $this->set_table(get_class($this). " as ".$table_name);
        return $this;
    }

    public function incre(array $where, array $incr, array $value = [])
    {
        return $this->where($where)->update()->incr($incr)->value($value)->execute();
    }

    public function lastInsertId()
    {
        return $this->instance->lastInsertId();
    }

    public function count(): int
    {
        return $this->select()->fields('COUNT(*) AS C')->fetch_all(\PDO::FETCH_COLUMN)[0];
    }

    public function get(): array
    {
        return $this->select()->fetch_all();
    }

    public function get_one(): array
    {
        $data = $this->select()->limit(1)->fetch_all();
        return $data[0] ?? [];
    }

    public function exist(): bool
    {
        $data = $this->select()->limit(1)->fetch_all(\PDO::FETCH_COLUMN);
        return isset($data[0]) ? true : false;
    }

    public function get_value()
    {
        $data = $this->select()->limit(1)->fetch_all(\PDO::FETCH_COLUMN);
        return $data[0] ?? '';
    }

    public function get_sum($field)
    {
        $res = $this->select()->limit(1)->fields("sum(" . $field . ")")->fetch_all(\PDO::FETCH_COLUMN);
        return $res[0] ?? 0;
    }

    public function get_col(): array
    {
        return $this->select()->fetch_all(\PDO::FETCH_COLUMN);
    }

    public function get_page(int $page, int $page_size): array
    {
        $data      = [
            'curr_page' => $page,
            'cnt_data'  => 0,
            'cnt_page'  => 0,
            'list'      => []
        ];
        $count_obj = clone $this;
        unset($count_obj->runtime['field']);
        $data['cnt_data'] = $count_obj->select()->fields('COUNT(*) AS C')->fetch_all(\PDO::FETCH_COLUMN)[0];
        $data['cnt_page'] = ceil($data['cnt_data'] / $page_size);

        $page         = $page < 1 ? 1 : $page;
        $page_size    = $page_size < 1 ? 1 : $page_size;
        $data['list'] = $this->limit(($page - 1) * $page_size, $page_size)->select()->fetch_all();
        return $data;
    }

    public function update_data()
    {
        return $this->update()->execute();
    }

    public function insert_data()
    {
        return $this->insert()->execute();
    }

    public function del()
    {
        return $this->delete()->execute();
    }
}