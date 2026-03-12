<?php

namespace Skywalker\Location\Http\Middleware;

use Closure;
use Skywalker\Location\Facades\Location;
use Skywalker\Location\Support\Concerns\ApiResponse;

class GeoRiskGuard
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|null $threshold
     * @return mixed
     */
    public function handle($request, Closure $next, $threshold = null)
    {
        $position = Location::get();

        if ($position instanceof \Skywalker\Location\DataTransferObjects\Position && $position->geoRiskScore !== null) {
            $threshold = $threshold ?: config('location.risk.threshold', 80);
            $threshold = is_numeric($threshold) ? (int) $threshold : 80;

            if ($position->geoRiskScore >= $threshold) {
                return $this->apiError('Access Denied: High Risk IP.', 403);
            }
        }

        return $next($request);
    }
}
