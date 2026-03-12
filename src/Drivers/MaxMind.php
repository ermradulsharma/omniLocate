<?php

declare(strict_types=1);

namespace Skywalker\Location\Drivers;

use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\WebService\Client;
use Illuminate\Support\Fluent;
use Skywalker\Location\DataTransferObjects\Position;

class MaxMind extends Driver
{
    /**
     * {@inheritdoc}
     */
    public function url(string $ip): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrate(Position $position, Fluent $location): Position
    {
        $position->countryName = $this->getString($location, 'country');
        $position->countryCode = $this->getString($location, 'country_code');
        $position->isoCode = $this->getString($location, 'country_code');
        $position->regionCode = $this->getString($location, 'regionCode');
        $position->regionName = $this->getString($location, 'regionName');
        $position->cityName = $this->getString($location, 'city');
        $position->postalCode = $this->getString($location, 'postal');
        $position->metroCode = $this->getString($location, 'metro_code');
        $position->timezone = $this->getString($location, 'timezone') ?? $this->getString($location, 'time_zone');
        $position->latitude = $this->getString($location, 'latitude');
        $position->longitude = $this->getString($location, 'longitude');

        $position->isProxy = $this->getBool($location, 'isProxy');
        $position->isVpn = $this->getBool($location, 'isVpn');
        $position->isTor = $this->getBool($location, 'isTorExitNode');
        $position->isHosting = $this->getBool($location, 'isHostingProvider');
        $position->isp = $this->getString($location, 'isp');
        $position->org = $this->getString($location, 'organization');
        $position->asn = $this->getString($location, 'asn');
        $position->connectionType = $this->getString($location, 'connectionType') ?? 'Unknown';

        return $position;
    }

    /**
     * {@inheritdoc}
     */
    protected function process(string $ip)
    {
        try {
            $record = $this->fetchLocation($ip);
            $traits = $record->traits;

            return new Fluent([
                'country' => $record->country->name,
                'country_code' => $record->country->isoCode,
                'city' => $record->city->name,
                'regionCode' => $record->mostSpecificSubdivision->isoCode,
                'regionName' => $record->mostSpecificSubdivision->name,
                'postal' => $record->postal->code,
                'timezone' => $record->location->timeZone,
                'latitude' => (string) $record->location->latitude,
                'longitude' => (string) $record->location->longitude,
                'metro_code' => (string) $record->location->metroCode,

                // IP Intelligence
                'isProxy' => $traits->isAnonymousProxy ?? false,
                'isVpn' => $traits->isAnonymousVpn ?? false,
                'isTorExitNode' => $traits->isTorExitNode ?? false,
                'isHostingProvider' => $traits->isHostingProvider ?? false,
                'isp' => $traits->isp ?? null,
                'organization' => $traits->organization ?? null,
                'asn' => $traits->autonomousSystemNumber ?? null,
                'connectionType' => $traits->connectionType ?? null,
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Attempt to fetch the location model from Maxmind.
     *
     * @throws Exception
     */
    protected function fetchLocation(string $ip): \GeoIp2\Model\City
    {
        $maxmind = $this->isWebServiceEnabled()
            ? $this->newClient($this->getUserId(), $this->getLicenseKey(), $this->getOptions())
            : $this->newReader($this->getDatabasePath());

        return $maxmind->city($ip);
    }

    /**
     * Returns a new MaxMind web service client.
     */
    /**
     * Returns a new MaxMind web service client.
     *
     * @param  array<string, mixed>  $options
     */
    protected function newClient(int $userId, string $licenseKey, array $options = []): Client
    {
        /** @var array<int, string> $locales */
        $locales = ['en'];

        return new Client($userId, $licenseKey, $locales, $options);
    }

    /**
     * Returns a new MaxMind reader client with
     * the specified database file path.
     */
    protected function newReader(string $path): Reader
    {
        return new Reader($path);
    }

    /**
     * Returns true / false if the MaxMind web service is enabled.
     */
    protected function isWebServiceEnabled(): bool
    {
        return (bool) config('location.maxmind.web.enabled', false);
    }

    /**
     * Returns the configured MaxMind web user ID.
     */
    protected function getUserId(): int
    {
        $config = config('location.maxmind.web.user_id');

        return is_numeric($config) ? (int) $config : 0;
    }

    /**
     * Returns the configured MaxMind web license key.
     */
    protected function getLicenseKey(): string
    {
        $config = config('location.maxmind.web.license_key');

        return is_string($config) ? $config : '';
    }

    /**
     * Returns the configured MaxMind web option array.
     *
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        $config = config('location.maxmind.web.options', []);

        return is_array($config) ? $config : [];
    }

    /**
     * Returns the MaxMind database file path.
     */
    protected function getDatabasePath(): string
    {
        $config = config('location.maxmind.local.path');

        return is_string($config) ? $config : database_path('maxmind/GeoLite2-City.mmdb');
    }
}

