<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\VolunteerController;

Route::get('/', [AuthController::class, 'showAuth'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/panduan-bencana', function () {
        return view('pages.panduan-bencana');
    })->name('panduan');

    Route::get('/dashboard', [LaporanController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create', [LaporanController::class, 'create'])->name('laporan.create');
    Route::post('/laporan/store', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/detail/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::post('/laporan/update-status/{id}', [LaporanController::class, 'updateStatus'])
        ->name('laporan.update_status')
        ->middleware('role:admin,BNPB');

    // Map routes
    Route::get('/peta-bencana', [MapController::class, 'index'])->name('map');
    Route::get('/info-posko', [MapController::class, 'shelterPage'])->name('shelter');
    Route::get('/cari-bencana', [MapController::class, 'search'])->name('search');
    Route::get('/api/disasters', [MapController::class, 'disasters'])->name('api.disasters');
    Route::get('/api/shelters', [MapController::class, 'shelters'])->name('api.shelters');

    // Volunteer routes
    Route::get('/relawan/daftar', [VolunteerController::class, 'create'])->name('volunteer.create');
    Route::post('/relawan/daftar', [VolunteerController::class, 'store'])->name('volunteer.store');
    Route::get('/relawan', [VolunteerController::class, 'index'])->name('volunteer.index')->middleware('role:admin,BNPB');
    Route::get('/relawan/{id}', [VolunteerController::class, 'show'])->name('volunteer.show');
    Route::post('/relawan/{id}/status', [VolunteerController::class, 'updateStatus'])
        ->name('volunteer.update_status')
        ->middleware('role:admin,BNPB');
    Route::post('/relawan/{id}/assign', [VolunteerController::class, 'assign'])
        ->name('volunteer.assign')
        ->middleware('role:admin,BNPB');
});


