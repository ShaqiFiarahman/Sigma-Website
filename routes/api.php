<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes registered here are automatically prefixed with /api and use
| stateless middleware (no session/cookies). Ideal for JSON endpoints.
|
*/

// ─────────────────────────────────────────────
//  PUBLIC MAP DATA (authenticated)
// ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/disasters', [MapController::class, 'disasters'])->name('api.disasters');
    Route::get('/shelters', [MapController::class, 'shelters'])->name('api.shelters');
});

// ─────────────────────────────────────────────
//  ADMIN API
// ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/pending-reports', [AdminDashboardController::class, 'pendingReports'])->name('api.pending_reports');
    Route::get('/stats', [AdminDashboardController::class, 'stats'])->name('api.admin_stats');
});
