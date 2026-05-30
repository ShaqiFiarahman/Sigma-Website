<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Volunteer;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // If user is an approved volunteer, redirect to volunteer dashboard
        $volunteerData = Volunteer::where('user_id', $user->id)->first();

        if ($volunteerData && $volunteerData->status === Volunteer::STATUS_APPROVED) {
            return redirect()->route('volunteer.dashboard');
        }

        $news = $this->getNews();
        $menu = $this->getMenu();

        return view('user.dashboard.index', compact('user', 'news', 'menu', 'volunteerData'));
    }

    public function panduan()
    {
        return view('panduan.index');
    }

    private function getNews(): array
    {
        return News::where('published_at', '>=', now()->subDays(7))
            ->latest('published_at')
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

    private function getMenu(): array
    {
        return [
            ['id' => 2,  'title' => 'Lapor Bencana',      'description' => 'Kirim laporan',          'icon' => 'bi-megaphone-fill'],
            ['id' => 3,  'title' => 'Info Posko',         'description' => 'Titik pengungsian',      'icon' => 'bi-house-heart-fill'],
            ['id' => 10, 'title' => 'Panduan Bencana',    'description' => 'Tips mitigasi',          'icon' => 'bi-book-fill'],
            ['id' => 7,  'title' => 'Cari Bencana',       'description' => 'Pencarian & filter',     'icon' => 'bi-search'],
            ['id' => 5,  'title' => 'Daftar Relawan',     'description' => 'Bergabung jadi relawan', 'icon' => 'bi-person-plus-fill'],
        ];
    }
}
