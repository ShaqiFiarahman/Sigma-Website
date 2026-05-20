<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\VolunteerController;

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
        Route::get('/relawan/{id}', [VolunteerController::class, 'show'])->name('volunteer.show');
        Route::post('/relawan/{id}/status', [VolunteerController::class, 'updateStatus'])->name('volunteer.update_status');
        Route::post('/relawan/{id}/assign', [VolunteerController::class, 'assign'])->name('volunteer.assign');
    });

    // USER / MASYARAKAT ROUTES
    Route::middleware('role:Masyarakat,Relawan')->group(function () {
        Route::get('/dashboard', [LaporanController::class, 'userDashboard'])->name('dashboard');
        Route::get('/panduan-bencana', function () { return view('user.panduan'); })->name('panduan');
        
        // Map & Information
        Route::get('/peta-bencana', function () { return redirect()->route('dashboard'); })->name('map');

        // Volunteer Registration
        Route::get('/relawan/daftar', [VolunteerController::class, 'create'])->name('volunteer.create');
        Route::post('/relawan/daftar', [VolunteerController::class, 'store'])->name('volunteer.store');
    });

    // SHARED ROUTES (accessible by all authenticated users)
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
});


