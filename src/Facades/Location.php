<?php

namespace Skywalker\Location\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Skywalker\Location\DataTransferObjects\Position|bool get(string $ip = null)
 * @method static void setDriver(\Skywalker\Location\Drivers\Driver $driver)
 * @method static void fallback(\Skywalker\Location\Drivers\Driver $driver)
 * @method static bool isBot()
 * @method static bool isVerifiedBot()
 * @method static bool inCountry(string $code)
 *
 * @see \Skywalker\Location\Location
 */
class Location extends Facade
{
    /**
     * The IoC key accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'location';
    }
}

