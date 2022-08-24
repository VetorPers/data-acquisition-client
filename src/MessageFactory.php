<?php

namespace ReportAgent;


use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;
use ReportAgent\Entity\MessageEntity;
use ReportAgent\Exception\InvalidConfigException;

/**
 * 初始化消息体.
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
    protected $secrect;

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
            $this->appId = $config->get('acquisition')['app_id'];
            $this->secrect = $config->get('acquisition')['secrect'];
        } catch (\Exception $exception) {
            throw new InvalidConfigException('lack app id config');
        }
    }

    /**
     * 生成消息体.
     *
     * @param string $type    上报类型
     * @param array  $options 上报参数
     *
     * @return \ReportAgent\Entity\MessageEntity
     * @author xiaowei@yuanxinjituan.com
     */
    public function produce(string $type, array $options)
    {
        $time = date('Y-m-d H:i:s', time());
        $messageId = $this->messageId();
        // 获取签名
        $sign = Auth::sign($this->secrect, [
            'app_id' => $this->appId,
            'time' => $time,
            'version' => MessageEntity::VERSION,
            'message_id' => $messageId,
        ]);

        return new MessageEntity(array_merge(
            $options,
            [
                'app_id' => $this->appId,
                'message_type' => $type,
                'message_id' => $messageId,
                'client_ip' => $this->clientIp(),
                'time' => $time,
                'sign' => $sign,
            ]));
    }

    /**
     * 获取消息ID.
     *
     * @return string
     * @author xiaowei@yuanxinjituan.com
     */
    public function messageId()
    {
        // 便于区分消息，返回自增id
        //        return (new \Hidehalo\Nanoid\Client())->generateId(21, \Hidehalo\Nanoid\Client::MODE_DYNAMIC);
        return time() . mt_rand(9000, 9999);
    }

    /**
     * 获取客户端IP.
     *
     * @return mixed|string
     * @author xiaowei@yuanxinjituan.com
     */
    public function clientIp()
    {
        $request = ApplicationContext::getContainer()->get(RequestInterface::class);
        $headers = $request->getHeaders();

        if (isset($headers['x-forwarded-for'][0]) && !empty($headers['x-forwarded-for'][0])) {
            return $headers['x-forwarded-for'][0];
        } elseif (isset($headers['x-real-ip'][0]) && !empty($headers['x-real-ip'][0])) {
            return $headers['x-real-ip'][0];
        }

        $serverParams = $request->getServerParams();

        return $serverParams['remote_addr'] ?? '';
    }
}
