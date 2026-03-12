<?php

namespace Skywalker\Location\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Skywalker\Location\Support\GeoHelper;

class AdaptiveRateLimit extends ThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|string  $maxAttempts
     * @param  float|int  $decayMinutes
     * @param  string  $prefix
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        // Get adaptive limit based on Geo Risk
        $maxAttempts = GeoHelper::getRateLimitPoints();

        return parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
    }

    /**
     * Resolve the number of attempts if the usage of the middleware is not using the default points.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        return sha1((string) $request->ip());
    }
}
