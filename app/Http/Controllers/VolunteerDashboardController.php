<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerDashboardController extends Controller
{
    /**
     * Volunteer Dashboard — render langsung tanpa redirect
     */
    public function index()
    {
        // Langsung arahkan ke route dashboard yang akan render volunteer.dashboard view
        return app(\App\Http\Controllers\LaporanController::class)->userDashboard();
    }

    /**
     * Toggle ketersediaan relawan
     */
    public function toggleAvailability(Request $request)
    {
        $user = auth()->user();
        $volunteer = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        $request->validate([
            'availability' => 'required|in:available,unavailable',
        ]);

        $volunteer->update(['availability' => $request->availability]);

        return redirect()->route('dashboard')
            ->with('msg', 'Status ketersediaan berhasil diperbarui.');
    }

    /**
     * Dismiss assignment notification
     */
    public function dismissNotification()
    {
        $user = auth()->user();
        $volunteer = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        $volunteer->update(['assignment_notified_at' => now()]);

        return redirect()->route('dashboard');
    }
}
