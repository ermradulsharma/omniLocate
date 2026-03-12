<?php

namespace Skywalker\Location\Tests;

use Mockery as m;
use Skywalker\Location\DataTransferObjects\Position;
use Skywalker\Location\Facades\Location;
use Skywalker\Location\Services\HybridVerifier;

class HybridLocationTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app['config'];

        $config->set('app.key', 'base64:6Cu/ozj4gPtIjmXjr8EdVnGFNsdRqZfHfVjQkmTlg4Y=');
        $config->set('app.debug', true);
    }

    public function test_hybrid_verification_spoofed(): void
    {
        // Mock Location::get('1.2.3.4') to return New York
        $ny = new Position();
        $ny->cityName = 'New York';
        $ny->latitude = (string) 40.7128;
        $ny->longitude = (string) -74.0060;
        $ny->countryCode = 'US';

        Location::shouldReceive('get')->with('1.2.3.4')->andReturn($ny);

        $verifier = new HybridVerifier();

        // Provided GPS is London (Spoofed VPN case)
        $londonLat = 51.5074;
        $londonLon = -0.1278;

        $result = $verifier->verify('1.2.3.4', $londonLat, $londonLon);

        $this->assertTrue($result['is_spoofed']);
        $this->assertFalse($result['verified']);
        $this->assertGreaterThan(5000, $result['distance_km']);
    }

    public function test_hybrid_verification_valid(): void
    {
        // Mock Location::get('1.2.3.4') to return New York
        $ny = new Position();
        $ny->cityName = 'New York';
        $ny->latitude = (string) 40.7128;
        $ny->longitude = (string) -74.0060;
        $ny->countryCode = 'US';

        Location::shouldReceive('get')->with('1.2.3.4')->andReturn($ny);

        $verifier = new HybridVerifier();

        // Provided GPS is nearby (Brooklyn)
        $brooklynLat = 40.6782;
        $brooklynLon = -73.9442;

        $result = $verifier->verify('1.2.3.4', $brooklynLat, $brooklynLon);

        $this->assertFalse($result['is_spoofed']);
        $this->assertTrue($result['verified']);
        $this->assertLessThan(20, $result['distance_km']);
    }

    public function test_verify_route(): void
    {
        $ny = new Position();
        $ny->latitude = (string) 40.7128;
        $ny->longitude = (string) -74.0060;
        $ny->countryCode = 'US';

        Location::shouldReceive('get')->with('1.2.3.4')->andReturn($ny);

        $response = $this->postJson(route('omni-locate.verify'), [
            'test_ip' => '1.2.3.4',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'is_spoofed' => false,
                    'verified' => true
                ]
            ]);
    }
}
