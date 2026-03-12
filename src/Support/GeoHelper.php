<?php

declare(strict_types=1);

namespace Skywalker\Location\Support;

use Skywalker\Location\Facades\Location;
use Skywalker\Location\DataTransferObjects\Position;

class GeoHelper
{
    /**
     * Get a rate limit key based on the user's country.
     */
    public static function rateLimitKey(string $prefix = 'geo_limit'): string
    {
        /** @var Position|bool $position */
        $position = Location::get();

        $country = $position instanceof Position ? $position->countryCode : 'unknown';
        $ip = $position instanceof Position ? $position->ip : (string) request()->ip();

        return "{$prefix}:{$country}:{$ip}";
    }

    /**
     * Check if the user is in a high risk country.
     */
    public static function isHighRiskCountry(): bool
    {
        /** @var Position|bool $position */
        $position = Location::get();

        if (! $position instanceof Position) {
            return false;
        }

        $highRisk = (array) config('location.risk.high_risk_countries', []);

        return in_array($position->countryCode, $highRisk, true);
    }

    /**
     * Get the recommended rate limit points based on risk.
     */
    public static function getRateLimitPoints(): int
    {
        /** @var Position|bool $position */
        $position = Location::get();

        // Default safe limit
        if (! $position instanceof Position) {
            return 60;
        }

        if (Location::isVerifiedBot()) {
            return 1000;
        }

        $risk = $position->geoRiskScore ?? 0;

        if ($risk >= 70) {
            return 5;
        }

        if ($risk >= 30) {
            return 20;
        }

        return 100;
    }
}

