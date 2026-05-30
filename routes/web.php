<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\NewsController;

// ─────────────────────────────────────────────
//  PUBLIC / AUTH ROUTES
// ─────────────────────────────────────────────
Route::get('/', [AuthController::class, 'showAuth'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─────────────────────────────────────────────
//  AUTHENTICATED ROUTES
// ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // ═══════════════════════════════════════════
    //  ADMIN ROUTES
    // ═══════════════════════════════════════════
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

        // Laporan Management
        Route::get('/laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('admin.laporan');
        Route::get('/api/laporan/{id}', [\App\Http\Controllers\Admin\LaporanController::class, 'detail'])->name('admin.laporan.detail');
        Route::post('/laporan/update-status/{id}', [\App\Http\Controllers\Admin\LaporanController::class, 'updateStatus'])->name('laporan.update_status');
        Route::post('/laporan/update-type/{id}', [\App\Http\Controllers\Admin\LaporanController::class, 'updateType'])->name('laporan.update_type');

        // Shelter Management
        Route::get('/posko/create', [\App\Http\Controllers\Admin\ShelterController::class, 'create'])->name('admin.shelter.create');
        Route::post('/posko/store', [\App\Http\Controllers\Admin\ShelterController::class, 'store'])->name('admin.shelter.store');
        Route::get('/posko/{id}/edit', [\App\Http\Controllers\Admin\ShelterController::class, 'edit'])->name('admin.shelter.edit');
        Route::post('/posko/{id}/update', [\App\Http\Controllers\Admin\ShelterController::class, 'update'])->name('admin.shelter.update');
        Route::delete('/posko/{id}', [\App\Http\Controllers\Admin\ShelterController::class, 'destroy'])->name('admin.shelter.delete');

        // Volunteer Management
        Route::get('/relawan', [\App\Http\Controllers\Admin\VolunteerController::class, 'index'])->name('volunteer.index');
        Route::get('/relawan/laporan', [\App\Http\Controllers\Admin\VolunteerController::class, 'reports'])->name('admin.volunteer.reports');
        Route::get('/relawan/{id}', [\App\Http\Controllers\Admin\VolunteerController::class, 'show'])->name('volunteer.show');
        Route::post('/relawan/{id}/status', [\App\Http\Controllers\Admin\VolunteerController::class, 'updateStatus'])->name('volunteer.update_status');
        Route::post('/relawan/{id}/assign', [\App\Http\Controllers\Admin\VolunteerController::class, 'assign'])->name('volunteer.assign');
    });

    // ═══════════════════════════════════════════
    //  USER / MASYARAKAT ROUTES
    // ═══════════════════════════════════════════
    Route::middleware('role:Masyarakat,Relawan')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/panduan-bencana', [\App\Http\Controllers\User\DashboardController::class, 'panduan'])->name('panduan');
        Route::get('/peta-bencana', function () { return redirect()->route('dashboard'); })->name('map');

        // Volunteer Registration
        Route::get('/relawan/daftar', [\App\Http\Controllers\Volunteer\RegistrationController::class, 'create'])->name('volunteer.create');
        Route::post('/relawan/daftar', [\App\Http\Controllers\Volunteer\RegistrationController::class, 'store'])->name('volunteer.store');

        // Volunteer Reports
        Route::get('/relawan/laporan', [\App\Http\Controllers\Volunteer\ReportController::class, 'index'])->name('volunteer.reports');
        Route::get('/relawan/laporan/buat', [\App\Http\Controllers\Volunteer\ReportController::class, 'create'])->name('volunteer.report.create');
        Route::post('/relawan/laporan/buat', [\App\Http\Controllers\Volunteer\ReportController::class, 'store'])->name('volunteer.report.store');

        // Volunteer Dashboard
        Route::get('/relawan/dashboard', [\App\Http\Controllers\Volunteer\DashboardController::class, 'index'])->name('volunteer.dashboard');
        Route::post('/relawan/ketersediaan', [\App\Http\Controllers\Volunteer\DashboardController::class, 'toggleAvailability'])->name('volunteer.toggle_availability');
        Route::post('/relawan/notifikasi-dismiss', [\App\Http\Controllers\Volunteer\DashboardController::class, 'dismissNotification'])->name('volunteer.dismiss_notification');
    });

    // ═══════════════════════════════════════════
    //  SHARED ROUTES (all authenticated users)
    // ═══════════════════════════════════════════
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
    Route::get('/laporan', [MapController::class, 'search'])->name('laporan.index');
    Route::get('/laporan/create', [\App\Http\Controllers\User\LaporanController::class, 'create'])->name('laporan.create');
    Route::post('/laporan/store', [\App\Http\Controllers\User\LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/detail/{id}', [\App\Http\Controllers\User\LaporanController::class, 'show'])->name('laporan.show');
    Route::get('/info-posko', [MapController::class, 'shelterPage'])->name('shelter');
    Route::get('/cari-bencana', [MapController::class, 'search'])->name('search');

    // API Routes for Map & Dashboard (session authenticated)
    Route::prefix('api')->group(function () {
        Route::get('/disasters', [MapController::class, 'disasters'])->name('api.disasters');
        Route::get('/shelters', [MapController::class, 'shelters'])->name('api.shelters');
        
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/pending-reports', [\App\Http\Controllers\Admin\DashboardController::class, 'pendingReports'])->name('api.pending_reports');
            Route::get('/stats', [\App\Http\Controllers\Admin\DashboardController::class, 'stats'])->name('api.admin_stats');
        });
    });
});
