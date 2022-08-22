<?php

namespace ReportAgent;

use ReportAgent\Entity\MessageEntity;
use Psr\Container\ContainerInterface;
use ReportAgent\Client\Client;

/**
 * @author xiaowei@yuanxinjituan.com
 */
class Report
{
    /**
     * @var mixed|\ReportAgent\Client\Client
     */
    protected $client;
    /**
     * @var mixed|\ReportAgent\MessageFactory
     */
    protected $messageFactory;

    /**
     * @param \Psr\Container\ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->client = $container->get(Client::class);
        $this->messageFactory = $container->get(MessageFactory::class);
    }

    /**
     * 埋点
     *
     * @param mixed  $content 内容
     * @param string $busTag  业务标识
     * @param array  $options 其他参数
     *
     * @return mixed
     * @author xiaowei@yuanxinjituan.com
     */
    public function buryPoint(mixed $content, string $busTag, array $options = [])
    {
        $message = $this->messageFactory->produce(
            MessageEntity::MESSAGE_TYPE_BURY,
            array_merge($options, [
                'bus_tag' => $busTag,
                'content' => $content,
            ])
        );

        return $this->client->send($message);
    }

    /**
     * 告警
     *
     * @param mixed  $content 内容
     * @param string $busTag  业务标识
     * @param array  $options 其他参数
     *
     * @return mixed
     * @author xiaowei@yuanxinjituan.com
     */
    public function alarm(mixed $content, string $busTag, array $options = [])
    {
        $message = $this->messageFactory->produce(
            MessageEntity::MESSAGE_TYPE_ALARM,
            array_merge($options, [
                'bus_tag' => $busTag,
                'content' => $content,
            ])
        );

        return $this->client->send($message);
    }
}
