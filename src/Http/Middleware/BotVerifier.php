<?php

namespace Skywalker\Location\Http\Middleware;

use Closure;
use Skywalker\Location\Facades\Location;
use Skywalker\Location\Support\Concerns\ApiResponse;

class BotVerifier
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 1. Check if it identifies as a bot (User-Agent check)
        // We can access the protected isBot via reflection or just duplicate simple logic, 
        // but cleaner is to use Location::isBot() if it were public. used protected in Location.php
        // Let's assume we use the new public isVerifiedBot() which calls internal isBot.

        // However, Location logic for isBot is protected. We might need to expose it or relies on isVerifiedBot's returning false for non-bots.
        // Wait, isVerifiedBot returns false if !isBot. So if it returns false, it could be a HUMAN or a FAKE BOT.

        // Let's rely on config list here to detect "Claimed Bot" status, 
        // OR better, update Location.php to make isBot public. 
        // For now, I'll assume I made isBot public or I'll implement a helper here.

        $isClaimedBot = false;
        $agent = strtolower((string) ($request->userAgent() ?? ''));
        $configBots = config('location.bots.list');
        $bots = is_array($configBots) ? $configBots : [];

        foreach ($bots as $bot) {
            if (is_string($bot) && str_contains($agent, strtolower($bot))) {
                $isClaimedBot = true;
                break;
            }
        }

        if ($isClaimedBot) {
            // It claims to be a bot. Let's verify it.
            // Note: Location::isVerifiedBot() needs to be accessible via Facade.
            // Since I added it to Location class, Facade forwards calls.

            // We need to ensure logic: If it claims to be Googlebot, but isVerifiedBot returns false, BLOCK IT.
            // But if isVerifiedBot returns false because it's a bot we don't have trusted domains for?
            // We should only block if it claims to be one of the verify-able bots.

            $trustedDomains = config('location.bots.trusted_domains');
            $verifiableBots = array_keys(is_array($trustedDomains) ? $trustedDomains : []);

            $needsVerification = false;
            foreach ($verifiableBots as $vBot) {
                if (is_string($vBot) && str_contains($agent, $vBot)) {
                    $needsVerification = true;
                    break;
                }
            }

            if ($needsVerification) {
                if (! Location::isVerifiedBot()) {
                    return $this->apiError('Access Denied: Unverified Crawler. Spoofed User-Agent detected.', 403);
                }
            }
        }

        return $next($request);
    }
}
