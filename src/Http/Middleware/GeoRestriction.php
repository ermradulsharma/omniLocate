<?php

namespace Skywalker\Location\Http\Middleware;

use Closure;
use Skywalker\Location\Facades\Location;
use Skywalker\Location\Support\Concerns\ApiResponse;

class GeoRestriction
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
        $position = Location::get();

        if ($position instanceof \Skywalker\Location\DataTransferObjects\Position) {
            $allowedConfig = config('location.restriction.allowed_countries');
            $blockedConfig = config('location.restriction.blocked_countries');
            $allowed = is_array($allowedConfig) ? $allowedConfig : [];
            $blocked = is_array($blockedConfig) ? $blockedConfig : [];

            if (!empty($allowed) && !in_array($position->countryCode, $allowed)) {
                return $this->apiError('Access Denied from your country.', 403);
            }

            if (!empty($blocked) && in_array($position->countryCode, $blocked)) {
                return $this->apiError('Access Denied from your country.', 403);
            }
        }

        return $next($request);
    }
}
