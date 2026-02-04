<?php

namespace Ermradulsharma\OmniLocate\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Ermradulsharma\OmniLocate\LocationServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [LocationServiceProvider::class];
    }
}
