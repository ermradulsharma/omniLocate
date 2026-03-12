<?php

namespace Skywalker\Location\Tests;

use Mockery as m;
use Skywalker\Location\DataTransferObjects\Position;
use Skywalker\Location\Facades\Location;
use Skywalker\Location\Http\Middleware\TorBlocker;
use Skywalker\Location\Http\Middleware\BotVerifier;
use Illuminate\Http\Request;
use Skywalker\Location\Drivers\Driver;
use Skywalker\Location\Drivers\HttpHeader;
use Skywalker\Location\Actions\VerifyBot;

class SecurityTest extends TestCase
{
    /**
     * Test SSRF protection in the base Driver class.
     */
    public function test_driver_ssrf_protection(): void
    {
        /** @var Driver&m\MockInterface $driver */
        $driver = m::mock(HttpHeader::class)->makePartial();
        
        $this->assertFalse($driver->get('invalid-ip'));
        $this->assertFalse($driver->get('256.256.256.256'));
    }

    /**
     * Test Real vs Fake Bot Verification.
     */
    public function test_real_vs_fake_bot_verification(): void
    {
        $agent = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
        
        // 1. Fake Bot
        $fakeIp = '1.2.3.4';
        $mock = m::mock(VerifyBot::class);
        $this->app->instance(VerifyBot::class, $mock);
        $mock->shouldReceive('execute')->with($fakeIp, $agent)->andReturn(false);

        $request = Request::create('/', 'GET');
        $request->headers->set('User-Agent', $agent);
        $request->server->set('REMOTE_ADDR', $fakeIp);
        $this->app->instance('request', $request);

        $this->assertFalse(Location::isVerifiedBot());

        // 2. Real Bot
        $realIp = '66.249.66.1';
        $mock->shouldReceive('execute')->with($realIp, $agent)->andReturn(true);

        $request = Request::create('/', 'GET');
        $request->headers->set('User-Agent', $agent);
        $request->server->set('REMOTE_ADDR', $realIp);
        $this->app->instance('request', $request);

        $this->assertTrue(Location::isVerifiedBot());
    }

    /**
     * Test TorBlocker Middleware.
     */
    public function test_tor_blocker_middleware(): void
    {
        config(['location.tor.block' => true]);

        $mock = m::mock(\Skywalker\Location\Location::class)->makePartial();
        $this->app->instance('location', $mock);

        $middleware = new TorBlocker();
        $request = Request::create('/safe', 'GET');

        // 1. Normal IP
        $position = new Position();
        $position->isTor = false;
        $mock->shouldReceive('get')->once()->andReturn($position);

        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        $this->assertEquals(200, $response->getStatusCode());

        // 2. Tor IP
        $torPosition = new Position();
        $torPosition->isTor = true;
        $mock->shouldReceive('get')->once()->andReturn($torPosition);

        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test BotVerifier Middleware blocks spoofed bots.
     */
    public function test_bot_verifier_blocks_spoofed_bots(): void
    {
        config(['location.bots.list' => ['googlebot']]);
        config(['location.bots.trusted_domains' => ['googlebot' => ['.googlebot.com']]]);

        $mock = m::mock(\Skywalker\Location\Location::class)->makePartial();
        $this->app->instance('location', $mock);

        $middleware = new BotVerifier();
        $agent = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
        
        // 1. Spoofed Bot
        $request = Request::create('/', 'GET');
        $request->headers->set('User-Agent', $agent);
        $this->app->instance('request', $request);

        $mock->shouldReceive('isVerifiedBot')->once()->andReturn(false);

        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals(403, $response->getStatusCode());
        
        // 2. Real Bot
        $mock->shouldReceive('isVerifiedBot')->once()->andReturn(true);
        
        $response = $middleware->handle($request, function () {
            return response('OK');
        });
        
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test HybridLocationController IP spoofing protection.
     */
    public function test_hybrid_controller_ip_spoofing_protection(): void
    {
        config(['app.debug' => false]);
        
        $request = Request::create('/omni-locate/verify', 'POST', [
            'latitude' => 19.0,
            'longitude' => 72.0,
            'test_ip' => '8.8.8.8'
        ]);
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $this->app->instance('request', $request);
        
        $verifier = m::mock(\Skywalker\Location\Services\HybridVerifier::class);
        $verifier->shouldReceive('verify')
            ->with('1.1.1.1', 19.0, 72.0)
            ->once()
            ->andReturn([]);

        $controller = new \Skywalker\Location\Http\Controllers\HybridLocationController();
        $controller->verify($request, $verifier);
        $this->assertTrue(true);
    }

    /**
     * Test GeoRestriction Middleware.
     */
    public function test_geo_restriction_middleware(): void
    {
        $middleware = new \Skywalker\Location\Http\Middleware\GeoRestriction();
        $request = Request::create('/', 'GET');
        $mock = m::mock(\Skywalker\Location\Location::class);
        $this->app->instance('location', $mock);

        // 1. Blocked Country
        config(['location.restriction.blocked_countries' => ['RU']]);
        $pos = new Position();
        $pos->countryCode = 'RU';
        $mock->shouldReceive('get')->andReturn($pos);

        $response = $middleware->handle($request, function() { return response('OK'); });
        $this->assertEquals(403, $response->getStatusCode());

        // 2. Allowed Country
        config(['location.restriction.blocked_countries' => []]);
        config(['location.restriction.allowed_countries' => ['US']]);
        $pos->countryCode = 'US';
        $mock->shouldReceive('get')->andReturn($pos);

        $response = $middleware->handle($request, function() { return response('OK'); });
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test GeoRiskGuard Middleware.
     */
    public function test_geo_risk_guard_middleware(): void
    {
        $middleware = new \Skywalker\Location\Http\Middleware\GeoRiskGuard();
        $request = Request::create('/', 'GET');
        $mock = m::mock(\Skywalker\Location\Location::class);
        $this->app->instance('location', $mock);

        config(['location.risk.threshold' => 80]);

        // 1. High Risk
        $pos = new Position();
        $pos->geoRiskScore = 90;
        $mock->shouldReceive('get')->andReturn($pos);

        $response = $middleware->handle($request, function() { return response('OK'); });
        $this->assertEquals(403, $response->getStatusCode());

        // 2. Low Risk
        $pos->geoRiskScore = 20;
        $mock->shouldReceive('get')->andReturn($pos);

        $response = $middleware->handle($request, function() { return response('OK'); });
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_adaptive_rate_limit_logic(): void
    {
        $middleware = $this->app->make(\Skywalker\Location\Http\Middleware\AdaptiveRateLimit::class);
        
        $request = Request::create('/', 'GET');
        $mockLoc = m::mock(\Skywalker\Location\Location::class);
        $this->app->instance('location', $mockLoc);
        
        // 1. High Risk IP -> 5 attempts
        $pos = new Position();
        $pos->geoRiskScore = 80;
        
        $mockLoc->shouldReceive('get')->andReturn($pos);
        $mockLoc->shouldReceive('isVerifiedBot')->andReturn(false);
        
        // We verify the logic doesn't crash and passes through to parent handle.
        // It's hard to verify parent::handle's internal state without deeper mocking,
        // but this confirms the 'handle' method exists and is callable.
        $response = $middleware->handle($request, function() { return response('OK'); }, 60, 1);
        $this->assertEquals(200, $response->getStatusCode());
    }
}



