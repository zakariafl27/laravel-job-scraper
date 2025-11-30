<?php

use App\Http\Controllers\Api\JobAlertController;
use App\Http\Controllers\Api\JobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Job Alerts
    Route::apiResource('alerts', JobAlertController::class);
    Route::post('alerts/{alert}/scrape', [JobAlertController::class, 'scrape']);
    Route::get('alerts/{alert}/new-jobs', [JobAlertController::class, 'newJobs']);

    // Jobs - IMPORTANT: specific routes BEFORE apiResource
    Route::get('jobs/statistics', [JobController::class, 'statistics']);
    Route::apiResource('jobs', JobController::class)->only(['index', 'show']);
});