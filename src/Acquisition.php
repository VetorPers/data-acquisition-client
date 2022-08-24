<?php

namespace YuanxinHealthy\DataAcquisitionClient;

use YuanxinHealthy\DataAcquisitionClient\Entity\MessageEntity;
use Psr\Container\ContainerInterface;
use YuanxinHealthy\DataAcquisitionClient\Client\Client;

/**
 * 上报类.
 *
 * @author xiaowei@yuanxinjituan.com
 */
class Acquisition
{
    /**
     * 连接端.
     *
     * @var Client
     */
    protected $client;
    /**
     * 消息工厂.
     *
     * @var MessageFactory
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
     * 埋点.
     *
     * @param mixed  $content 内容.
     * @param string $busTag  业务标识.
     * @param array  $options 其他参数.
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
                'options' => $options,
            ])
        );

        return $this->client->send($message);
    }

    /**
     * 告警.
     *
     * @param mixed  $content 内容.
     * @param string $busTag  业务标识.
     * @param array  $options 其他参数.
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
                'options' => $options,
            ])
        );

        return $this->client->send($message);
    }

    /**
     * 跟踪.
     *
     * @param string $busTag     业务标识.
     * @param string $distinctId 用户标识.
     * @param bool   $isLogin    是否登录.
     * @param string $event      事件名.
     * @param array  $properties 自定义属性.
     * @param array  $options    其他参数.
     *
     * @return bool
     * @author xiaowei@yuanxinjituan.com
     */
    public function track(
        string $busTag,
        string $distinctId,
        bool $isLogin,
        string $event,
        array $properties,
        array $options = []
    ) {
        // 消息内容
        $content = [
            'type' => 'track',
            'distinct_id' => $distinctId,
            'event' => $event,
            'is_login' => $isLogin,
            'properties' => $properties,
        ];

        $message = $this->messageFactory->produce(
            MessageEntity::MESSAGE_TYPE_BURY,
            array_merge($options, [
                'bus_tag' => $busTag,
                'content' => $content,
                'options' => $options,
            ])
        );

        return $this->client->send($message);
    }
}
