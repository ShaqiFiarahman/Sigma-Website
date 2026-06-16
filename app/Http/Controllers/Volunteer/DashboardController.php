<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FetchesNews;
use App\Models\Volunteer;
use App\Models\VolunteerReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use FetchesNews;

    // Beranda Relawan
    public function index()
    {
        $user = auth()->user();
        // Ambil data relawan yang berstatus aktif/disetujui berdasarkan ID user
        $volunteerData = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->with(['disaster', 'assignedByUser'])
            ->firstOrFail();

        $news = $this->getNews();
        $menu = config('menu.relawan');

        // Hitung total laporan yang pernah dibuat oleh relawan ini
        $totalReports = VolunteerReport::where('volunteer_id', $volunteerData->id)->count();
        // Hitung jumlah laporan yang dibuat relawan ini pada bulan berjalan
        $reportsThisMonth = VolunteerReport::where('volunteer_id', $volunteerData->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Ambil 3 laporan terakhir yang dibuat oleh relawan ini beserta data bencananya
        $recentReports = VolunteerReport::where('volunteer_id', $volunteerData->id)
            ->with('disaster')
            ->latest()
            ->limit(3)
            ->get();

        $teamMembers = collect();
        // Jika relawan memiliki tugas penugasan, cari anggota tim lain dengan tugas yang sama
        if ($volunteerData->assignment) {
            // Ambil data anggota tim relawan lain yang ditugaskan di lokasi bencana yang sama
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

    // Ketersediaan Relawan
    public function toggleAvailability(Request $request)
    {
        // Ambil data relawan aktif untuk memperbarui status ketersediaan
        $volunteer = Volunteer::where('user_id', auth()->id())
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        // Pastikan status ketersediaan yang dikirim bernilai available atau unavailable
        $request->validate([
            'availability' => 'required|in:available,unavailable',
        ]);

        $volunteer->update(['availability' => $request->availability]);

        return redirect()->route('dashboard')
            ->with('msg', 'Status ketersediaan berhasil diperbarui.');
    }

    // Manajemen Penugasan
    public function dismissNotification()
    {
        // Ambil data relawan aktif untuk menolak/membaca notifikasi penugasan
        $volunteer = Volunteer::where('user_id', auth()->id())
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        $volunteer->update(['assignment_notified_at' => now()]);

        return redirect()->route('dashboard');
    }

    public function acceptAssignment()
    {
        // Ambil data relawan aktif untuk menerima penugasan
        $volunteer = Volunteer::where('user_id', auth()->id())
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        // Jika status penugasan bukan pending, kembalikan ke dashboard dengan error
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
        // Validasi alasan penolakan penugasan agar tidak kosong
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // Ambil data relawan aktif untuk menolak penugasan
        $volunteer = Volunteer::where('user_id', auth()->id())
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        // Jika status penugasan bukan pending, kembalikan dengan pesan error
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
}
