<?php

declare(strict_types=1);

namespace Skywalker\Location\Tests;

use Skywalker\Location\DataTransferObjects\Position;

class DtoTest extends TestCase
{
    /** @test */
    public function test_it_can_be_instantiated_with_attributes(): void
    {
        $position = new Position([
            'countryName' => 'United States',
            'countryCode' => 'US',
            'cityName' => 'New York',
        ]);

        $this->assertEquals('United States', $position->countryName);
        $this->assertEquals('US', $position->countryCode);
        $this->assertEquals('New York', $position->cityName);
    }

    /** @test */
    public function test_it_handles_null_values_correctly(): void
    {
        $position = new Position([
            'countryName' => null,
            'cityName' => '',
        ]);

        $this->assertNull($position->countryName);
        // Depending on DTO implementation, empty string might stay empty string or become null
        $this->assertEquals('', $position->cityName);
    }

    /** @test */
    public function test_it_can_be_converted_to_array(): void
    {
        $attributes = [
            'ip' => '1.1.1.1',
            'countryName' => 'Australia',
            'countryCode' => 'AU',
        ];

        $position = new Position($attributes);
        $array = $position->toArray();

        $this->assertEquals('1.1.1.1', $array['ip']);
        $this->assertEquals('Australia', $array['countryName']);
        $this->assertEquals('AU', $array['countryCode']);
    }

    /** @test */
    public function test_it_can_detect_if_it_is_empty(): void
    {
        $position = new Position();
        $this->assertTrue($position->isEmpty());

        $position->countryCode = 'US';
        $this->assertFalse($position->isEmpty());
    }

    /** @test */
    public function test_it_serializes_to_json(): void
    {
        $position = new Position(['ip' => '8.8.8.8']);
        $json = (string) json_encode($position);

        $this->assertStringContainsString('8.8.8.8', $json);
    }
}
