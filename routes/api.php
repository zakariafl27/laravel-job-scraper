<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobAlertController;
use App\Http\Controllers\Api\JobController;

Route::prefix('v1')->group(function () {
    // Job Alerts
    Route::get('alerts', [JobAlertController::class, 'index']);
    Route::post('alerts', [JobAlertController::class, 'store']);
    Route::delete('alerts/{id}', [JobAlertController::class, 'destroy']);

    // Jobs
    Route::get('jobs', [JobController::class, 'index']);
    Route::get('jobs/statistics', [JobController::class, 'statistics']);
});
