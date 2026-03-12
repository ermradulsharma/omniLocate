<?php

namespace Skywalker\Location\Tests;

use Mockery as m;
use Skywalker\Location\DataTransferObjects\Position;
use Skywalker\Location\Drivers\HttpHeader;
use Skywalker\Location\Rules\LocationRule;
use Illuminate\Support\Fluent;

class FeatureTest extends TestCase
{
    public function test_position_flag(): void
    {
        $position = new Position();
        $position->countryCode = 'IN';
        $this->assertEquals('🇮🇳', $position->flag());

        $position->countryCode = 'US';
        $this->assertEquals('🇺🇸', $position->flag());

        $position->countryCode = 'ca'; // lowercase
        $this->assertEquals('🇨🇦', $position->flag());

        $position->countryCode = null;
        $this->assertNull($position->flag());
    }

    public function test_http_header_driver(): void
    {
        $driver = new HttpHeader();

        config(['location.position' => Position::class]);

        $request = \Illuminate\Http\Request::create('/', 'GET');
        $request->headers->set('cf-ipcountry', 'IN');
        $request->headers->set('x-region-code', 'MH');
        $request->headers->set('x-city-name', 'Mumbai');

        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;
        $app->instance('request', $request);

        /** @var Position $position */
        $position = $driver->get('1.1.1.1');

        $this->assertEquals('IN', $position->countryCode);
        $this->assertEquals('MH', $position->regionCode);
        $this->assertEquals('Mumbai', $position->cityName);
    }

    public function test_location_rule(): void
    {
        $rule = new LocationRule('India');

        /** @var \Skywalker\Location\Drivers\Driver&m\MockInterface $driver */
        $driver = m::mock(\Skywalker\Location\Drivers\Driver::class);

        $india = new Position();
        $india->countryName = 'India';
        $india->countryCode = 'IN';

        $us = new Position();
        $us->countryName = 'United States';
        $us->countryCode = 'US';

        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('get');
        $expectation->with('1.1.1.1')->andReturn($india);

        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('get');
        $expectation->with('8.8.8.8')->andReturn($us);

        \Skywalker\Location\Facades\Location::setDriver($driver);

        $this->assertTrue($rule->passes('ip', '1.1.1.1'));
        $this->assertFalse($rule->passes('ip', '8.8.8.8'));

        $ruleCode = new LocationRule('US');
        $this->assertTrue($ruleCode->passes('ip', '8.8.8.8'));
    }

    public function test_distance_calculation(): void
    {
        $mumbai = new Position();
        $mumbai->latitude = (string) 19.0760;
        $mumbai->longitude = (string) 72.8777;

        $delhi = new Position();
        $delhi->latitude = (string) 28.6139;
        $delhi->longitude = (string) 77.2090;

        $distance = $mumbai->distanceTo($delhi);
        $this->assertGreaterThan(1100, $distance);
        $this->assertLessThan(1200, $distance);
    }

    public function test_currency_mapping(): void
    {
        /** @var \Skywalker\Location\Location $location */
        $location = app('location');

        $position = new Position();
        $position->countryCode = 'IN';

        /** @var \Skywalker\Location\Drivers\Driver&m\MockInterface $driver */
        $driver = m::mock(\Skywalker\Location\Drivers\Driver::class);
        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('get');
        $expectation->andReturn($position);
        $location->setDriver($driver);

        /** @var Position $result */
        $result = $location->get('1.1.1.1');
        $this->assertEquals('INR', $result->currencyCode);
    }

    public function test_bot_detection(): void
    {
        config(['location.bots.enabled' => true]);
        config(['location.bots.list' => ['googlebot']]);

        $request = \Illuminate\Http\Request::create('/', 'GET');
        $request->headers->set('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');

        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;
        $app->instance('request', $request);

        /** @var \Skywalker\Location\Location $location */
        $location = app('location');
        $this->assertFalse($location->get('1.1.1.1'));
    }
}

