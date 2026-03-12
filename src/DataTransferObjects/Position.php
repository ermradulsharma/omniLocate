<?php

declare(strict_types=1);

namespace Skywalker\Location\DataTransferObjects;

use Skywalker\Support\Foundation\Dto;

class Position extends Dto
{
    /**
     * The IP address used to retrieve the location.
     */
    public ?string $ip = null;

    /**
     * The country name.
     */
    public ?string $countryName = null;

    /**
     * The country code.
     */
    public ?string $countryCode = null;

    /**
     * The region code.
     */
    public ?string $regionCode = null;

    /**
     * The region name.
     */
    public ?string $regionName = null;

    /**
     * The city name.
     */
    public ?string $cityName = null;

    /**
     * The zip code.
     */
    public ?string $zipCode = null;

    /**
     * The iso code.
     */
    public ?string $isoCode = null;

    /**
     * The postal code.
     */
    public ?string $postalCode = null;

    /**
     * The latitude.
     */
    public ?string $latitude = null;

    /**
     * The longitude.
     */
    public ?string $longitude = null;

    /**
     * The metro code.
     */
    public ?string $metroCode = null;

    /**
     * The area code.
     */
    public ?string $areaCode = null;

    /**
     * The timezone.
     */
    public ?string $timezone = null;

    /**
     * The currency code.
     */
    public ?string $currencyCode = null;

    /**
     * The driver used for retrieving the location.
     */
    public ?string $driver = null;

    /**
     * True if IP is a proxy.
     */
    public ?bool $isProxy = null;

    /**
     * True if IP is a VPN.
     */
    public ?bool $isVpn = null;

    /**
     * True if IP is a Tor exit node.
     */
    public ?bool $isTor = null;

    /**
     * True if IP belongs to a hosting provider.
     */
    public ?bool $isHosting = null;

    /**
     * The Geo risk score (0-100).
     */
    public ?int $geoRiskScore = null;

    /**
     * The ISP name.
     */
    public ?string $isp = null;

    /**
     * The Autonomous System Number.
     */
    public ?string $asn = null;

    /**
     * The Organization or Network Owner.
     */
    public ?string $org = null;

    /**
     * The connection type (e.g., specific, corporate, mobile).
     */
    public ?string $connectionType = null;

    /**
     * The Language code.
     */
    public ?string $language = null;

    /**
     * Determine if the position is empty.
     */
    public function isEmpty(): bool
    {
        $data = $this->toArray();

        unset($data['ip'], $data['driver']);

        return empty(array_filter($data));
    }

    /**
     * Get the distance to another position.
     */
    public function distanceTo(self $other, string $unit = 'km'): ?float
    {
        if (! $this->latitude || ! $this->longitude || ! $other->latitude || ! $other->longitude) {
            return null;
        }

        $theta = (float) $this->longitude - (float) $other->longitude;

        $dist = sin(deg2rad((float) $this->latitude)) * sin(deg2rad((float) $other->latitude)) + cos(deg2rad((float) $this->latitude)) * cos(deg2rad((float) $other->latitude)) * cos(deg2rad($theta));

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
     * Get the country flag emoji.
     */
    public function flag(): ?string
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
