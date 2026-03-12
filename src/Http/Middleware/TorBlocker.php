<?php

namespace Skywalker\Location\Http\Middleware;

use Closure;
use Skywalker\Location\Facades\Location;
use Skywalker\Location\Support\Concerns\ApiResponse;

class TorBlocker
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
        // Only verify if enforcement is enabled
        if ((bool) config('location.tor.block', false)) {
            $position = Location::get();

            if ($position instanceof \Skywalker\Location\DataTransferObjects\Position && $position->isTor) {
                return $this->apiError('Access Denied: Tor Network not allowed.', 403);
            }
        }

        return $next($request);
    }
}
