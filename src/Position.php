<?php

namespace Ermradulsharma\OmniLocate;

use Illuminate\Contracts\Support\Arrayable;

class Position implements Arrayable
{
    /**
     * The IP address used to retrieve the location.
     *
     * @var string
     */
    public $ip;

    /**
     * The country name.
     *
     * @var string|null
     */
    public $countryName;

    /**
     * The country code.
     *
     * @var string|null
     */
    public $countryCode;

    /**
     * The region code.
     *
     * @var string|null
     */
    public $regionCode;

    /**
     * The region name.
     *
     * @var string|null
     */
    public $regionName;

    /**
     * The city name.
     *
     * @var string|null
     */
    public $cityName;

    /**
     * The zip code.
     *
     * @var string|null
     */
    public $zipCode;

    /**
     * The iso code.
     *
     * @var string|null
     */
    public $isoCode;

    /**
     * The postal code.
     *
     * @var string|null
     */
    public $postalCode;

    /**
     * The latitude.
     *
     * @var string|null
     */
    public $latitude;

    /**
     * The longitude.
     *
     * @var string|null
     */
    public $longitude;

    /**
     * The metro code.
     *
     * @var string|null
     */
    public $metroCode;

    /**
     * The area code.
     *
     * @var string|null
     */
    public $areaCode;

    /**
     * The timezone.
     *
     * @var string|null
     */
    public $timezone;

    /**
     * The currency code.
     *
     * @var string|null
     */
    public $currencyCode;

    /**
     * The driver used for retrieving the location.
     *
     * @var string|null
     */
    public $driver;

    /**
     * Determine if the position is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        $data = $this->toArray();

        unset($data['ip']);
        unset($data['driver']);

        return empty(array_filter($data));
    }

    /**
     * Get the distance to another position.
     *
     * @param  Position  $other
     * @param  string  $unit
     * @return float|null
     */
    public function distanceTo(Position $other, $unit = 'km')
    {
        if (! $this->latitude || ! $this->longitude || ! $other->latitude || ! $other->longitude) {
            return null;
        }

        $theta = $this->longitude - $other->longitude;

        $dist = sin(deg2rad($this->latitude)) * sin(deg2rad($other->latitude)) + cos(deg2rad($this->latitude)) * cos(deg2rad($other->latitude)) * cos(deg2rad($theta));

        $dist = acos($dist);
        $dist = rad2deg($dist);

        $miles = $dist * 60 * 1.1515;
        $unit = strtolower($unit);

        if ($unit === 'km') {
            return $miles * 1.609344;
        }

        return $miles;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * Get the country flag emoji.
     *
     * @return string|null
     */
    public function flag()
    {
        if (! $this->countryCode || strlen($this->countryCode) !== 2) {
            return null;
        }

        $code = strtoupper($this->countryCode);

        $flag = '';

        foreach (str_split($code) as $char) {
            $flag .= mb_chr(ord($char) + 127397);
        }

        return $flag;
    }
}
