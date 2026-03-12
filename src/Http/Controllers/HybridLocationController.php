<?php

namespace Skywalker\Location\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Skywalker\Location\Services\HybridVerifier;
use Skywalker\Location\Support\Concerns\ApiResponse;

class HybridLocationController extends Controller
{
    use ApiResponse;
    /**
     * Verify location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Skywalker\Location\Services\HybridVerifier  $verifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, HybridVerifier $verifier)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'ip' => 'nullable|ip',
        ]);

        $ip = (string) $request->ip();
        // Allow passing IP for testing purposes IF in debug mode AND (from a local IP OR in testing environment)
        if (config('app.debug') && (app()->environment('testing') || $ip === '127.0.0.1' || $ip === '::1')) {
            $testIp = $request->input('test_ip');
            if (is_string($testIp)) {
                $ip = $testIp;
            }
        }

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        return $this->apiSuccess(
            $verifier->verify(
                $ip,
                is_numeric($latitude) ? (float) $latitude : 0.0,
                is_numeric($longitude) ? (float) $longitude : 0.0
            ),
            'Location verified successfully'
        );

        // Log to analytics if blocked/spoofed? 
        // Logic could be added here or via middleware.
    }
}
