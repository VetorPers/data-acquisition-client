<?php

namespace ReportAgent\Client;


use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class Client
{
    protected $ip;
    protected $port;

    public function __construct(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $serverConfig = $config->get('report.server');
        $this->ip = $serverConfig['ip'];
        $this->port = $serverConfig['port'];
    }

    public function send($data)
    {
        $client = new \Swoole\Client(SWOOLE_SOCK_UDP);
        $client->sendTo($this->ip, $this->port, $data);
        $data = $client->recv();
        var_dump($data);
    }
}
