<?php

namespace YuanxinHealthy\DataAcquisitionClient;


use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;
use YuanxinHealthy\DataAcquisitionClient\Entity\MessageEntity;
use YuanxinHealthy\DataAcquisitionClient\Exception\InvalidConfigException;

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
    protected $secret;

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
            $this->secret = $config->get('acquisition')['secret'];
        } catch (\Exception $exception) {
            throw new InvalidConfigException('lack app id or secret config');
        }

        if (empty($this->appId) || empty($this->secret)) {
            throw new InvalidConfigException('app id or secret config is empty');
        }
    }

    /**
     * 生成消息体.
     *
     * @param string $type    上报类型
     * @param array  $options 上报参数
     *
     * @return MessageEntity
     * @author xiaowei@yuanxinjituan.com
     */
    public function produce(string $type, array $options)
    {
        $time = time();
        $messageId = $this->messageId($time);
        $time = date('Y-m-d H:i:s', $time);
        // 获取签名
        $sign = Auth::sign($this->secret, [
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
                'time' => $time,
                'sign' => $sign,
            ]));
    }

    /**
     * 获取消息ID.
     *
     * @param int|null $time 当前时间戳.
     *
     * @return string
     * @author xiaowei@yuanxinjituan.com
     */
    public function messageId(int $time = null)
    {
        // 便于区分消息，返回自增id
        return mb_substr($this->appId, -5)
            . date('YmdHis', $time ?? time())
            . mt_rand(9000, 9999);
    }
}
