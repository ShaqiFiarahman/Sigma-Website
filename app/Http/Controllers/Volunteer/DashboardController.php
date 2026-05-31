<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Volunteer;
use App\Models\VolunteerReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the volunteer dashboard for approved volunteers
     */
    public function index()
    {
        $user = auth()->user();
        $volunteerData = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->with(['disaster', 'assignedByUser'])
            ->firstOrFail();

        $news = $this->getNews();
        $menu = config('menu.relawan');

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

    public function acceptAssignment()
    {
        $user = auth()->user();
        $volunteer = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        if ($volunteer->assignment_status !== 'pending') {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak ada penugasan yang menunggu konfirmasi.');
        }

        $volunteer->update([
            'assignment_status' => 'accepted',
            'assignment_notified_at' => now(),
            'assignment_rejection_reason' => null,
        ]);

        return redirect()->route('dashboard')
            ->with('msg', 'Penugasan berhasil diterima. Selamat bertugas!');
    }

    public function rejectAssignment(Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user = auth()->user();
        $volunteer = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        if ($volunteer->assignment_status !== 'pending') {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak ada penugasan yang menunggu konfirmasi.');
        }

        $volunteer->update([
            'assignment_status' => 'rejected',
            'assignment_rejection_reason' => $request->rejection_reason,
            'assignment_notified_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('msg', 'Penugasan ditolak. Admin akan meninjau alasan Anda.');
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
}
