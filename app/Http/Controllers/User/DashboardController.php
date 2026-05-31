<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FetchesNews;
use App\Models\Volunteer;

class DashboardController extends Controller
{
    use FetchesNews;

    public function index()
    {
        $user = auth()->user();

        // If user is an approved volunteer, redirect to volunteer dashboard
        $volunteerData = Volunteer::where('user_id', $user->id)->first();

        if ($volunteerData && $volunteerData->status === Volunteer::STATUS_APPROVED) {
            return redirect()->route('volunteer.dashboard');
        }

        $news = $this->getNews();
        $menu = config('menu.masyarakat');

        return view('user.dashboard.index', compact('user', 'news', 'menu', 'volunteerData'));
    }

    public function panduan()
    {
        return view('panduan.index');
    }
}
