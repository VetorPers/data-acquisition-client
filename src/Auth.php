<?php

namespace ReportAgent;

class Auth
{
    /**
     * @param string $secret 密钥.
     * @param array  $data   签名的数据.
     *
     * @return string
     * @author xionglin
     */
    public static function sign(string $secret, array $data): string
    {
        $data['secret'] = $secret;
        ksort($data);

        return strtolower(md5(urldecode(http_build_query($data))));
    }
}
