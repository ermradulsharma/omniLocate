<?php

use Illuminate\Support\Facades\Route;
use Skywalker\Location\Http\Controllers\HybridLocationController;

Route::group([
    'prefix' => 'omni-locate',
    'as' => 'omni-locate.',
    'middleware' => (array) config('location.routes.middleware', ['web']),
], function () {
    Route::post('/verify', [HybridLocationController::class, 'verify'])->name('verify');

    // Dashboard protected by specific middleware
    Route::group([
        'middleware' => (array) config('location.dashboard.middleware', ['web']),
    ], function () {
        Route::get('/dashboard', [\Skywalker\Location\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/stats', [\Skywalker\Location\Http\Controllers\DashboardController::class, 'stats'])->name('dashboard.stats');
    });
});

