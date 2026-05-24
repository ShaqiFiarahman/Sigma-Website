<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    /**
     * Show registration form
     */
    public function create()
    {
        $skills = Volunteer::getSkillOptions();
        $existing = Volunteer::where('user_id', auth()->id())->first();
        return view('user.register-volunteer', compact('skills', 'existing'));
    }

    /**
     * Store volunteer registration (sesuai Android: name, skill, address, phone_number)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'skill'        => 'required|in:MEDIS,SAR,LOGISTIK,KONSUMSI,PSIKOSOSIAL',
            'address'      => 'required|string',
            'phone_number' => 'required|string|max:20',
        ]);

        $user = auth()->user();

        // Cek duplikat
        $existing = Volunteer::where('user_id', $user->id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah terdaftar sebagai relawan.');
        }

        Volunteer::create([
            'user_id'      => $user->id,
            'name'         => $request->name,
            'skill'        => $request->skill,
            'address'      => $request->address,
            'phone_number' => $request->phone_number,
            'status'       => Volunteer::STATUS_PENDING,
        ]);

        return redirect()->route('dashboard')
            ->with('msg', 'Pendaftaran relawan berhasil. Menunggu verifikasi Admin.');
    }

    /**
     * Show volunteer list (admin only)
     */
    public function index()
    {
        $volunteers = Volunteer::with(['user', 'disaster'])->latest()->get();
        return view('admin.volunteer.index', compact('volunteers'));
    }

    /**
     * Show all volunteer reports (admin only)
     */
    public function reports(Request $request)
    {
        $query = \App\Models\VolunteerReport::with(['volunteer', 'disaster'])->latest();

        // Filter by skill
        if ($request->filled('skill')) {
            $query->where('skill_type', $request->skill);
        }

        // Filter by disaster
        if ($request->filled('disaster_id')) {
            $query->where('disaster_id', $request->disaster_id);
        }

        // Filter by volunteer
        if ($request->filled('volunteer_id')) {
            $query->where('volunteer_id', $request->volunteer_id);
        }

        $reports = $query->get();

        // Data for filters
        $skills = Volunteer::getSkillOptions();
        $disasters = \App\Models\Disaster::whereNotIn('status', ['PENDING', 'DECLINE'])
            ->latest()
            ->get(['id', 'title', 'location']);
        $volunteers = Volunteer::where('status', Volunteer::STATUS_APPROVED)
            ->orderBy('name')
            ->get(['id', 'name', 'skill']);

        return view('admin.volunteer.reports', compact('reports', 'skills', 'disasters', 'volunteers'));
    }

    /**
     * Show volunteer detail
     */
    public function show($id)
    {
        $volunteer = Volunteer::with('user')->findOrFail($id);
        return view('admin.volunteer.show', compact('volunteer'));
    }

    /**
     * Update volunteer status (admin only)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,APPROVED,REJECTED,FIRED',
        ]);

        $volunteer = Volunteer::findOrFail($id);
        $volunteer->update(['status' => $request->status]);

        // Update role di profiles saat approved/rejected/fired
        if ($request->status === Volunteer::STATUS_APPROVED) {
            \App\Models\User::where('id', $volunteer->user_id)
                ->update(['role' => 'Relawan']);
        } elseif ($request->status === Volunteer::STATUS_REJECTED || $request->status === Volunteer::STATUS_PENDING || $request->status === Volunteer::STATUS_FIRED) {
            \App\Models\User::where('id', $volunteer->user_id)
                ->update(['role' => 'Masyarakat']);
        }

        $msg = $request->status === Volunteer::STATUS_APPROVED ? 'approved' : 'rejected';
        return redirect()->route('volunteer.show', $id)->with('msg', $msg);
    }

    /**
     * Assign volunteer to location (admin only)
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'assignment' => 'nullable|string|max:255',
            'disaster_id' => 'nullable|exists:disasters,id',
        ]);

        $volunteer = Volunteer::findOrFail($id);
        $volunteer->update([
            'assignment' => $request->assignment,
            'disaster_id' => $request->disaster_id,
            'assignment_notified_at' => null, // Reset notifikasi agar relawan lihat banner baru
        ]);

        return redirect()->route('volunteer.show', $id)
            ->with('msg', 'Penugasan berhasil diperbarui.');
    }
}
