<?php

declare(strict_types=1);

namespace Skywalker\Location;

use Illuminate\Contracts\Config\Repository;
use Skywalker\Location\DataTransferObjects\Position;
use Skywalker\Location\Actions\HydratePosition;
use Skywalker\Location\Actions\VerifyBot;
use Skywalker\Location\Drivers\Driver;
use Skywalker\Location\Events\LocationDetected;
use Skywalker\Location\Exceptions\DriverDoesNotExistException;
use Skywalker\Support\Foundation\Service;

class Location extends Service
{
    /**
     * The current driver.
     */
    protected Driver $driver;

    /**
     * The application configuration.
     */
    protected Repository $config;

    /**
     * Constructor.
     *
     * @throws DriverDoesNotExistException
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;

        $this->setDefaultDriver();
    }

    /**
     * Set the current driver to use.
     */
    public function setDriver(Driver $driver): void
    {
        $this->driver = $driver;
    }

    /**
     * Set the default location driver to use.
     *
     * @throws DriverDoesNotExistException
     */
    public function setDefaultDriver(): void
    {
        $driver = $this->getDriver($this->getDefaultDriver());

        foreach ($this->getDriverFallbacks() as $fallback) {
            $driver->fallback($this->getDriver($fallback));
        }

        $this->setDriver($driver);
    }

    /**
     * Add a fallback driver.
     */
    public function fallback(Driver $driver): void
    {
        $this->driver->fallback($driver);
    }

    /**
     * Attempt to retrieve the location of the user.
     *
     * @return Position|bool
     */
    public function get(?string $ip = null)
    {
        if ($this->isBot()) {
            return false;
        }

        $ip = $ip ?: $this->getClientIP();

        /** @var Position|bool $position */
        $position = $this->driver->get($ip);

        if ($position instanceof Position) {
            $result = HydratePosition::run($position);

            return $result instanceof Position ? $result : false;
        }

        return false;
    }

    /**
     * Determine if the current user is a bot.
     */
    public function isBot(): bool
    {
        if (! $this->config->get('location.bots.enabled', false)) {
            return false;
        }

        $agent = (string) request()->userAgent();

        $botsConfig = $this->config->get('location.bots.list');
        $bots = is_array($botsConfig) ? $botsConfig : [];

        foreach ($bots as $bot) {
            if (str_contains(strtolower($agent), strtolower((string) $bot))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the current user is a VERIFIED bot (e.g., real Googlebot).
     */
    public function isVerifiedBot(): bool
    {
        if (! $this->isBot()) {
            return false;
        }

        return (bool) VerifyBot::run(
            (string) request()->ip(),
            (string) (request()->userAgent() ?? '')
        );
    }

    /**
     * Determine if caching is enabled.
     */
    protected function cacheEnabled(): bool
    {
        return (bool) $this->config->get('location.cache.enabled', false);
    }

    /**
     * Get the cache key for the given IP address.
     */
    protected function getCacheKey(string $ip): string
    {
        return "location.{$ip}";
    }

    /**
     * Get the cache duration.
     */
    protected function getCacheDuration(): int
    {
        $config = $this->config->get('location.cache.duration', 86400);

        return is_numeric($config) ? (int) $config : 86400;
    }

    /**
     * Get the client IP address.
     */
    protected function getClientIP(): string
    {
        $ip = (string) request()->ip();

        if ($this->localHostTesting() && ($ip === '127.0.0.1' || $ip === '::1')) {
            return $this->getLocalHostTestingIp();
        }

        return $ip;
    }

    /**
     * Determine if testing is enabled.
     */
    protected function localHostTesting(): bool
    {
        return (bool) $this->config->get('location.testing.enabled', false);
    }

    /**
     * Get the testing IP address.
     */
    protected function getLocalHostTestingIp(): string
    {
        $config = $this->config->get('location.testing.ip', '66.102.0.0');

        return is_string($config) ? $config : '66.102.0.0';
    }

    /**
     * Get the fallback location drivers to use.
     *
     * @return array<int, string>
     */
    protected function getDriverFallbacks(): array
    {
        $fallbacks = $this->config->get('location.fallbacks');

        return is_array($fallbacks) ? $fallbacks : [];
    }

    /**
     * Get the default location driver.
     */
    protected function getDefaultDriver(): string
    {
        $config = $this->config->get('location.driver');

        return is_string($config) ? $config : '';
    }

    /**
     * Determine if the user is in the given country.
     */
    public function inCountry(string $code): bool
    {
        $position = $this->get();

        return $position instanceof Position && $position->countryCode === $code;
    }

    /**
     * Attempt to create the location driver.
     *
     * @throws DriverDoesNotExistException
     */
    protected function getDriver(string $driver): Driver
    {
        if (! class_exists($driver)) {
            throw DriverDoesNotExistException::forDriver($driver);
        }

        /** @var Driver $instance */
        $instance = app()->make($driver);

        return $instance;
    }
}

