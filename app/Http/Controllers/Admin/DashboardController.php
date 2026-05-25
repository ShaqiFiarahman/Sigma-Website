<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disaster;
use App\Models\Volunteer;

class DashboardController extends Controller
{
    public function index()
    {
        // ─── Statistics ───────────────────────────────────
        $total   = Disaster::count();
        $pending = Disaster::where('status', 'PENDING')->count();
        $selesai = Disaster::where('status', 'RESOLVED')->count();
        $decline = Disaster::where('status', 'DECLINE')->count();
        $awas    = Disaster::where('status', 'AWAS')->count();
        $siaga1  = Disaster::where('status', 'SIAGA_1')->count();
        $siaga2  = Disaster::where('status', 'SIAGA_2')->count();

        // ─── Volunteer Stats ─────────────────────────────
        $totalVolunteers    = Volunteer::count();
        $approvedVolunteers = Volunteer::where('status', Volunteer::STATUS_APPROVED)->count();
        $pendingVolunteers  = Volunteer::where('status', Volunteer::STATUS_PENDING)->count();

        // ─── Chart: 7 hari terakhir ─────────────────────
        $chartLabels = [];
        $chartData   = [];
        $chartVerified = [];
        $chartPending  = [];
        for ($i = 6; $i >= 0; $i--) {
            $date            = now()->subDays($i);
            $chartLabels[]   = $date->format('d M');
            $chartData[]     = Disaster::whereDate('created_at', $date->toDateString())->count();
            $chartVerified[] = Disaster::whereDate('created_at', $date->toDateString())
                                ->whereNotIn('status', ['PENDING', 'DECLINE'])
                                ->count();
            $chartPending[]  = Disaster::whereDate('created_at', $date->toDateString())
                                ->where('status', 'PENDING')
                                ->count();
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
}
