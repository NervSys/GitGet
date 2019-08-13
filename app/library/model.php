<?php
/**
 * Created by PhpStorm.
 * User: xushuhui
 * Date: 2019/6/29
 * Time: 10:47
 */

namespace app\library;


use ext\pdo_mysql;
use ext\pdo;

class model extends pdo_mysql
{
    public $prefix = 'git_';
    public $table = '';

    public function __construct()
    {
        $this->instance = pdo::use('main')->get_pdo();
    }

    public function incre(array $where, array $incr, array $value = [])
    {
        return $this->where($where)->update()->incr($incr)->value($value)->execute();
    }

    public function lastInsertId()
    {
        return $this->instance->lastInsertId();
    }

    public function alias($alias)
    {
        $table_path = get_called_class();
        $table = explode('\\', $table_path);
        $this->table = end($table) . " AS " . $alias;
        return $this;
    }

    public function count(): int
    {
        return $this->select($this->table)->field('COUNT(*) AS C')->fetch(\PDO::FETCH_COLUMN)[0];
    }

    public function get(): array
    {
        return $this->select($this->table)->fetch();
    }

    public function get_one(): array
    {
        $data = $this->select($this->table)->limit(1)->fetch();
        return $data[0] ?? [];
    }

    public function exist(): bool
    {
        $data = $this->select($this->table)->limit(1)->fetch(\PDO::FETCH_COLUMN);
        return isset($data[0]) ? true : false;
    }

    public function get_value()
    {
        $data = $this->select($this->table)->limit(1)->fetch(\PDO::FETCH_COLUMN);
        return $data[0] ?? '';
    }

    public function get_sum($field)
    {
        $res = $this->select($this->table)->limit(1)->field("sum(" . $field . ")")->fetch(\PDO::FETCH_COLUMN);
        return $res[0] ?? 0;
    }

    public function get_col(): array
    {
        return $this->select($this->table)->fetch(\PDO::FETCH_COLUMN);
    }

    public function get_page(int $page, int $page_size): array
    {
        $data = [
            'total' => 0,
            'list' => []
        ];
        $count_obj = clone $this;
        unset($count_obj->runtime['field']);
        $data['total'] = $count_obj->select($this->table)->field('COUNT(*) AS C')->fetch(\PDO::FETCH_COLUMN)[0];

        $page = $page < 1 ? 1 : $page;
        $page_size = $page_size < 1 ? 1 : $page_size;
        $data['list'] = $this->limit(($page - 1) * $page_size, $page_size)->select($this->table)->fetch();
        return $data;
    }

    public function update_data()
    {
        return $this->update($this->table)->execute();
    }

    public function insert_data()
    {
        return $this->insert($this->table)->execute();
    }
}