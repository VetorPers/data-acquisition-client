<?php

namespace ReportAgent\Entity;

/**
 * 消息实体.
 */
class MessageEntity extends Entity
{
    // 告警
    const MESSAGE_TYPE_ALARM = 'alarm';

    // 埋点
    const MESSAGE_TYPE_BURY = 'bury';

    // 应用id
    public string $app_id = '';

    // 消息类型
    public string $message_type = '';

    // 业务标示
    public string $bus_tag = '';

    // 内容
    public array $content = [];

    // 客户端ip
    public string $client_ip = '';

    // 发送时间
    public string $time = '';

    // 消息ID
    public string $message_id = '';

    // 版本
    public string $version = '1.0.2';

    // 其他参数
    public array $options = [];
}
