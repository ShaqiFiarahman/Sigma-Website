<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disaster;
use App\Models\Volunteer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ─── Statistics (1 query instead of 7) ───────────
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

        // ─── Volunteer Stats (1 query instead of 3) ──────
        $volunteerCounts = Volunteer::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalVolunteers    = $volunteerCounts->sum();
        $approvedVolunteers = $volunteerCounts->get(Volunteer::STATUS_APPROVED, 0);
        $pendingVolunteers  = $volunteerCounts->get(Volunteer::STATUS_PENDING, 0);

        // ─── Chart: 7 hari terakhir (1 query instead of 21) ──
        $startDate = now()->subDays(6)->startOfDay();

        $dailyStats = Disaster::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw("sum(case when status NOT IN ('PENDING','DECLINE') then 1 else 0 end) as verified"),
                DB::raw("sum(case when status = 'PENDING' then 1 else 0 end) as pending_count")
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date')
            ->toArray();

        // Re-query to get all columns we need
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

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->toDateString();
            $chartLabels[]   = $date->format('d M');
            $chartData[]     = (int) ($dailyStatsRaw[$dateKey]->total ?? 0);
            $chartVerified[] = (int) ($dailyStatsRaw[$dateKey]->verified ?? 0);
            $chartPending[]  = (int) ($dailyStatsRaw[$dateKey]->pending_count ?? 0);
        }

        // ─── All disasters for client-side period filtering ──
        $allDisasters = Disaster::select('id', 'status', 'created_at')
            ->latest()
            ->get()
            ->map(fn($d) => [
                'status' => $d->status,
                'date' => $d->created_at->toISOString(),
            ]);

        return view('admin.dashboard.index', compact(
            'total', 'pending', 'selesai', 'decline', 'awas', 'siaga1', 'siaga2',
            'totalVolunteers', 'approvedVolunteers', 'pendingVolunteers',
            'chartLabels', 'chartData', 'chartVerified', 'chartPending',
            'allDisasters'
        ));
    }

    /**
     * API: Get pending reports for admin notification dropdown
     */
    public function pendingReports(): JsonResponse
    {
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

    /**
     * API: Get admin statistics for real-time dashboard updates
     */
    public function stats(): JsonResponse
    {
        $disasters = Disaster::all();
        $statusCounts = $disasters->groupBy('status')->map->count();

        return response()->json([
            'total'          => $disasters->count(),
            'pending'        => $statusCounts->get('PENDING', 0),
            'selesai'        => $disasters->whereNotIn('status', ['PENDING', 'DECLINE'])->count(),
            'decline'        => $statusCounts->get('DECLINE', 0),
            'awas'           => $statusCounts->get('AWAS', 0),
            'siaga1'         => $statusCounts->get('SIAGA_1', 0),
            'siaga2'         => $statusCounts->get('SIAGA_2', 0),
            'verified_total' => $disasters->whereNotIn('status', ['PENDING', 'DECLINE'])->count(),
            'week_verified'  => Disaster::where('created_at', '>=', now()->subWeek())
                ->whereNotIn('status', ['PENDING', 'DECLINE'])->count(),
            'today_count'    => Disaster::whereDate('created_at', today())->count(),
            'all_disasters'  => $disasters->map(fn($d) => [
                'id'     => $d->id,
                'status' => $d->status,
                'date'   => $d->created_at?->toIso8601String(),
            ]),
            'pending_items'  => Disaster::where('status', 'PENDING')
                ->latest()->limit(5)->get()->map(fn($d) => [
                    'id'         => $d->id,
                    'judul'      => $d->title,
                    'lokasi'     => $d->location ?? '',
                    'tanggal'    => $d->created_at?->format('d M Y'),
                    'created_at' => $d->created_at?->toISOString(),
                ]),
        ]);
    }
}
