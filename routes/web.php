<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\VolunteerDashboardController;

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

    // ADMIN ROUTES
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [LaporanController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::post('/laporan/update-status/{id}', [LaporanController::class, 'updateStatus'])->name('laporan.update_status');
        
        // Volunteer Management
        Route::get('/relawan', [VolunteerController::class, 'index'])->name('volunteer.index');
        Route::get('/relawan/laporan', [VolunteerController::class, 'reports'])->name('admin.volunteer.reports');
        Route::get('/relawan/{id}', [VolunteerController::class, 'show'])->name('volunteer.show');
        Route::post('/relawan/{id}/status', [VolunteerController::class, 'updateStatus'])->name('volunteer.update_status');
        Route::post('/relawan/{id}/assign', [VolunteerController::class, 'assign'])->name('volunteer.assign');
    });

    // USER / MASYARAKAT ROUTES
    Route::middleware('role:Masyarakat,Relawan')->group(function () {
        Route::get('/dashboard', [LaporanController::class, 'userDashboard'])->name('dashboard');
        Route::get('/panduan-bencana', function () { return view('panduan.index'); })->name('panduan');
        
        // Map & Information
        Route::get('/peta-bencana', function () { return redirect()->route('dashboard'); })->name('map');

        // Volunteer Registration
        Route::get('/relawan/daftar', [VolunteerController::class, 'create'])->name('volunteer.create');
        Route::post('/relawan/daftar', [VolunteerController::class, 'store'])->name('volunteer.store');

        // Volunteer Reports (hanya relawan yang approved)
        Route::get('/relawan/laporan', [\App\Http\Controllers\VolunteerReportController::class, 'index'])->name('volunteer.reports');
        Route::get('/relawan/laporan/buat', [\App\Http\Controllers\VolunteerReportController::class, 'create'])->name('volunteer.report.create');
        Route::post('/relawan/laporan/buat', [\App\Http\Controllers\VolunteerReportController::class, 'store'])->name('volunteer.report.store');

        // Volunteer Dashboard (khusus relawan approved)
        Route::get('/relawan/dashboard', [VolunteerDashboardController::class, 'index'])->name('volunteer.dashboard');
        Route::post('/relawan/ketersediaan', [VolunteerDashboardController::class, 'toggleAvailability'])->name('volunteer.toggle_availability');
        Route::post('/relawan/notifikasi-dismiss', [VolunteerDashboardController::class, 'dismissNotification'])->name('volunteer.dismiss_notification');
    });

    // SHARED ROUTES (accessible by all authenticated users)
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
    Route::get('/laporan', [MapController::class, 'search'])->name('laporan.index');
    Route::get('/laporan/create', [LaporanController::class, 'create'])->name('laporan.create');
    Route::post('/laporan/store', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/detail/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::get('/info-posko', [MapController::class, 'shelterPage'])->name('shelter');
    Route::get('/cari-bencana', [MapController::class, 'search'])->name('search');

    // API ROUTES
    Route::get('/api/disasters', [MapController::class, 'disasters'])->name('api.disasters');
    Route::get('/api/shelters', [MapController::class, 'shelters'])->name('api.shelters');
    Route::get('/api/pending-reports', function () {
        if (strtolower(auth()->user()->role ?? '') !== 'admin') {
            return response()->json([]);
        }
        return response()->json(
            \App\Models\Disaster::where('status', 'PENDING')
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn($d) => [
                    'id' => $d->id,
                    'title' => $d->title,
                    'reporter' => $d->reporter_name,
                    'date' => $d->created_at?->diffForHumans(),
                    'created_at' => $d->created_at?->toISOString(),
                ])
        );
    })->name('api.pending_reports');

});
