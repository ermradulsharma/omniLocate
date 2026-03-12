<?php

declare(strict_types=1);

namespace Skywalker\Location\Drivers;

use Exception;
use Illuminate\Support\Fluent;
use Skywalker\Location\DataTransferObjects\Position;

class IpApi extends Driver
{
    /**
     * {@inheritdoc}
     */
    protected function url(string $ip): string
    {
        return "https://ip-api.com/json/{$ip}";
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrate(Position $position, Fluent $location): Position
    {
        $position->countryName = $this->getString($location, 'country');
        $position->countryCode = $this->getString($location, 'countryCode');
        $position->regionCode = $this->getString($location, 'region');
        $position->regionName = $this->getString($location, 'regionName');
        $position->cityName = $this->getString($location, 'city');
        $position->zipCode = $this->getString($location, 'zip');
        $position->latitude = $this->getString($location, 'lat');
        $position->longitude = $this->getString($location, 'lon');
        $position->areaCode = $this->getString($location, 'region');
        $position->timezone = $this->getString($location, 'timezone');

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

