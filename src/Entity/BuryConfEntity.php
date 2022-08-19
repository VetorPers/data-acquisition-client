<?php

namespace ReportAgent\Entity;

/**
 * 埋点配置
 */
class BuryConfEntity extends Entity
{
    // 上报地址
    public string $url = '';

    // 对应的es索引
    public string $index = '';

    // 间隔多少秒上报一次
    public int $interval = 300;

    // 满足多少条上报一次
    public int $batch_num = 50;
}
