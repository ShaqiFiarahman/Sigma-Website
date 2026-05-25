<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
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
