<?php

declare(strict_types=1);

namespace Skywalker\Location\Drivers;

use Exception;
use Illuminate\Support\Fluent;
use Skywalker\Location\DataTransferObjects\Position;

class GeoPlugin extends Driver
{
    /**
     * {@inheritdoc}
     */
    protected function url(string $ip): string
    {
        return "https://www.geoplugin.net/json.gp?ip={$ip}";
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrate(Position $position, Fluent $location): Position
    {
        $position->countryCode = $this->getString($location, 'geoplugin_countryCode');
        $position->countryName = $this->getString($location, 'geoplugin_countryName');
        $position->regionName = $this->getString($location, 'geoplugin_regionName');
        $position->regionCode = $this->getString($location, 'geoplugin_regionCode');
        $position->cityName = $this->getString($location, 'geoplugin_city');
        $position->latitude = $this->getString($location, 'geoplugin_latitude');
        $position->longitude = $this->getString($location, 'geoplugin_longitude');
        $position->areaCode = $this->getString($location, 'geoplugin_areaCode');
        $position->timezone = $this->getString($location, 'geoplugin_timezone');

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

