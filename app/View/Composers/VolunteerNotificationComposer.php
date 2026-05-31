<?php

namespace App\View\Composers;

use App\Models\Volunteer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VolunteerNotificationComposer
{
    /**
     * Bind data to the volunteer notification view.
     */
    public function compose(View $view): void
    {
        $volunteerNotif = null;

        if (Auth::check() && strtolower(Auth::user()->role ?? '') === 'relawan') {
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
