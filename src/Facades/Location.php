<?php

namespace Ermradulsharma\OmniLocate\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ermradulsharma\OmniLocate\Position|bool get(string $ip = null)
 * @method static void setDriver(\Ermradulsharma\OmniLocate\Drivers\Driver $driver)
 * @method static void fallback(\Ermradulsharma\OmniLocate\Drivers\Driver $driver)
 *
 * @see \Ermradulsharma\OmniLocate\Location
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
