<?php

require __DIR__ . '/../../NervSys/NS.php';

\Ext\libCoreApi::new()
    //全局跨域
    ->addCorsRecord('*')
    //核心调试模式
    ->setCoreDebug(true)
    //强制输出为 JSON
    ->setContentType('application/json');

NS::new();