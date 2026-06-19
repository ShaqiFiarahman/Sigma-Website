<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disaster;
use App\Models\Volunteer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Statistik Beranda Admin
    public function index()
    {
        // Ambil jumlah laporan bencana yang dikelompokkan berdasarkan statusnya
        $statusCounts = Disaster::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $total   = $statusCounts->sum();
        $pending = $statusCounts->get('PENDING', 0);
        $selesai = $statusCounts->get('RESOLVED', 0);
        $decline = $statusCounts->get('DECLINE', 0);
        $awas    = $statusCounts->get('AWAS', 0);
        $siaga1  = $statusCounts->get('SIAGA_1', 0);
        $siaga2  = $statusCounts->get('SIAGA_2', 0);

        // Ambil jumlah pendaftaran relawan yang dikelompokkan berdasarkan status persetujuan
        $volunteerCounts = Volunteer::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalVolunteers    = $volunteerCounts->sum();
        $approvedVolunteers = $volunteerCounts->get(Volunteer::STATUS_APPROVED, 0);
        $pendingVolunteers  = $volunteerCounts->get(Volunteer::STATUS_PENDING, 0);

        $startDate = now()->subDays(6)->startOfDay();

        // Ambil statistik harian bencana buatan user dalam 7 hari terakhir
        $dailyStatsRaw = Disaster::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw("sum(case when status NOT IN ('PENDING','DECLINE') then 1 else 0 end) as verified"),
                DB::raw("sum(case when status = 'PENDING' then 1 else 0 end) as pending_count")
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('date');

        $chartLabels   = [];
        $chartData     = [];
        $chartVerified = [];
        $chartPending  = [];

        // Iterasi untuk mengisi data chart selama 7 hari ke belakang
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->toDateString();
            $chartLabels[]   = $date->format('d M');
            $chartData[]     = (int) ($dailyStatsRaw[$dateKey]->total ?? 0);
            $chartVerified[] = (int) ($dailyStatsRaw[$dateKey]->verified ?? 0);
            $chartPending[]  = (int) ($dailyStatsRaw[$dateKey]->pending_count ?? 0);
        }

        // Ambil daftar semua bencana untuk di-filter di sisi klien
        $allDisasters = Disaster::select('id', 'status', 'created_at')
            ->latest()
            ->get()
            ->map(fn($d) => [
                'status' => $d->status,
                'date' => $d->created_at?->toISOString(),
            ]);

        return view('admin.dashboard.index', compact(
            'total', 'pending', 'selesai', 'decline', 'awas', 'siaga1', 'siaga2',
            'totalVolunteers', 'approvedVolunteers', 'pendingVolunteers',
            'chartLabels', 'chartData', 'chartVerified', 'chartPending',
            'allDisasters'
        ));
    }

    // API Waktu Nyata
    public function pendingReports(): JsonResponse
    {
        // Ambil 10 laporan bencana pending terbaru untuk notifikasi admin
        return response()->json(
            Disaster::where('status', 'PENDING')
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
    }

    public function stats(): JsonResponse
    {
        // Ambil semua data bencana secara ringkas untuk perhitungan statistik waktu nyata
        $disasters = Disaster::select('id', 'status', 'created_at')->get();
        $statusCounts = $disasters->groupBy('status')->map(fn($group) => $group->count());

        $total = $disasters->count();
        $pending = $statusCounts->get('PENDING', 0);
        $decline = $statusCounts->get('DECLINE', 0);
        $awas = $statusCounts->get('AWAS', 0);
        $siaga1 = $statusCounts->get('SIAGA_1', 0);
        $siaga2 = $statusCounts->get('SIAGA_2', 0);
        $selesai = $total - $pending - $decline;

        // Hitung jumlah laporan bencana yang masuk hari ini
        $todayCount = Disaster::where('created_at', '>=', today())->count();

        // Hitung jumlah laporan bencana terverifikasi selama seminggu terakhir
        $weekVerified = Disaster::where('created_at', '>=', now()->subWeek())
            ->whereNotIn('status', ['PENDING', 'DECLINE'])
            ->count();

        // Ambil 5 laporan bencana pending terbaru untuk dashboard admin
        $pendingItems = Disaster::where('status', 'PENDING')
            ->latest()
            ->limit(5)
            ->get(['id', 'title', 'location', 'created_at'])
            ->map(fn($d) => [
                'id'         => $d->id,
                'judul'      => $d->title,
                'lokasi'     => $d->location ?? '',
                'tanggal'    => $d->created_at?->format('d M Y'),
                'created_at' => $d->created_at?->toISOString(),
            ]);

        return response()->json([
            'total'          => $total,
            'pending'        => $pending,
            'selesai'        => $selesai,
            'decline'        => $decline,
            'awas'           => $awas,
            'siaga1'         => $siaga1,
            'siaga2'         => $siaga2,
            'verified_total' => $selesai,
            'week_verified'  => $weekVerified,
            'today_count'    => $todayCount,
            'all_disasters'  => $disasters->map(fn($d) => [
                'id'     => $d->id,
                'status' => $d->status,
                'date'   => $d->created_at?->toIso8601String(),
            ]),
            'pending_items'  => $pendingItems,
        ]);
    }
}
