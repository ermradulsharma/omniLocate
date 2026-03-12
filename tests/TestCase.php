<?php

namespace Skywalker\Location\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Skywalker\Location\LocationServiceProvider;

class TestCase extends BaseTestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [LocationServiceProvider::class];
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app['config'];

        $config->set('location.driver', \Skywalker\Location\Drivers\IpApi::class);
        $config->set('location.bots.enabled', true);
        $config->set('location.bots.list', ['Googlebot', 'Bingbot']);
        $config->set('location.cache.enabled', false);
        $config->set('app.debug', true);
        $config->set('location.testing.enabled', true);
        $config->set('location.testing.ip', '66.102.0.0');
    }
}

