<?php

declare(strict_types=1);

namespace Skywalker\Location\Drivers;

use Illuminate\Support\Fluent;
use Skywalker\Location\DataTransferObjects\Position;

abstract class Driver
{
    public const CURL_MAX_TIME = 2;

    public const CURL_CONNECT_TIMEOUT = 2;

    /**
     * The fallback driver.
     */
    protected ?Driver $fallback = null;

    /**
     * Append a fallback driver to the end of the chain.
     */
    public function fallback(Driver $handler): void
    {
        if (is_null($this->fallback)) {
            $this->fallback = $handler;
        } else {
            $this->fallback->fallback($handler);
        }
    }

    /**
     * Handle the driver request.
     *
     * @return Position|bool
     */
    public function get(string $ip)
    {
        // Security check: Ensure the IP is a valid public IP to prevent SSRF and internal lookups
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            // Allow if it's explicitly for local testing and allowed in config
            if (! config('location.testing.enabled', false)) {
                return false;
            }
        }

        $data = $this->process($ip);

        $position = $this->getNewPosition();

        // Here we will ensure the locations data we received isn't empty.
        // Some IP location providers will return empty JSON. We want
        // to avoid this so we can go to a fallback driver.
        if ($data instanceof Fluent && $this->fluentDataIsNotEmpty($data)) {
            $position = $this->hydrate($position, $data);

            $position->ip = $ip;
            $position->driver = get_class($this);
        }

        if (! $position->isEmpty()) {
            return $position;
        }

        return $this->fallback ? $this->fallback->get($ip) : false;
    }

    /**
     * Create a new position instance.
     */
    protected function getNewPosition(): Position
    {
        $config = config('location.position');
        /** @var class-string<Position> $position */
        $position = is_string($config) ? $config : \Skywalker\Location\DataTransferObjects\Position::class;

        return new $position;
    }

    /**
     * Determine if the given fluent data is not empty.
     *
     * @param  Fluent<string, mixed>  $data
     */
    protected function fluentDataIsNotEmpty(Fluent $data): bool
    {
        return ! empty(array_filter($data->getAttributes()));
    }

    /**
     * Get a string value from the fluent data.
     *
     * @param  Fluent<string, mixed>  $data
     */
    protected function getString(Fluent $data, string $key): ?string
    {
        $value = $data->get($key);

        return is_string($value) || is_numeric($value) ? (string) $value : null;
    }

    /**
     * Get a boolean value from the fluent data.
     *
     * @param  Fluent<string, mixed>  $data
     */
    protected function getBool(Fluent $data, string $key): ?bool
    {
        $value = $data->get($key);

        return is_null($value) ? null : (bool) $value;
    }

    /**
     * Get an array value from the fluent data.
     *
     * @param  Fluent<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getArray(Fluent $data, string $key): array
    {
        $value = $data->get($key);

        return is_array($value) ? $value : [];
    }

    /**
     * Get content from the given URL using cURL.
     *
     * @return string|bool
     */
    protected function getUrlContent(string $url)
    {
        $session = curl_init();

        curl_setopt($session, CURLOPT_URL, $url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($session, CURLOPT_TIMEOUT, static::CURL_MAX_TIME);
        curl_setopt($session, CURLOPT_CONNECTTIMEOUT, static::CURL_CONNECT_TIMEOUT);

        $content = curl_exec($session);

        curl_close($session);

        return $content;
    }

    /**
     * Get the URL to use for querying the current driver.
     */
    abstract protected function url(string $ip): string;

    /**
     * Hydrate the Position object with the given location data.
     *
     * @param  Position  $position
     * @param  Fluent<string, mixed>  $location
     * @return Position
     */
    abstract protected function hydrate(Position $position, Fluent $location): Position;

    /**
     * Attempt to fetch and process the location data from the driver.
     *
     * @param  string  $ip
     * @return Fluent<string, mixed>|bool
     */
    abstract protected function process(string $ip);
}

