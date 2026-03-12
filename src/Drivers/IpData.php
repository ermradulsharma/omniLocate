<?php

declare(strict_types=1);

namespace Skywalker\Location\Drivers;

use Exception;
use Illuminate\Support\Fluent;
use Skywalker\Location\DataTransferObjects\Position;

class IpData extends Driver
{
    /**
     * {@inheritdoc}
     */
    protected function url(string $ip): string
    {
        $config = config('location.ip_data.api_key');
        $key = is_string($config) ? $config : '';
        $token = $key; // Assuming $key should be used as $token based on the context of the change.

        return "https://api.ipdata.co/{$ip}?api-key={$token}";
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrate(Position $position, Fluent $location): Position
    {
        $position->countryName = $this->getString($location, 'country_name');
        $position->countryCode = $this->getString($location, 'country_code');
        $position->regionCode = $this->getString($location, 'region_code');
        $position->regionName = $this->getString($location, 'region');
        $position->cityName = $this->getString($location, 'city');
        $position->zipCode = $this->getString($location, 'postal');
        $position->postalCode = $this->getString($location, 'postal');
        $position->latitude = $this->getString($location, 'latitude');
        $position->longitude = $this->getString($location, 'longitude');

        $timezone = $this->getArray($location, 'time_zone');
        $position->timezone = isset($timezone['name']) && (is_string($timezone['name']) || is_numeric($timezone['name'])) ? (string) $timezone['name'] : null;

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

