<?php

namespace ReportAgent;


use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use ReportAgent\Entity\MessageEntity;

class Message
{
    protected $appId;
    protected $config;

    public function __construct(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $this->config = $config->get('report.php');
        $this->appId = $this->config['app_id'];
    }

    public function produce($type, $options)
    {
        return new MessageEntity(array_merge($this->config[$type], $options, [
            'app_id' => $this->appId,
            'type' => $type,
            'message_id' => $this->messageId(),
            'client_ip' => $this->clientIp(),
            'created_at' => date('Y-m-d H:i:s', time()),
        ]));
    }

    public function messageId()
    {
        return (new \Hidehalo\Nanoid\Client())->generateId(21, \Hidehalo\Nanoid\Client::MODE_DYNAMIC);
    }

    public function clientIp()
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
}
