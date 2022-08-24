<?php

namespace YuanxinHealthy\DataAcquisitionClient\Entity;

/**
 * 消息实体.
 */
class MessageEntity extends Entity
{
    // 告警
    const MESSAGE_TYPE_ALARM = 'alarm';

    // 埋点
    const MESSAGE_TYPE_BURY = 'bury';

    // 版本
    const VERSION = '1.0.8';

    // 应用id
    public string $app_id = '';

    // 消息类型
    public string $message_type = '';

    // 业务标示
    public string $bus_tag = '';

    // 内容
    public array $content = [];

    // 发送时间
    public string $time = '';

    // 消息ID
    public string $message_id = '';

    // 版本
    public string $version = self::VERSION;

    // 签名
    public string $sign = '';

    // 其他参数
    public array $options = [];
}
