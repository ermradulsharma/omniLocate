<?php

namespace Skywalker\Location\Tests\Drivers;

use Mockery as m;
use Illuminate\Support\Fluent;
use Skywalker\Location\DataTransferObjects\Position;
use Skywalker\Location\Drivers\GeoPlugin;
use Skywalker\Location\Tests\TestCase;

class GeoPluginTest extends TestCase
{
    public function test_process_with_json_response(): void
    {
        /** @var GeoPlugin&m\MockInterface $driver */
        $driver = m::mock(GeoPlugin::class.'[url,getUrlContent]')->shouldAllowMockingProtectedMethods();

        $jsonResponse = json_encode([
            'geoplugin_countryCode' => 'US',
            'geoplugin_countryName' => 'United States',
            'geoplugin_regionName' => 'California',
            'geoplugin_regionCode' => 'CA',
            'geoplugin_city' => 'Long Beach',
            'geoplugin_latitude' => '50',
            'geoplugin_longitude' => '50',
            'geoplugin_areaCode' => '555',
            'geoplugin_timezone' => 'America/Toronto',
        ]);

        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('url');
        $expectation->once()->andReturn('http://www.geoplugin.net/json.gp?ip=66.102.0.0');

        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('getUrlContent');
        $expectation->once()->andReturn($jsonResponse);

        $result = $driver->get('66.102.0.0');

        $this->assertInstanceOf(Position::class, $result);
        $this->assertEquals('US', $result->countryCode);
        $this->assertEquals('Long Beach', $result->cityName);
    }

    public function test_process_with_invalid_json_returns_false(): void
    {
        /** @var GeoPlugin&m\MockInterface $driver */
        $driver = m::mock(GeoPlugin::class.'[getUrlContent]')->shouldAllowMockingProtectedMethods();

        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('getUrlContent');
        $expectation->once()->andReturn('invalid json');

        $result = $driver->get('66.102.0.0');

        $this->assertFalse($result);
    }
}
