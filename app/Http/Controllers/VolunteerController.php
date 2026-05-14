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
        return view('volunteer.register', compact('skills'));
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
            ->with('msg', 'Pendaftaran relawan berhasil. Menunggu verifikasi BNPB.');
    }

    /**
     * Show volunteer list (admin/BNPB only)
     */
    public function index()
    {
        $volunteers = Volunteer::with('user')
            ->latest()
            ->paginate(15);

        return view('volunteer.index', compact('volunteers'));
    }

    /**
     * Show volunteer detail
     */
    public function show($id)
    {
        $volunteer = Volunteer::with('user')->findOrFail($id);
        return view('volunteer.show', compact('volunteer'));
    }

    /**
     * Update volunteer status (admin/BNPB only)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,APPROVED,REJECTED',
        ]);

        $volunteer = Volunteer::findOrFail($id);
        $volunteer->update(['status' => $request->status]);

        $msg = $request->status === Volunteer::STATUS_APPROVED ? 'approved' : 'rejected';
        return redirect()->route('volunteer.show', $id)->with('msg', $msg);
    }

    /**
     * Assign volunteer to location (admin/BNPB only)
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'assignment' => 'required|string|max:255',
        ]);

        $volunteer = Volunteer::findOrFail($id);
        $volunteer->update(['assignment' => $request->assignment]);

        return redirect()->route('volunteer.show', $id)
            ->with('msg', 'Penugasan berhasil diperbarui.');
    }
}
