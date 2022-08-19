<?php

declare(strict_types=1);

return [
    'app_id' => 'hs-service', // 应用ID

    'server' => [
        'ip' => '127.0.0.1', // 上报服务端IP
        'port' => '9506', // 上报端口
    ],

    // 埋点相关配置
    'bury' => [
        'url' => 'shenche', // 上报地址
        'index' => 'report:hs-service', // 对应的es索引
        'interval' => 300, // 间隔多少秒上报一次
        'batch_num' => 50, // 满足多少条上报一次
    ],

    // 告警相关配置
    'alarm' => [

    ],
];
