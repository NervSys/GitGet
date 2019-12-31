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