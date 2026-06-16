<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\NewsController;

// rute publik & auth
Route::get('/', [\App\Http\Controllers\User\DashboardController::class, 'index'])->name('dashboard');
Route::get('/login', [AuthController::class, 'showAuth'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// endpoint vercel cron buat ambil berita otomatis
Route::get('/api/cron/fetch-news', function (\App\Services\NewsService $newsService) {
    $authHeader = request()->header('Authorization');
    $cronSecret = env('CRON_SECRET');
    
    // validasi auth kalo bukan di env local
    if (!app()->environment('local')) {
        if (empty($cronSecret) || $authHeader !== 'Bearer ' . $cronSecret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    try {
        $newsService->fetchNews();
        return response()->json([
            'status' => 'success',
            'message' => 'News fetched successfully'
        ]);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Cron Fetch News Error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// rute api publik buat peta
Route::prefix('api')->group(function () {
    Route::get('/disasters', [MapController::class, 'disasters'])->name('api.disasters');
    Route::get('/shelters', [MapController::class, 'shelters'])->name('api.shelters');
});

// rute halaman publik
Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
Route::get('/laporan', [MapController::class, 'search'])->name('laporan.index');
Route::get('/laporan/detail/{id}', [\App\Http\Controllers\User\LaporanController::class, 'show'])->name('laporan.show');
Route::get('/info-posko', [MapController::class, 'shelterPage'])->name('shelter');
Route::get('/cari-bencana', [MapController::class, 'search'])->name('search');
Route::get('/panduan-bencana', [\App\Http\Controllers\User\DashboardController::class, 'panduan'])->name('panduan');
Route::get('/relawan/daftar', [\App\Http\Controllers\Volunteer\RegistrationController::class, 'create'])->name('volunteer.create');
Route::get('/laporan/create', [\App\Http\Controllers\User\LaporanController::class, 'create'])->name('laporan.create');

// redirect cadangan buat path dashboard
Route::get('/dashboard', function () {
    return redirect()->route('dashboard');
});

// rute yang butuh auth
Route::middleware('auth')->group(function () {

    // rute khusus admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

        // kelola laporan
        Route::get('/laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('admin.laporan');
        Route::get('/api/laporan/{id}', [\App\Http\Controllers\Admin\LaporanController::class, 'detail'])->name('admin.laporan.detail');
        Route::post('/laporan/update-status/{id}', [\App\Http\Controllers\Admin\LaporanController::class, 'updateStatus'])->name('laporan.update_status');
        Route::post('/laporan/update-type/{id}', [\App\Http\Controllers\Admin\LaporanController::class, 'updateType'])->name('laporan.update_type');

        // kelola posko
        Route::get('/posko/create', [\App\Http\Controllers\Admin\ShelterController::class, 'create'])->name('admin.shelter.create');
        Route::post('/posko/store', [\App\Http\Controllers\Admin\ShelterController::class, 'store'])->name('admin.shelter.store');
        Route::get('/posko/{id}/edit', [\App\Http\Controllers\Admin\ShelterController::class, 'edit'])->name('admin.shelter.edit');
        Route::post('/posko/{id}/update', [\App\Http\Controllers\Admin\ShelterController::class, 'update'])->name('admin.shelter.update');
        Route::delete('/posko/{id}', [\App\Http\Controllers\Admin\ShelterController::class, 'destroy'])->name('admin.shelter.delete');

        // kelola relawan
        Route::get('/relawan', [\App\Http\Controllers\Admin\VolunteerController::class, 'index'])->name('volunteer.index');
        Route::get('/relawan/laporan', [\App\Http\Controllers\Admin\VolunteerController::class, 'reports'])->name('admin.volunteer.reports');
        Route::get('/relawan/{id}', [\App\Http\Controllers\Admin\VolunteerController::class, 'show'])->name('volunteer.show');
        Route::post('/relawan/{id}/status', [\App\Http\Controllers\Admin\VolunteerController::class, 'updateStatus'])->name('volunteer.update_status');
        Route::post('/relawan/{id}/assign', [\App\Http\Controllers\Admin\VolunteerController::class, 'assign'])->name('volunteer.assign');
    });

    // rute buat user & relawan
    Route::middleware('role:Masyarakat,Relawan')->group(function () {
        Route::get('/peta-bencana', function () { return redirect()->route('dashboard'); })->name('map');

        // daftar relawan
        Route::post('/relawan/daftar', [\App\Http\Controllers\Volunteer\RegistrationController::class, 'store'])->name('volunteer.store');

        // laporan relawan
        Route::get('/relawan/laporan', [\App\Http\Controllers\Volunteer\ReportController::class, 'index'])->name('volunteer.reports');
        Route::get('/relawan/laporan/buat', [\App\Http\Controllers\Volunteer\ReportController::class, 'create'])->name('volunteer.report.create');
        Route::post('/relawan/laporan/buat', [\App\Http\Controllers\Volunteer\ReportController::class, 'store'])->name('volunteer.report.store');

        // dashboard relawan
        Route::get('/relawan/dashboard', [\App\Http\Controllers\Volunteer\DashboardController::class, 'index'])->name('volunteer.dashboard');
        Route::post('/relawan/ketersediaan', [\App\Http\Controllers\Volunteer\DashboardController::class, 'toggleAvailability'])->name('volunteer.toggle_availability');
        Route::post('/relawan/notifikasi-dismiss', [\App\Http\Controllers\Volunteer\DashboardController::class, 'dismissNotification'])->name('volunteer.dismiss_notification');
        Route::post('/relawan/penugasan/terima', [\App\Http\Controllers\Volunteer\DashboardController::class, 'acceptAssignment'])->name('volunteer.accept_assignment');
        Route::post('/relawan/penugasan/tolak', [\App\Http\Controllers\Volunteer\DashboardController::class, 'rejectAssignment'])->name('volunteer.reject_assignment');
    });

    // rute bersama (semua user yang login)
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    Route::post('/laporan/store', [\App\Http\Controllers\User\LaporanController::class, 'store'])->name('laporan.store');

    // rute api dashboard (pake session auth)
    Route::prefix('api')->group(function () {
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/pending-reports', [\App\Http\Controllers\Admin\DashboardController::class, 'pendingReports'])->name('api.pending_reports');
            Route::get('/stats', [\App\Http\Controllers\Admin\DashboardController::class, 'stats'])->name('api.admin_stats');
        });
    });
});
