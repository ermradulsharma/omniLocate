<?php

namespace Ermradulsharma\OmniLocate\Middleware;

use Closure;
use Ermradulsharma\OmniLocate\Facades\Location;

class LocationMiddleware
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
        if ($position = Location::get($request->ip())) {
            $request->merge(['location' => $position]);
        }

        return $next($request);
    }
}
