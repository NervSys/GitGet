<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 10/31/2019
 * Time: 3:07 PM
 * Note: boot.php
 */

namespace init;

use ext\conf;
use init\lib\input_escape;

/**
 * Class boot
 *
 * @package init
 */
class boot
{
    //ENV default value
    public static $env = 'prod';

    /**
     * 加载环境配置
     */
    public function env()
    {
        //get new .env
        if (is_file($env_file = ROOT . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . '.env')) {
            $env_conf = trim(file_get_contents($env_file));

            if (in_array($env_conf, ['prod', 'test', 'dev'])) {
                self::$env = &$env_conf;
            }
        }

        //Load conf
        conf::load('conf', self::$env);
    }

    /**
     * 数据预备
     */
    public function prep()
    {
        //全局过滤
        input_escape::new();
    }
}