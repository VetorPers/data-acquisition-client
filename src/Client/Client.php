<?php

namespace YuanxinHealthy\DataAcquisitionClient\Client;


use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use YuanxinHealthy\DataAcquisitionClient\Entity\MessageEntity;
use YuanxinHealthy\DataAcquisitionClient\Exception\InvalidConfigException;
use YuanxinHealthy\DataAcquisitionClient\Exception\ReportFailException;

/**
 * 客户端.
 *
 * @author xiaowei@yuanxinjituan.com
 */
class Client
{
    /**
     * @var mixed 上报服务端域名.
     */
    protected $domain;
    /**
     * @var mixed 上报服务端端口.
     */
    protected $port;
    /**
     * @var mixed 是否调试模式
     */
    protected $debug;

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
            $this->domain = $config->get('acquisition')['domain'];
            $this->debug = $config->get('acquisition')['debug'];
        } catch (\Exception $exception) {
            throw new InvalidConfigException('lack domain config');
        }
        $this->port = 9506;
    }

    /**
     * 上报.
     *
     * @param MessageEntity $data 上报数据.
     *
     * @return bool
     * @author xiaowei@yuanxinjituan.com
     */
    public function send(MessageEntity $data)
    {
        $client = new \Swoole\Client(SWOOLE_SOCK_UDP);
        if (!$client->connect($this->domain, $this->port, 0.5)) {
            throw new InvalidConfigException('connect fail');
        }
        $client->send(json_encode($data));

        // 调试模式，获取响应
        if ($this->debug) {
            $this->recv($client);
        }
    }

    /**
     * 接受数据
     *
     * @param \Swoole\Client $client 客户端
     *
     * @return mixed
     * @author xiaowei@yuanxinjituan.com
     */
    public function recv(\Swoole\Client $client)
    {
        while (true) {
            $data = $client->recv();
            if (strlen($data) > 0) {
                break;
            } else {
                if ($data === '') {
                    // 全等于空 直接关闭连接
                    $client->close();
                    break;
                } else {
                    if ($data === false) {
                        // 可以自行根据业务逻辑和错误码进行处理，例如：
                        // 如果超时时则不关闭连接，其他情况直接关闭连接
                        if ($client->errCode !== SOCKET_ETIMEDOUT) {
                            $client->close();
                            break;
                        }
                    } else {
                        $client->close();
                        break;
                    }
                }
            }

            sleep(1);
        }

        $data = empty($data) ? [] : json_decode($data, true);
        if (isset($data['result']) && $data['result']) {
            return $data;
        }
        throw new ReportFailException($data['msg'] ?? 'report fail');
    }
}
