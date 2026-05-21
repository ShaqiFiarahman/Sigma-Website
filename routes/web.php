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

    Route::get('/api/admin-stats', function () {
        if (strtolower(auth()->user()->role ?? '') !== 'admin') {
            return response()->json([]);
        }

        $period = request('period', '7d'); // 1d, 7d, 30d, all
        $query = \App\Models\Disaster::query();

        if ($period === '1d') {
            $query->where('created_at', '>=', now()->subDay());
            $days = 1;
        } elseif ($period === '7d') {
            $query->where('created_at', '>=', now()->subDays(7));
            $days = 7;
        } elseif ($period === '30d') {
            $query->where('created_at', '>=', now()->subDays(30));
            $days = 30;
        } else {
            $days = max(7, (int) ceil(now()->diffInDays(\App\Models\Disaster::min('created_at') ?? now())));
        }

        // Stats
        $total = $query->count();
        $pending = (clone $query)->where('status', 'PENDING')->count();
        $selesai = (clone $query)->whereNotIn('status', ['PENDING', 'DECLINE'])->count();
        $decline = (clone $query)->where('status', 'DECLINE')->count();
        $awas = (clone $query)->where('status', 'AWAS')->count();
        $siaga1 = (clone $query)->where('status', 'SIAGA_1')->count();
        $siaga2 = (clone $query)->where('status', 'SIAGA_2')->count();

        // Chart data
        $chartDays = min($days, 7);
        if ($period === '30d') $chartDays = 14;
        if ($period === 'all') $chartDays = 14;

        $chartLabels = [];
        $chartData = [];
        $chartVerified = [];
        $chartPending = [];

        for ($i = $chartDays - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartData[] = \App\Models\Disaster::whereDate('created_at', $date->toDateString())->count();
            $chartVerified[] = \App\Models\Disaster::whereDate('created_at', $date->toDateString())
                ->whereNotIn('status', ['PENDING', 'DECLINE'])->count();
            $chartPending[] = \App\Models\Disaster::whereDate('created_at', $date->toDateString())
                ->where('status', 'PENDING')->count();
        }

        return response()->json([
            'total' => $total,
            'pending' => $pending,
            'selesai' => $selesai,
            'decline' => $decline,
            'awas' => $awas,
            'siaga1' => $siaga1,
            'siaga2' => $siaga2,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'chartVerified' => $chartVerified,
            'chartPending' => $chartPending,
        ]);
    })->name('api.admin_stats');
});


