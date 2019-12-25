<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 7/16/2019
 * Time: 1:27 PM
 * Note: request_verify.php
 */

namespace init\lib;

use app\lib\error_code;
use app\lib\response;
use app\lib\validator;
use core\lib\std\pool;
use core\lib\std\router;
use ext\core;
use ext\factory;

class request_verify extends factory
{
    public function __construct()
    {
        $pool = \core\lib\stc\factory::create(pool::class, []);

        //解析cmd
        list($class, $method) = explode('-', $pool->cmd);
        $class = \core\lib\stc\factory::create(router::class, [])->get_cls($class);

        //rule验证
        $target_namespace = substr($class, 0, strrpos($class, '\\')) . '\\rule';
        $target_class     = $target_namespace . substr($class, strrpos($class, '\\')) . "_rule";
        if (class_exists($target_class)) {
            $tar_class = new $target_class;
            if (method_exists($tar_class, $method)) {
                $rule     = forward_static_call([$tar_class, $method]);
                $validate = validator::new();

                $res = $validate->check(core::get_data(), $rule);
                if (!$res) {
                    response::new()->failMsg(error_code::INVALID_PARAMS, $validate->getError());
                    core::stop();
                }
            }
        }

        /*foreach (parent::$cgi_list as $item) {
            if (false === strpos($item, '-')) {
                continue;
            }

            list($class, $method) = explode('-', $item);

            $class = parent::get_app_class($class);

            //rule验证
            $target_namespace = substr($class, 0, strrpos($class, '\\')) . '\\rule';
            $target_class     = $target_namespace . substr($class, strrpos($class, '\\')) . "_rule";
            if (class_exists($target_class)) {
                $tar_class = new $target_class;
                if (method_exists($tar_class, $method)) {
                    $rule    = forward_static_call([$tar_class, $method]);
                    $request = request::new();
                    $res     = $request->check(parent::$data, $rule);
                    if (!$res) {
                        $request->setParamsError($request->getError());
                        parent::stop();
                    }
                }
            }

            //load数据
            $target_namespace = substr($class, 0, strrpos($class, '\\')) . '\\request';
            $target_class     = $target_namespace . '\\' . $method . '_request';

            if (class_exists($target_class)) {
                $request = new $target_class;

                $res = $request->check(parent::$data);
                if (!$res) {
                    $request->setParamsError($request->getError());
                    parent::stop();
                }

                if (method_exists($request, 'load')) {
                    $request->load(parent::$data);
                    parent::$data[$method . '_request'] = $request;
                }
            }
        }*/
    }
}