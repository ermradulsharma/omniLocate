<?php

namespace Skywalker\Location\Services;

use Skywalker\Location\Facades\Location;
use Skywalker\Location\DataTransferObjects\Position;

class HybridVerifier
{
    /**
     * Verify if the user's physical location matches their IP location.
     *
     * @param string $ip
     * @param float $latitude
     * @param float $longitude
     * @return array<string, mixed>
     */
    public function verify($ip, $latitude, $longitude)
    {
        $ip = (string) $ip;
        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        $ipPosition = Location::get($ip);

        if (! ($ipPosition instanceof Position) || $ipPosition->isEmpty()) {
            return [
                'verified' => false,
                'reason' => 'IP location not found',
                'distance' => null,
            ];
        }

        // Create a temporary Position for the GPS coordinates
        $gpsPosition = new Position();
        $gpsPosition->latitude = (string) $latitude;
        $gpsPosition->longitude = (string) $longitude;

        // Calculate distance in kilometers
        $distance = $ipPosition->distanceTo($gpsPosition);

        // Get threshold from config or default to 500km
        $thresholdConfig = config('location.hybrid.threshold', 500);
        $threshold = is_numeric($thresholdConfig) ? (float) $thresholdConfig : 500.0;

        $isSpoofed = $distance > $threshold;

        return [
            'verified' => !$isSpoofed,
            'is_spoofed' => $isSpoofed,
            'distance_km' => round((float) ($distance ?? 0.0), 2),
            'threshold_km' => $threshold,
            'ip_location' => [
                'city' => (string) $ipPosition->cityName,
                'country' => (string) $ipPosition->countryCode,
                'lat' => (string) $ipPosition->latitude,
                'lon' => (string) $ipPosition->longitude,
            ],
            'gps_location' => [
                'lat' => $latitude,
                'lon' => $longitude,
            ],
        ];
    }
}

