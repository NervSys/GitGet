<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/28/2019
 * Time: 10:36 AM
 * Note: input_escape.php
 */

namespace init\lib;

use core\lib\std\pool;
use ext\factory;

/**
 * Class input_escape
 *
 * @package init\lib
 */
class input_escape extends factory
{
    //设置不过滤字段
    private $exclude = [
        'zhj_token'
    ];

    /**
     * input_escape constructor.
     */
    public function __construct()
    {
        /** @var \core\lib\std\pool $unit_pool */
        $unit_pool = \core\lib\stc\factory::build(pool::class);
        $this->escape($unit_pool, $unit_pool->data);
    }

    /**
     * 过滤输入
     *
     * @param \core\lib\std\pool $unit_pool
     * @param array              $input
     */
    private function escape(pool $unit_pool, array $input): void
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $this->escape($unit_pool, $value);
                continue;
            }

            if (in_array($key, $this->exclude, true)) {
                continue;
            }

            if (is_object($value)) {
                continue;
            }

            $unit_pool->data[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }
}