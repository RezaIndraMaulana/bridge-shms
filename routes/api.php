<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BridgeController;
use App\Http\Controllers\Api\CameraController;
use App\Http\Controllers\Api\CameraDataController;
use App\Http\Controllers\Api\AccelerometerSensorController;
use App\Http\Controllers\Api\AccelerometerDataController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\SinkNodeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// ─── Auth (public) ───────────────────────────────────────────────────────────
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me',      [AuthController::class, 'me']);

    // ─── Location Reference Data ─────────────────────────────────────────────
    Route::prefix('countries')->group(function () {
        Route::get('/',       [LocationController::class, 'countriesIndex']);
        Route::post('/',      [LocationController::class, 'countriesStore']);
        Route::put('/{id}',   [LocationController::class, 'countriesUpdate']);
        Route::delete('/{id}',[LocationController::class, 'countriesDestroy']);
    });

    Route::prefix('provinces')->group(function () {
        Route::get('/',       [LocationController::class, 'provincesIndex']);
        Route::post('/',      [LocationController::class, 'provincesStore']);
        Route::put('/{id}',   [LocationController::class, 'provincesUpdate']);
        Route::delete('/{id}',[LocationController::class, 'provincesDestroy']);
    });

    Route::prefix('cities')->group(function () {
        Route::get('/',       [LocationController::class, 'citiesIndex']);
        Route::post('/',      [LocationController::class, 'citiesStore']);
        Route::put('/{id}',   [LocationController::class, 'citiesUpdate']);
        Route::delete('/{id}',[LocationController::class, 'citiesDestroy']);
    });

    // ─── Companies ───────────────────────────────────────────────────────────
    Route::apiResource('companies', CompanyController::class);

    // ─── Bridges ─────────────────────────────────────────────────────────────
    Route::apiResource('bridges', BridgeController::class);

    // ─── Users ───────────────────────────────────────────────────────────────
    Route::apiResource('users', UserController::class);

    // ─── Sink Nodes ──────────────────────────────────────────────────────────
    Route::apiResource('sink-nodes', SinkNodeController::class);

    // ─── Cameras ─────────────────────────────────────────────────────────────
    Route::apiResource('cameras', CameraController::class);

    // ─── Camera Data (composite PK → no {id}) ────────────────────────────────
    Route::get('camera-data',    [CameraDataController::class, 'index']);
    Route::post('camera-data',   [CameraDataController::class, 'store']);
    Route::put('camera-data',    [CameraDataController::class, 'update']);
    Route::delete('camera-data', [CameraDataController::class, 'destroy']);

    // ─── Accelerometer Sensors ───────────────────────────────────────────────
    Route::apiResource('accelerometer-sensors', AccelerometerSensorController::class);

    // ─── Accelerometer Data (single + batch) ─────────────────────────────────
    Route::get('accelerometer-data',  [AccelerometerDataController::class, 'index']);
    Route::post('accelerometer-data', [AccelerometerDataController::class, 'store']);
});
