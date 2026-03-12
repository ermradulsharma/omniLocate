<?php

declare(strict_types=1);

namespace Skywalker\Location\Drivers;

class IpApiPro extends IpApi
{
    /**
     * {@inheritdoc}
     */
    protected function url(string $ip): string
    {
        $config = config('location.ip_api.token');
        $key = is_string($config) ? $config : '';

        return "https://pro.ip-api.com/json/{$ip}?key={$key}";
    }
}

