<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FetchesNews;
use App\Models\Volunteer;

class DashboardController extends Controller
{
    use FetchesNews;

    // Dashboard
    public function index()
    {
        $user = auth()->user();

        $volunteerData = null;
        // Kalau user sedang login, cek role dan status relawannya
        if ($user) {
            // Kalau user adalah admin, alihkan ke dashboard admin
            if (strtolower($user->role) === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // Cari data relawan berdasarkan ID user
            $volunteerData = Volunteer::where('user_id', $user->id)->first();
            // Kalau user terdaftar sebagai relawan yang disetujui, alihkan ke dashboard relawan
            if ($volunteerData && $volunteerData->status === Volunteer::STATUS_APPROVED) {
                return redirect()->route('volunteer.dashboard');
            }
        }

        $news = $this->getNews();
        $menu = config('menu.masyarakat');

        return view('user.dashboard.index', compact('user', 'news', 'menu', 'volunteerData'));
    }

    // Panduan
    public function panduan()
    {
        return view('panduan.index');
    }
}
