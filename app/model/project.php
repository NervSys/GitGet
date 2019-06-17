<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 5:15 PM
 * Note: project.php
 */

namespace app\model;

use app\library\model;

class project extends model
{
    /**
     * @param int $id
     *
     * @return array
     */
    public function get_conf(int $id): array
    {
        $conf = $this->select()
            ->field('proj_conf')
            ->where(['proj_id', $id])
            ->limit(1)
            ->fetch();

        if (empty($conf)) {
            return [];
        }

        return json_decode(current($conf), true);
    }
}