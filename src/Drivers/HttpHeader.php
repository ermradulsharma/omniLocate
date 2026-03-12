<?php

declare(strict_types=1);

namespace Skywalker\Location\Drivers;

use Illuminate\Support\Fluent;
use Skywalker\Location\DataTransferObjects\Position;

class HttpHeader extends Driver
{
    /**
     * Map of headers to Position properties.
     *
     * @var array<string, string>
     */
    protected array $headersParts = [
        'cf-ipcountry' => 'countryCode',
        'x-country-code' => 'countryCode',
        'x-region-code' => 'regionCode',
        'x-city-name' => 'cityName',
    ];

    /**
     * {@inheritdoc}
     */
    protected function url(string $ip): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrate(Position $position, Fluent $location): Position
    {
        $position->countryCode = $this->getString($location, 'countryCode');
        $position->regionCode = $this->getString($location, 'regionCode');
        $position->cityName = $this->getString($location, 'cityName');

        return $position;
    }

    /**
     * {@inheritdoc}
     */
    protected function process(string $ip)
    {
        /** @var array<string, mixed> $data */
        $data = [];

        foreach ($this->headersParts as $header => $property) {
            if ($value = request()->header($header)) {
                $data[$property] = $value;
            }
        }

        if (count($data) === 0) {
            return false;
        }

        return new Fluent($data);
    }
}

