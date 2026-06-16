<?php

namespace App\View\Composers;

use App\Models\Volunteer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VolunteerNotificationComposer
{
    // Penyusun Tampilan Notifikasi Relawan
    public function compose(View $view): void
    {
        $volunteerNotif = null;

        // Jika user login dan memiliki role relawan, cari penugasan aktif yang belum dikonfirmasi
        if (Auth::check() && strtolower(Auth::user()->role ?? '') === 'relawan') {
            // Ambil data penugasan relawan aktif yang berstatus pending beserta detail bencana
            $volunteerNotif = Volunteer::where('user_id', Auth::id())
                ->where('status', Volunteer::STATUS_APPROVED)
                ->whereNotNull('assignment')
                ->where('assignment_status', 'pending')
                ->with(['disaster', 'assignedByUser'])
                ->first();
        }

        $view->with('volunteerNotif', $volunteerNotif);
    }
}
