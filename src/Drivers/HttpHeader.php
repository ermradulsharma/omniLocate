<?php

namespace Ermradulsharma\OmniLocate\Drivers;

use Illuminate\Support\Fluent;
use Ermradulsharma\OmniLocate\Position;

class HttpHeader extends Driver
{
    /**
     * Map of headers to Position properties.
     *
     * @var array
     */
    protected $headersParts = [
        'cf-ipcountry' => 'countryCode',
        'x-country-code' => 'countryCode',
        'x-region-code' => 'regionCode',
        'x-city-name' => 'cityName',
    ];

    /**
     * {@inheritdoc}
     */
    protected function url($ip)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrate(Position $position, Fluent $location)
    {
        $position->countryCode = $location->countryCode;
        $position->regionCode = $location->regionCode;
        $position->cityName = $location->cityName;

        return $position;
    }

    /**
     * {@inheritdoc}
     */
    protected function process($ip)
    {
        $data = [];

        foreach ($this->headersParts as $header => $property) {
            if ($value = request()->header($header)) {
                $data[$property] = $value;
            }
        }

        return count($data) > 0 ? new Fluent($data) : false;
    }
}
