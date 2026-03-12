<?php

declare(strict_types=1);

namespace Skywalker\Location\Tests;

use Skywalker\Location\Facades\Location;
use Skywalker\Location\DataTransferObjects\Position;
use Skywalker\Location\Drivers\IpApi;
use Mockery as m;

class ServiceTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @test */
    public function test_it_can_get_current_position_using_default_driver(): void
    {
        /** @var IpApi&m\MockInterface $driver */
        $driver = m::mock(IpApi::class)->makePartial();
        $driver->shouldAllowMockingProtectedMethods();
        
        $mockPosition = new Position(['ip' => '1.2.3.4', 'countryCode' => 'US']);
        
        /** @var m\Expectation $expectation */
        $expectation = $driver->shouldReceive('get');
        $expectation->andReturn($mockPosition);
        
        Location::setDriver($driver);
        
        $position = Location::get('1.2.3.4');
        
        $this->assertInstanceOf(Position::class, $position);
        $this->assertEquals('US', $position->countryCode);
        $this->assertEquals('1.2.3.4', $position->ip);
    }
}
