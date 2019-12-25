<?php
/**
 * Created by PhpStorm.
 * User: xushuhui
 * Date: 2019/6/29
 * Time: 10:47
 */

namespace app\library;


use ext\conf;
use ext\mysql;
use ext\pdo;
use ext\pdo_mysql;

class model extends mysql
{
    public $prefix = 'git_';

    public function __construct()
    {
        parent::__construct(pdo::create(conf::get('mysql'))->connect());
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
        $data = [
            'curr_page' => $page,
            'cnt_data' => 0,
            'cnt_page' => 0,
            'list' => []
        ];
        $count_obj = clone $this;
        unset($count_obj->runtime['field']);
        $data['cnt_data'] = $count_obj->select()->fields('COUNT(*) AS C')->fetch_all(\PDO::FETCH_COLUMN)[0];
        $data['cnt_page'] = ceil($data['cnt_data'] / $page_size);

        $page = $page < 1 ? 1 : $page;
        $page_size = $page_size < 1 ? 1 : $page_size;
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