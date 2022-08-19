<?php

namespace ReportAgent;


use ReportAgent\Entity\BuryConfEntity;
use ReportAgent\Entity\Entity;
use ReportAgent\Entity\MessageEntity;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use ReportAgent\Client\Client;

class Report
{
    protected $client;
    protected $messageFactory;

    public function __construct(ContainerInterface $container)
    {
        $this->client = $container->get(Client::class);
        $this->messageFactory = $container->get(Message::class);
    }

    /**
     * @param array{bug_tag: string, content: string} $data
     *
     * @return void
     * @author xiaowei@yuanxinjituan.com
     */
    public function buryPoint(array $data)
    {
        $message = $this->messageFactory->produce(MessageEntity::MESSAGE_TYPE_BURY, $data);

        return $this->client->send($message);
    }

    public function alarm($data)
    {
        $message = $this->messageFactory->produce(MessageEntity::MESSAGE_TYPE_ALARM, $data);

        return $this->client->send($message);
    }
}
