<?php

namespace Ermradulsharma\OmniLocate;

use Illuminate\Contracts\Config\Repository;
use Ermradulsharma\OmniLocate\Drivers\Driver;
use Ermradulsharma\OmniLocate\Exceptions\DriverDoesNotExistException;

class Location
{
    /**
     * The current driver.
     *
     * @var Driver
     */
    protected $driver;

    /**
     * The application configuration.
     *
     * @var Repository
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param Repository $config
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
     *
     * @param Driver $driver
     */
    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Set the default location driver to use.
     *
     * @throws DriverDoesNotExistException
     */
    public function setDefaultDriver()
    {
        $driver = $this->getDriver($this->getDefaultDriver());

        foreach ($this->getDriverFallbacks() as $fallback) {
            $driver->fallback($this->getDriver($fallback));
        }

        $this->setDriver($driver);
    }

    /**
     * Add a fallback driver.
     *
     * @param Driver $driver
     * @return void
     */
    public function fallback(Driver $driver)
    {
        $this->driver->fallback($driver);
    }

    /**
     * Attempt to retrieve the location of the user.
     *
     * @param string|null $ip
     *
     * @return \Ermradulsharma\OmniLocate\Position|bool
     */
    public function get($ip = null)
    {
        if ($this->isBot()) {
            return false;
        }

        $ip = $ip ?: $this->getClientIP();

        $position = $this->cacheEnabled()
            ? cache()->remember($this->getCacheKey($ip), $this->getCacheDuration(), function () use ($ip) {
                return $this->driver->get($ip);
            })
            : $this->driver->get($ip);

        if ($position) {
            $this->hydrateAdvancedFeatures($position);

            event(new Events\LocationDetected($position));

            return $position;
        }

        return false;
    }

    /**
     * Hydrate advanced features on the position.
     *
     * @param Position $position
     * @return void
     */
    protected function hydrateAdvancedFeatures(Position $position)
    {
        $position->currencyCode = $this->getCurrencyCode($position->countryCode);
    }

    /**
     * Get the currency code for the given country code.
     *
     * @param string|null $countryCode
     * @return string|null
     */
    protected function getCurrencyCode($countryCode)
    {
        $currencies = [
            'US' => 'USD',
            'IN' => 'INR',
            'GB' => 'GBP',
            'CA' => 'CAD',
            'AU' => 'AUD',
            'DE' => 'EUR',
            'FR' => 'EUR',
            'IT' => 'EUR',
            'ES' => 'EUR',
            'JP' => 'JPY',
        ];

        return $currencies[strtoupper($countryCode)] ?? null;
    }

    /**
     * Determine if the current user is a bot.
     *
     * @return bool
     */
    protected function isBot()
    {
        if (! $this->config->get('location.bots.enabled', false)) {
            return false;
        }

        $agent = request()->userAgent();

        foreach ($this->config->get('location.bots.list', []) as $bot) {
            if (str_contains(strtolower($agent), strtolower($bot))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if caching is enabled.
     *
     * @return bool
     */
    protected function cacheEnabled()
    {
        return $this->config->get('location.cache.enabled', false);
    }

    /**
     * Get the cache key for the given IP address.
     *
     * @param string $ip
     *
     * @return string
     */
    protected function getCacheKey($ip)
    {
        return "location.$ip";
    }

    /**
     * Get the cache duration.
     *
     * @return int
     */
    protected function getCacheDuration()
    {
        return $this->config->get('location.cache.duration', 86400);
    }

    /**
     * Get the client IP address.
     *
     * @return string
     */
    protected function getClientIP()
    {
        return $this->localHostTesting()
            ? $this->getLocalHostTestingIp()
            : request()->ip();
    }

    /**
     * Determine if testing is enabled.
     *
     * @return bool
     */
    protected function localHostTesting()
    {
        return $this->config->get('location.testing.enabled', true);
    }

    /**
     * Get the testing IP address.
     *
     * @return string
     */
    protected function getLocalHostTestingIp()
    {
        return $this->config->get('location.testing.ip', '66.102.0.0');
    }

    /**
     * Get the fallback location drivers to use.
     *
     * @return array
     */
    protected function getDriverFallbacks()
    {
        return $this->config->get('location.fallbacks', []);
    }

    /**
     * Get the default location driver.
     *
     * @return string
     */
    protected function getDefaultDriver()
    {
        return $this->config->get('location.driver');
    }

    /**
     * Attempt to create the location driver.
     *
     * @param string $driver
     *
     * @return Driver
     *
     * @throws DriverDoesNotExistException
     */
    protected function getDriver($driver)
    {
        if (! class_exists($driver)) {
            throw DriverDoesNotExistException::forDriver($driver);
        }

        return app()->make($driver);
    }
}
