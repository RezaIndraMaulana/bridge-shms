<?php

use App\Http\Controllers\BridgeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/bridges/{bridge}', [DashboardController::class, 'show'])->name('dashboard.bridge.show');

    // ── Management Area ──────────────────────────────────────────────────

    // Manage Bridges (Superadmin only)
    Route::middleware(['role:superadmin'])->prefix('manage/bridges')->name('manage.bridges.')->group(function () {
        Route::get('/',              [BridgeController::class, 'index'])->name('index');
        Route::get('/create',        [BridgeController::class, 'create'])->name('create');
        Route::post('/',             [BridgeController::class, 'store'])->name('store');
        Route::get('/{bridge}/edit', [BridgeController::class, 'edit'])->name('edit');
        Route::put('/{bridge}',      [BridgeController::class, 'update'])->name('update');
        Route::delete('/{bridge}',   [BridgeController::class, 'destroy'])->name('destroy');
    });

    // Manage Sensors (Superadmin only)
    Route::middleware(['role:superadmin'])->prefix('manage/sensors')->name('manage.sensors.')->group(function () {
        Route::get('/',               [SensorController::class, 'index'])->name('index');
        Route::post('/',              [SensorController::class, 'store'])->name('store');
        Route::put('/{sensor}',       [SensorController::class, 'update'])->name('update');
        Route::delete('/{sensor}',    [SensorController::class, 'destroy'])->name('destroy');
    });

    // Manage Users (Read: Superadmin & Company; Write: Superadmin only)
    Route::prefix('manage/users')->name('manage.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('role:superadmin,company')->name('index');

        Route::middleware(['role:superadmin'])->group(function () {
            Route::post('/',         [UserController::class, 'store'])->name('store');
            Route::put('/{user}',    [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
    });

    // Manage Companies (Read: Superadmin & Company; Write: Superadmin only)
    Route::prefix('manage/companies')->name('manage.companies.')->group(function () {
        Route::get('/',          [CompanyController::class, 'index'])->middleware('role:superadmin,company')->name('index');
        Route::get('/{company}', [CompanyController::class, 'show'])->middleware('role:superadmin,company')->name('show');

        Route::middleware(['role:superadmin'])->group(function () {
            Route::post('/',                             [CompanyController::class, 'store'])->name('store');
            Route::put('/{company}',                     [CompanyController::class, 'update'])->name('update');
            Route::delete('/{company}',                  [CompanyController::class, 'destroy'])->name('destroy');
            Route::post('/{company}/bridges',            [CompanyController::class, 'assignBridge'])->name('bridges.assign');
            Route::delete('/{company}/bridges/{bridge}', [CompanyController::class, 'removeBridge'])->name('bridges.remove');
        });
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
