<?php

namespace Skywalker\Location\Tests;

use Mockery as m;
use Illuminate\Support\Fluent;
use Skywalker\Location\Drivers\IpData;
use Skywalker\Location\DataTransferObjects\Position;
use Skywalker\Location\Drivers\IpApi;
use Skywalker\Location\Drivers\IpInfo;
use Skywalker\Location\Drivers\Driver;
use Skywalker\Location\Drivers\MaxMind;
use Skywalker\Location\Drivers\GeoPlugin;
use Skywalker\Location\Facades\Location;
use Skywalker\Location\Exceptions\DriverDoesNotExistException;

class LocationTest extends TestCase
{
    public function test_driver_process(): void
    {
        /** @var Driver&m\MockInterface $driver */
        $driver = m::mock(Driver::class);

        Location::setDriver($driver);

        $position = new Position();
        $position->cityName = 'foo';

        $driver->makePartial();
        $driver->shouldAllowMockingProtectedMethods();
        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('process');
        $expectation->once()->andReturn(new Fluent(['foo']));

        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('hydrate');
        $expectation->once()->andReturn($position);

        $this->assertEquals($position, Location::get());
    }

    public function test_driver_fallback(): void
    {
        /** @var Driver&m\MockInterface $fallback */
        $fallback = m::mock(Driver::class)
            ->shouldAllowMockingProtectedMethods();

        /** @var m\Expectation $expectation */
        $expectation = $fallback->shouldReceive('get');
        $expectation->once()->andReturn(new Position());

        /** @var IpApi&m\MockInterface $driver */
        $driver = m::mock(IpApi::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('process');
        $expectation->once()->andReturn(false);

        $driver->fallback($fallback);

        Location::setDriver($driver);

        $this->assertInstanceOf(Position::class, Location::get());
    }

    public function test_driver_does_not_exist(): void
    {
        config(['location.driver' => 'Test']);

        $this->expectException(DriverDoesNotExistException::class);

        Location::get();
    }

    public function test_ip_api(): void
    {
        /** @var IpApi&m\MockInterface $driver */
        $driver = m::mock(IpApi::class)->makePartial();

        $attributes = [
            'country' => 'United States',
            'countryCode' => 'US',
            'region' => 'CA',
            'regionName' => 'California',
            'city' => 'Long Beach',
            'zip' => '55555',
            'lat' => '50',
            'lon' => '50',
            'timezone' => 'America/Toronto',
        ];

        $driver->shouldAllowMockingProtectedMethods();
        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('process');
        $expectation->once()->andReturn(new Fluent($attributes));

        Location::setDriver($driver);

        $position = Location::get();

        $this->assertInstanceOf(Position::class, $position);
        $this->assertEquals([
            'ip' => '66.102.0.0',
            'countryName' => 'United States',
            'countryCode' => 'US',
            'regionCode' => 'CA',
            'regionName' => 'California',
            'cityName' => 'Long Beach',
            'zipCode' => '55555',
            'isoCode' => null,
            'postalCode' => null,
            'latitude' => '50',
            'longitude' => '50',
            'metroCode' => null,
            'areaCode' => 'CA',
            'timezone' => 'America/Toronto',
            'currencyCode' => 'USD',
            'driver' => get_class($driver),
            'isProxy' => null,
            'isVpn' => null,
            'isTor' => null,
            'isHosting' => null,
            'geoRiskScore' => null,
            'isp' => null,
            'asn' => null,
            'org' => null,
            'connectionType' => 'Unknown',
            'language' => 'en',
        ], $position->toArray());
    }

    public function test_geo_plugin(): void
    {
        /** @var GeoPlugin&m\MockInterface $driver */
        $driver = m::mock(GeoPlugin::class)->makePartial();

        $attributes = [
            'geoplugin_countryCode' => 'US',
            'geoplugin_countryName' => 'United States',
            'geoplugin_regionName' => 'California',
            'geoplugin_regionCode' => 'CA',
            'geoplugin_city' => 'Long Beach',
            'geoplugin_latitude' => '50',
            'geoplugin_longitude' => '50',
            'geoplugin_areaCode' => '555',
            'geoplugin_timezone' => 'America/Toronto',
        ];

        $driver->shouldAllowMockingProtectedMethods();
        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('process');
        $expectation->once()->andReturn(new Fluent($attributes));

        Location::setDriver($driver);

        $position = Location::get();

        $this->assertInstanceOf(Position::class, $position);
        $this->assertEquals([
            'ip' => '66.102.0.0',
            'countryName' => 'United States',
            'countryCode' => 'US',
            'regionCode' => 'CA',
            'regionName' => 'California',
            'cityName' => 'Long Beach',
            'zipCode' => null,
            'isoCode' => null,
            'postalCode' => null,
            'latitude' => '50',
            'longitude' => '50',
            'metroCode' => null,
            'areaCode' => '555',
            'timezone' => 'America/Toronto',
            'currencyCode' => 'USD',
            'driver' => get_class($driver),
            'isProxy' => null,
            'isVpn' => null,
            'isTor' => null,
            'isHosting' => null,
            'geoRiskScore' => null,
            'isp' => null,
            'asn' => null,
            'org' => null,
            'connectionType' => 'Unknown',
            'language' => 'en',
        ], $position->toArray());
    }

    public function test_ip_info(): void
    {
        /** @var IpInfo&m\MockInterface $driver */
        $driver = m::mock(IpInfo::class)->makePartial();

        $attributes = [
            'country' => 'US',
            'region' => 'California',
            'city' => 'Long Beach',
            'postal' => '55555',
            'loc' => '50,50',
            'timezone' => 'America/Toronto',
        ];

        $driver->shouldAllowMockingProtectedMethods();
        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('process');
        $expectation->once()->andReturn(new Fluent($attributes));

        Location::setDriver($driver);

        $position = Location::get();

        $this->assertInstanceOf(Position::class, $position);
        $this->assertEquals([
            'ip' => '66.102.0.0',
            'countryName' => null,
            'countryCode' => 'US',
            'regionCode' => null,
            'regionName' => 'California',
            'cityName' => 'Long Beach',
            'zipCode' => '55555',
            'isoCode' => null,
            'postalCode' => null,
            'latitude' => '50',
            'longitude' => '50',
            'metroCode' => null,
            'areaCode' => null,
            'timezone' => 'America/Toronto',
            'currencyCode' => 'USD',
            'driver' => get_class($driver),
            'isProxy' => null,
            'isVpn' => null,
            'isTor' => null,
            'isHosting' => null,
            'geoRiskScore' => null,
            'isp' => null,
            'asn' => null,
            'org' => null,
            'connectionType' => 'Unknown',
            'language' => 'en',
        ], $position->toArray());
    }

    public function test_max_mind(): void
    {
        /** @var MaxMind&m\MockInterface $driver */
        $driver = m::mock(MaxMind::class);

        $attributes = [
            'country' => 'United States',
            'country_code' => 'US',
            'city' => 'Long Beach',
            'postal' => '55555',
            'metro_code' => '5555',
            'latitude' => '50',
            'longitude' => '50',
            'time_zone' => 'America/Toronto'
        ];

        $driver->makePartial();
        $driver->shouldAllowMockingProtectedMethods();
        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('process');
        $expectation->once()->andReturn(new Fluent($attributes));

        Location::setDriver($driver);

        $position = Location::get();

        $this->assertInstanceOf(Position::class, $position);
        $this->assertEquals([
            'ip' => '66.102.0.0',
            'countryName' => 'United States',
            'countryCode' => 'US',
            'regionCode' => null,
            'regionName' => null,
            'cityName' => 'Long Beach',
            'zipCode' => null,
            'isoCode' => 'US',
            'postalCode' => '55555',
            'latitude' => '50',
            'longitude' => '50',
            'metroCode' => '5555',
            'areaCode' => null,
            'timezone' => 'America/Toronto',
            'currencyCode' => 'USD',
            'driver' => get_class($driver),
            'isProxy' => null,
            'isVpn' => null,
            'isTor' => null,
            'isHosting' => null,
            'geoRiskScore' => null,
            'isp' => null,
            'asn' => null,
            'org' => null,
            'connectionType' => 'Unknown',
            'language' => 'en',
        ], $position->toArray());
    }

    public function test_ip_data(): void
    {
        /** @var IpData&m\MockInterface $driver */
        $driver = m::mock(IpData::class);

        $attributes = [
            'country_name' => 'United States',
            'country_code' => 'US',
            'region_code' => 'CA',
            'region' => 'California',
            'city' => 'Long Beach',
            'postal' => '55555',
            'latitude' => '50',
            'longitude' => '50',
            'time_zone' => ['name' => 'America/Toronto'],
        ];

        $driver->makePartial();
        $driver->shouldAllowMockingProtectedMethods();
        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('process');
        $expectation->once()->andReturn(new Fluent($attributes));

        Location::setDriver($driver);

        $position = Location::get();

        $this->assertInstanceOf(Position::class, $position);
        $this->assertEquals([
            'ip' => '66.102.0.0',
            'countryName' => 'United States',
            'countryCode' => 'US',
            'regionCode' => 'CA',
            'regionName' => 'California',
            'cityName' => 'Long Beach',
            'zipCode' => '55555',
            'isoCode' => null,
            'postalCode' => '55555',
            'latitude' => '50',
            'longitude' => '50',
            'metroCode' => null,
            'areaCode' => null,
            'timezone' => 'America/Toronto',
            'currencyCode' => 'USD',
            'driver' => get_class($driver),
            'isProxy' => null,
            'isVpn' => null,
            'isTor' => null,
            'isHosting' => null,
            'geoRiskScore' => null,
            'isp' => null,
            'asn' => null,
            'org' => null,
            'connectionType' => 'Unknown',
            'language' => 'en',
        ], $position->toArray());
    }

    public function test_position_is_empty(): void
    {
        $position = new Position();
        $position->ip = '192.168.1.1';
        $position->driver = 'foo';

        $this->assertTrue($position->isEmpty());

        $position = new Position();
        $position->isoCode = 'foo';
        $this->assertFalse($position->isEmpty());
    }
}
