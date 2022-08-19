<?php

namespace ReportAgent\Entity;

/**
 * 消息实体
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

    // 创建时间
    public string $created_at = '';

    // 消息ID
    public string $message_id = '';

    /**
     * @param string $clientIp 客户端ip.
     * @return MessageEntity
     */
    public function setClientIp(string $clientIp): self
    {
        $this->client_ip = $clientIp;
        return $this;
    }

    /**
     * @param string $createdAt 创建时间.
     * @return MessageEntity
     */
    public function setCreatedAt(string $createdAt): self
    {
        $this->created_at = $createdAt;

        return $this;
    }
}
