<?php

namespace Skywalker\Location\Http\Middleware;

use Closure;
use Skywalker\Location\Facades\Location;

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

