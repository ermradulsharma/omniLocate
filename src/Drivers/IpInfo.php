<?php

declare(strict_types=1);

namespace Skywalker\Location\Drivers;

use Exception;
use Illuminate\Support\Fluent;
use Skywalker\Location\DataTransferObjects\Position;

class IpInfo extends Driver
{
    /**
     * {@inheritdoc}
     */
    protected function url(string $ip): string
    {
        $url = "https://ipinfo.io/{$ip}";

        if ($token = config('location.ipinfo.token')) {
            $url .= '?token=' . $token;
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrate(Position $position, Fluent $location): Position
    {
        $position->countryCode = $this->getString($location, 'country');
        $position->regionName = $this->getString($location, 'region');
        $position->cityName = $this->getString($location, 'city');
        $position->zipCode = $this->getString($location, 'postal');
        $position->timezone = $this->getString($location, 'timezone');

        if ($loc = $this->getString($location, 'loc')) {
            $coords = explode(',', $loc);

            if (array_key_exists(0, $coords)) {
                $position->latitude = $coords[0];
            }

            if (array_key_exists(1, $coords)) {
                $position->longitude = $coords[1];
            }
        }

        return $position;
    }

    /**
     * {@inheritdoc}
     */
    protected function process(string $ip)
    {
        try {
            $content = $this->getUrlContent($this->url($ip));
            $response = json_decode((string) $content, true);

            return new Fluent(is_array($response) ? $response : []);
        } catch (Exception $e) {
            return false;
        }
    }
}

