<?php

namespace ReportAgent\Client;


use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use ReportAgent\Entity\MessageEntity;

/**
 * 客户端
 *
 * @author xiaowei@yuanxinjituan.com
 */
class Client
{
    /**
     * @var mixed 上报服务端ip
     */
    protected $ip;
    /**
     * @var mixed 上报服务端端口
     */
    protected $port;

    /**
     * @param \Psr\Container\ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $serverConfig = $config->get('report.server');
        $this->ip = $serverConfig['ip'];
        $this->port = $serverConfig['port'];
    }

    /**
     * 上报
     *
     * @param \ReportAgent\Entity\MessageEntity $data 上报数据
     *
     * @return mixed
     * @author xiaowei@yuanxinjituan.com
     */
    public function send(MessageEntity $data)
    {
        $client = new \Swoole\Client(SWOOLE_SOCK_UDP);
        $client->sendTo($this->ip, $this->port, $data);
        $data = $client->recv();
        var_dump($data);

        return $data;
    }
}
