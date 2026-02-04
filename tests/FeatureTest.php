<?php

namespace Ermradulsharma\OmniLocate\Tests;

use Mockery as m;
use Ermradulsharma\OmniLocate\Position;
use Ermradulsharma\OmniLocate\Drivers\HttpHeader;
use Ermradulsharma\OmniLocate\Rules\LocationRule;
use Illuminate\Support\Fluent;

class FeatureTest extends TestCase
{
    public function test_position_flag()
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

    public function test_http_header_driver()
    {
        $driver = new HttpHeader();

        config(['location.position' => Position::class]);

        $request = \Illuminate\Http\Request::create('/', 'GET');
        $request->headers->set('cf-ipcountry', 'IN');
        $request->headers->set('x-region-code', 'MH');
        $request->headers->set('x-city-name', 'Mumbai');

        $this->app->instance('request', $request);

        $position = $driver->get('1.1.1.1');

        $this->assertEquals('IN', $position->countryCode);
        $this->assertEquals('MH', $position->regionCode);
        $this->assertEquals('Mumbai', $position->cityName);
    }

    public function test_location_rule()
    {
        $rule = new LocationRule('India');

        $driver = m::mock(\Ermradulsharma\OmniLocate\Drivers\Driver::class);

        $india = new Position();
        $india->countryName = 'India';
        $india->countryCode = 'IN';

        $us = new Position();
        $us->countryName = 'United States';
        $us->countryCode = 'US';

        $driver->shouldReceive('get')->with('1.1.1.1')->andReturn($india);
        $driver->shouldReceive('get')->with('8.8.8.8')->andReturn($us);

        \Ermradulsharma\OmniLocate\Facades\Location::setDriver($driver);

        $this->assertTrue($rule->passes('ip', '1.1.1.1'));
        $this->assertFalse($rule->passes('ip', '8.8.8.8'));

        $ruleCode = new LocationRule('US');
        $this->assertTrue($ruleCode->passes('ip', '8.8.8.8'));
    }

    public function test_distance_calculation()
    {
        $mumbai = new Position();
        $mumbai->latitude = 19.0760;
        $mumbai->longitude = 72.8777;

        $delhi = new Position();
        $delhi->latitude = 28.6139;
        $delhi->longitude = 77.2090;

        $distance = $mumbai->distanceTo($delhi);
        $this->assertGreaterThan(1100, $distance);
        $this->assertLessThan(1200, $distance);
    }

    public function test_currency_mapping()
    {
        $location = app('location');

        $position = new Position();
        $position->countryCode = 'IN';

        $driver = m::mock(\Ermradulsharma\OmniLocate\Drivers\Driver::class);
        $driver->shouldReceive('get')->andReturn($position);
        $location->setDriver($driver);

        $result = $location->get('1.1.1.1');
        $this->assertEquals('INR', $result->currencyCode);
    }

    public function test_bot_detection()
    {
        config(['location.bots.enabled' => true]);
        config(['location.bots.list' => ['googlebot']]);

        $request = \Illuminate\Http\Request::create('/', 'GET');
        $request->headers->set('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
        $this->app->instance('request', $request);

        $location = app('location');
        $this->assertFalse($location->get('1.1.1.1'));
    }
}
