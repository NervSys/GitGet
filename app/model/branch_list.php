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

class branch_list extends model
{
    public function get_active_branch_id(int $proj_id)
    {
        return $this->where([['proj_id', $proj_id], ['active', 1]])
            ->field('branch_id')
            ->get_value();
    }
}