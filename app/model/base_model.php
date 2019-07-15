<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 1:07 PM
 * Note: model.php
 */

namespace app\model;

use ext\conf;
use ext\crypt;
use ext\pdo;
use ext\pdo_mysql;

class base_model extends pdo_mysql
{
    public $prefix = 'zhj_';
    public $table  = '';

    /**
     * model constructor.
     */
    public function __construct()
    {
        conf::load('/', 'mysql');
        conf::set('openssl', ['conf' => ROOT . 'conf' . DIRECTORY_SEPARATOR . 'openssl.conf']);
        $this->instance = $this->config(conf::get('mysql'))->connect()->get_pdo();
    }

    public function alias($alias)
    {
        $table_path  = get_called_class();
        $table       = explode('\\', $table_path);
        $this->table = end($table) . " AS " . $alias;
        return $this;
    }

    public function count(): int
    {
        return $this->select($this->table)->field('COUNT(*) AS C')->fetch(\PDO::FETCH_COLUMN)[0];
    }

    public function exist(): bool
    {
        $data = $this->select($this->table)->limit(1)->fetch(\PDO::FETCH_COLUMN);
        return isset($data[0]) ? true : false;
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

    public function get_value()
    {
        $data = $this->select($this->table)->limit(1)->fetch(\PDO::FETCH_COLUMN);
        return $data[0] ?? [];
    }

    public function get_col(): array
    {
        return $this->select($this->table)->fetch(\PDO::FETCH_COLUMN);
    }

    public function save()
    {
        return $this->update($this->table)->execute();
    }

    public function create()
    {
        return $this->insert($this->table)->execute();
    }

    public function create_all(array $data)
    {
        $values = $bind_keys = $keys = [];
        foreach ($data as $idx => $datum) {
            $value    = [];
            $bind_key = [];
            foreach ($datum as $key => $item) {
                if (!in_array($key, $keys)) {
                    $keys[] = $key;
                }
                $bind_key[]                     = ":" . $key . "_" . $idx;
                $value[":" . $key . "_" . $idx] = $item;
            }
            $bind_keys[] = '(' . implode(',', $bind_key) . ')';
            $values[]    = $value;
        }
        $table      = explode('\\', get_called_class());
        $table_name = $this->prefix . end($table);
        $sql        = 'INSERT INTO ' . $table_name . ' (' . implode(',', $keys) . ') VALUES ' . implode(',', $bind_keys);
        $statment   = pdo::use('main')->get_pdo()->prepare($sql);
        foreach ($values as $bind) {
            foreach ($bind as $k => $v) {
                $statment->bindValue($k, $v);
            }
        }
        $res = $statment->execute();
        return $res;
    }
}