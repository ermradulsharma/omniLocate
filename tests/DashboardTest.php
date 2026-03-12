<?php

namespace Skywalker\Location\Tests;

use Skywalker\Location\Models\GeoAnalytics;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DashboardTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app['config'];

        $config->set('database.default', 'testbench');
        $config->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $config->set('app.key', 'base64:6Cu/ozj4gPtIjmXjr8EdVnGFNsdRqZfHfVjQkmTlg4Y=');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('location_geo_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->nullable();
            $table->string('country_code')->nullable();
            $table->string('city')->nullable();
            $table->string('isp')->nullable();
            $table->integer('risk_score')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->timestamps();
        });
    }



    public function test_dashboard_stats_response(): void
    {
        GeoAnalytics::create([
            'ip' => '1.1.1.1',
            'country_code' => 'US',
            'risk_score' => 10
        ]);

        GeoAnalytics::create([
            'ip' => '2.2.2.2',
            'country_code' => 'RU',
            'risk_score' => 80
        ]);

        $response = $this->get(route('omni-locate.dashboard.stats'));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'total_requests' => 2,
                    'blocked_threats' => 1,
                    'risk_distribution' => [1, 0, 1]
                ]
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'total_requests',
                    'blocked_threats',
                    'top_countries' => [
                        '*' => ['country_code', 'count']
                    ],
                    'risk_distribution',
                    'logs'
                ]
            ]);
    }
}
