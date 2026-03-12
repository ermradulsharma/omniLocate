<?php

declare(strict_types=1);

namespace Skywalker\Location\Actions;

use Skywalker\Support\Foundation\Action;

class VerifyBot extends Action
{
    /**
     * Execute the action.
     *
     * @param  mixed  ...$args
     * @return bool
     */
    public function execute(...$args): bool
    {
        $ip = isset($args[0]) && is_string($args[0]) ? $args[0] : '';
        $agent = strtolower(isset($args[1]) && is_string($args[1]) ? $args[1] : '');

        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        return (bool) cache()->remember("bot_verified.$ip", 3600, function () use ($ip, $agent) {
            $hostname = gethostbyaddr($ip);

            if (! $hostname || $hostname === $ip) {
                return false;
            }

            /** @var array<string, array<int, string>> $trusted */
            $trusted = config('location.bots.trusted_domains', [
                'googlebot' => ['.googlebot.com', '.google.com'],
                'bingbot' => ['.search.msn.com'],
                'slurp' => ['.crawl.yahoo.net'],
                'duckduckbot' => ['.duckduckgo.com'],
                'yandexbot' => ['.yandex.com', '.yandex.ru', '.yandex.net'],
                'baiduspider' => ['.baidu.com', '.baidu.jp'],
            ]);

            foreach ($trusted as $botKey => $domains) {
                if (str_contains($agent, (string) $botKey)) {
                    foreach ((array) $domains as $domain) {
                        if (str_ends_with((string) $hostname, (string) $domain)) {
                            $resolvedIps = gethostbynamel((string) $hostname);
                            if ($resolvedIps && in_array($ip, (array) $resolvedIps, true)) {
                                return true;
                            }
                        }
                    }
                }
            }

            return false;
        });
    }
}
