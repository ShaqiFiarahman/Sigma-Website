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
    public function index()
    {
        $volunteers = Volunteer::with(['user', 'disaster'])->latest()->get();
        return view('admin.volunteer.index', compact('volunteers'));
    }

    public function show($id)
    {
        $volunteer = Volunteer::with(['user', 'disaster'])->findOrFail($id);
        return view('admin.volunteer.show', compact('volunteer'));
    }

    public function reports(Request $request)
    {
        $query = VolunteerReport::with(['volunteer', 'disaster'])->latest();

        if ($request->filled('skill')) {
            $query->where('skill_type', $request->skill);
        }
        if ($request->filled('disaster_id')) {
            $query->where('disaster_id', $request->disaster_id);
        }
        if ($request->filled('volunteer_id')) {
            $query->where('volunteer_id', $request->volunteer_id);
        }

        $reports = $query->get();

        $skills = Volunteer::getSkillOptions();
        $disasters = Disaster::whereNotIn('status', ['PENDING', 'DECLINE'])
            ->latest()
            ->get(['id', 'title', 'location']);
        $volunteers = Volunteer::where('status', Volunteer::STATUS_APPROVED)
            ->orderBy('name')
            ->get(['id', 'name', 'skill']);

        return view('admin.volunteer.reports', compact('reports', 'skills', 'disasters', 'volunteers'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,APPROVED,REJECTED,FIRED',
        ]);

        $volunteer = Volunteer::findOrFail($id);
        $volunteer->update(['status' => $request->status]);

        // Update role di profiles
        if ($request->status === Volunteer::STATUS_APPROVED) {
            User::where('id', $volunteer->user_id)->update(['role' => 'Relawan']);
        } elseif (in_array($request->status, [Volunteer::STATUS_REJECTED, Volunteer::STATUS_PENDING, Volunteer::STATUS_FIRED])) {
            User::where('id', $volunteer->user_id)->update(['role' => 'Masyarakat']);
        }

        $msg = $request->status === Volunteer::STATUS_APPROVED ? 'approved' : 'rejected';
        return redirect()->route('volunteer.show', $id)->with('msg', $msg);
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'assignment' => 'nullable|string|max:255',
            'disaster_id' => 'nullable|exists:disasters,id',
        ]);

        $volunteer = Volunteer::findOrFail($id);

        // Jika penugasan dikosongkan, reset semua field terkait
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
