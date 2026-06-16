<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Disaster;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\VolunteerReport;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    // Daftar Relawan
    public function index()
    {
        // Ambil semua data relawan beserta info user dan bencana yang ditugaskan secara terbaru
        $volunteers = Volunteer::with(['user', 'disaster'])->latest()->get();
        return view('admin.volunteer.index', compact('volunteers'));
    }

    // Detail Relawan
    public function show($id)
    {
        // Cari detail relawan beserta relasinya berdasarkan ID
        $volunteer = Volunteer::with(['user', 'disaster'])->findOrFail($id);
        return view('admin.volunteer.show', compact('volunteer'));
    }

    // Laporan Tugas Relawan
    public function reports(Request $request)
    {
        // Ambil laporan relawan beserta relasi relawan dan bencana secara terbaru
        $query = VolunteerReport::with(['volunteer', 'disaster'])->latest();

        // Jika filter keahlian dipilih, saring laporan berdasarkan keahlian
        if ($request->filled('skill')) {
            $query->where('skill_type', $request->skill);
        }
        // Jika filter bencana dipilih, saring laporan berdasarkan ID bencana
        if ($request->filled('disaster_id')) {
            $query->where('disaster_id', $request->disaster_id);
        }
        // Jika filter relawan tertentu dipilih, saring laporan berdasarkan relawan
        if ($request->filled('volunteer_id')) {
            $query->where('volunteer_id', $request->volunteer_id);
        }

        $reports = $query->get();

        $skills = Volunteer::getSkillOptions();
        // Ambil daftar bencana aktif untuk dropdown filter
        $disasters = Disaster::whereNotIn('status', ['PENDING', 'DECLINE'])
            ->latest()
            ->get(['id', 'title', 'location']);
        // Ambil daftar semua relawan yang disetujui untuk dropdown filter
        $volunteers = Volunteer::where('status', Volunteer::STATUS_APPROVED)
            ->orderBy('name')
            ->get(['id', 'name', 'skill']);

        return view('admin.volunteer.reports', compact('reports', 'skills', 'disasters', 'volunteers'));
    }

    // Update Status Dan Penugasan
    public function updateStatus(Request $request, $id)
    {
        // Validasi status relawan baru yang akan diperbarui
        $request->validate([
            'status' => 'required|in:PENDING,APPROVED,REJECTED,FIRED',
        ]);

        // Cari data relawan berdasarkan ID
        $volunteer = Volunteer::findOrFail($id);
        $volunteer->update(['status' => $request->status]);

        // Update role di profiles
        // Jika disetujui, ubah role user lokal menjadi Relawan
        if ($request->status === Volunteer::STATUS_APPROVED) {
            User::where('id', $volunteer->user_id)->update(['role' => 'Relawan']);
        // Jika ditolak, pending, atau diberhentikan, kembalikan role user menjadi Masyarakat
        } elseif (in_array($request->status, [Volunteer::STATUS_REJECTED, Volunteer::STATUS_PENDING, Volunteer::STATUS_FIRED])) {
            User::where('id', $volunteer->user_id)->update(['role' => 'Masyarakat']);
        }

        $msg = $request->status === Volunteer::STATUS_APPROVED ? 'approved' : 'rejected';
        return redirect()->route('volunteer.show', $id)->with('msg', $msg);
    }

    public function assign(Request $request, $id)
    {
        // Validasi data penugasan lokasi dan bencana
        $request->validate([
            'assignment' => 'nullable|string|max:255',
            'disaster_id' => 'nullable|exists:disasters,id',
        ]);

        // Cari data relawan yang akan ditugaskan berdasarkan ID
        $volunteer = Volunteer::findOrFail($id);

        // Tentukan apakah admin sedang menghapus penugasan yang ada
        $isClearing = empty($request->assignment) && empty($request->disaster_id);

        $volunteer->update([
            'assignment' => $request->assignment,
            'disaster_id' => $request->disaster_id,
            'assignment_notified_at' => null,
            'assignment_status' => $isClearing ? null : 'pending',
            'assignment_rejection_reason' => null,
            'assigned_by' => $isClearing ? null : auth()->id(),
        ]);

        $msg = $isClearing ? 'Penugasan berhasil dihapus.' : 'Penugasan dikirim, menunggu konfirmasi relawan.';

        return redirect()->route('volunteer.show', $id)
            ->with('msg', $msg);
    }
}
