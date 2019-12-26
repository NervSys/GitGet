<?php
/**
 * Created by PhpStorm.
 * User: liu
 * Date: 2019/12/26
 * Time: 15:34
 * Note: update_timing.php
 */

namespace app\queue\lib;

use ext\log;

class update_timing
{
    public function update()
    {
        log::new()->add(['update', time()])->save();
    }
}