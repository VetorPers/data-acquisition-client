<?php

namespace ReportAgent\Client;


use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use ReportAgent\Entity\MessageEntity;
use ReportAgent\Exception\InvalidConfigException;
use ReportAgent\Exception\ReportFailException;

/**
 * 客户端
 *
 * @author xiaowei@yuanxinjituan.com
 */
class Client
{
    /**
     * @var mixed 上报服务端域名
     */
    protected $domain;
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
        try {
            $config = $container->get(ConfigInterface::class);
            $this->domain = $config->get('report')['domain'];
        } catch (\Exception $exception) {
            throw new InvalidConfigException('lack domain config');
        }
        $this->port = 9506;
    }

    /**
     * 上报
     *
     * @param \ReportAgent\Entity\MessageEntity $data 上报数据
     *
     * @return bool
     * @author xiaowei@yuanxinjituan.com
     */
    public function send(MessageEntity $data)
    {
        $client = new \Swoole\Client(SWOOLE_SOCK_UDP);
        $ips = \Swoole\Coroutine\System::getaddrinfo($this->domain);
        // 域名解析失败
        if (!$ips) throw new InvalidConfigException('domain config valid');
        $ips = (array)$ips;
        $client->sendTo($ips[0], $this->port, json_encode($data));

        // 等待响应
        while (true) {
            $ret = @$client->recv();
            if (strlen($ret) > 0) {
                break;
            }
            sleep(1);
        }

        if (is_string($ret) && $ret == 'success') {
            return true;
        }

        throw new ReportFailException('report fail');
    }
}
