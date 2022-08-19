<?php

namespace ReportAgent;


use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use ReportAgent\Entity\MessageEntity;

/**
 * 初始化消息体
 *
 * @author xiaowei@yuanxinjituan.com
 */
class MessageFactory
{
    /**
     * @var mixed
     */
    protected $appId;
    /**
     * @var mixed
     */
    protected $config;

    /**
     * @param \Psr\Container\ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $this->config = $config->get('report.php');
        $this->appId = $this->config['app_id'];
    }

    /**
     * 生成消息体
     *
     * @param string $type    上报类型
     * @param array  $options 上报参数
     *
     * @return \ReportAgent\Entity\MessageEntity
     * @author xiaowei@yuanxinjituan.com
     */
    public function produce(string $type, array $options)
    {
        return new MessageEntity(array_merge(
            $this->config[$type],
            $options,
            [
                'app_id' => $this->appId,
                'type' => $type,
                'message_id' => $this->messageId(),
                'client_ip' => $this->clientIp(),
                'created_at' => date('Y-m-d H:i:s', time()),
            ]));
    }

    /**
     * 获取消息ID
     *
     * @return string
     * @author xiaowei@yuanxinjituan.com
     */
    public function messageId()
    {
        return (new \Hidehalo\Nanoid\Client())->generateId(21, \Hidehalo\Nanoid\Client::MODE_DYNAMIC);
    }

    /**
     * 获取客户端IP
     *
     * @return mixed
     * @author xiaowei@yuanxinjituan.com
     */
    public function clientIp()
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
}