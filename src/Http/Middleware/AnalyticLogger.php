<?php

namespace Skywalker\Location\Http\Middleware;

use Closure;
use Skywalker\Location\Facades\Location;
use Skywalker\Location\Models\GeoAnalytics;

class AnalyticLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Perform logging after response is sent (using terminate or just after next)
        // Ideally this should be queued or done in terminate() if using TerminableMiddleware
        try {
            $position = Location::get();

            if ($position instanceof \Skywalker\Location\DataTransferObjects\Position) {
                GeoAnalytics::create([
                    'ip' => $position->ip,
                    'country_code' => $position->countryCode,
                    'city' => $position->cityName,
                    'isp' => $position->isp,
                    'is_proxy' => $position->isProxy ?? false,
                    'is_vpn' => $position->isVpn ?? false,
                    'is_tor' => $position->isTor ?? false,
                    'risk_score' => $position->geoRiskScore,
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]);
            }
        } catch (\Exception $e) {
            // diverse error handling
        }

        return $response;
    }
}

