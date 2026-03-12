<?php

namespace Skywalker\Location\Http\Controllers;

use Illuminate\Routing\Controller;
use Skywalker\Location\Models\GeoAnalytics;
use Illuminate\Support\Facades\DB;
use Skywalker\Location\Support\Concerns\ApiResponse;

class DashboardController extends Controller
{
    use ApiResponse;
    /**
     * Show the dashboard view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('location::dashboard');
    }

    /**
     * Get stats for the dashboard charts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        // 1. Total Requests
        $total = GeoAnalytics::count();

        // 2. Blocked / High Risk (Assume > 70 is effectively blocked/throttled)
        $threats = GeoAnalytics::where('risk_score', '>=', 70)->count();

        // 3. Top Countries
        $topCountries = GeoAnalytics::select('country_code', DB::raw('count(*) as count'))
            ->groupBy('country_code')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // 4. Risk Distribution
        $lowRisk = GeoAnalytics::where('risk_score', '<', 30)->count();
        $medRisk = GeoAnalytics::whereBetween('risk_score', [30, 69])->count();
        $highRisk = GeoAnalytics::where('risk_score', '>=', 70)->count();

        // 5. Recent Logs
        $logs = GeoAnalytics::query()->latest()->limit(10)->get();

        return $this->apiSuccess([
            'total_requests' => $total,
            'blocked_threats' => $threats,
            'top_countries' => $topCountries,
            'risk_distribution' => [$lowRisk, $medRisk, $highRisk],
            'logs' => $logs
        ], 'Dashboard statistics retrieved successfully');
    }
}
