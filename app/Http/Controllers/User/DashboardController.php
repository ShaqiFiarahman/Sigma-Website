<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Volunteer;
use App\Models\VolunteerReport;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user?->role ?? 'Masyarakat';

        $news = $this->getNews();
        $menu = $this->getMenu($role);

        // Data relawan (pending/rejected/approved)
        $volunteerData = Volunteer::where('user_id', $user->id)->first();

        // Khusus relawan approved → render volunteer dashboard
        if ($volunteerData && $volunteerData->status === Volunteer::STATUS_APPROVED) {
            $totalReports = VolunteerReport::where('volunteer_id', $volunteerData->id)->count();
            $reportsThisMonth = VolunteerReport::where('volunteer_id', $volunteerData->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $recentReports = VolunteerReport::where('volunteer_id', $volunteerData->id)
                ->with('disaster')
                ->latest()
                ->limit(3)
                ->get();

            $teamMembers = collect();
            if ($volunteerData->assignment) {
                $teamMembers = Volunteer::where('status', Volunteer::STATUS_APPROVED)
                    ->where('assignment', $volunteerData->assignment)
                    ->where('id', '!=', $volunteerData->id)
                    ->limit(5)
                    ->get();
            }

            return view('volunteer.dashboard.index', [
                'user' => $user,
                'volunteer' => $volunteerData,
                'news' => $news,
                'menu' => $menu,
                'totalReports' => $totalReports,
                'reportsThisMonth' => $reportsThisMonth,
                'recentReports' => $recentReports,
                'teamMembers' => $teamMembers,
            ]);
        }

        $volunteerDashboard = null;

        return view('user.dashboard.index', compact('user', 'news', 'menu', 'volunteerData', 'volunteerDashboard'));
    }

    private function getNews(): array
    {
        return News::latest('published_at')
            ->limit(6)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'time' => $item->published_at->diffForHumans(),
                    'category' => strtoupper($item->source),
                    'tone' => 'info',
                    'image_url' => $item->image_url,
                    'url' => $item->url,
                    'source' => $item->source,
                ];
            })
            ->toArray();
    }

    private function getMenu(string $role): array
    {
        if (strtolower($role) === 'relawan') {
            return [
                ['id' => 12, 'title' => 'Lapor Tugas',       'description' => 'Kirim laporan tugas',  'icon' => 'bi-send-fill'],
                ['id' => 3,  'title' => 'Info Posko',        'description' => 'Titik pengungsian',    'icon' => 'bi-house-heart-fill'],
                ['id' => 10, 'title' => 'Panduan Bencana',   'description' => 'Tips mitigasi',        'icon' => 'bi-book-fill'],
                ['id' => 7,  'title' => 'Cari Bencana',      'description' => 'Pencarian & filter',   'icon' => 'bi-search'],
            ];
        }

        return [
            ['id' => 2,  'title' => 'Lapor Bencana',      'description' => 'Kirim laporan',      'icon' => 'bi-megaphone-fill'],
            ['id' => 3,  'title' => 'Info Posko',         'description' => 'Titik pengungsian',  'icon' => 'bi-house-heart-fill'],
            ['id' => 10, 'title' => 'Panduan Bencana',    'description' => 'Tips mitigasi',      'icon' => 'bi-book-fill'],
            ['id' => 7,  'title' => 'Cari Bencana',       'description' => 'Pencarian & filter', 'icon' => 'bi-search'],
            ['id' => 5,  'title' => 'Daftar Relawan',     'description' => 'Bergabung jadi relawan', 'icon' => 'bi-person-plus-fill'],
        ];
    }
}
