<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use App\Models\Volunteer;
use App\Models\VolunteerReport;
use Illuminate\Http\Request;

class VolunteerReportController extends Controller
{
    /**
     * Show form to create a new volunteer report
     */
    public function create()
    {
        $user = auth()->user();
        $volunteer = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->first();

        if (!$volunteer) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda belum disetujui sebagai relawan.');
        }

        $fields = VolunteerReport::getFieldsForSkill($volunteer->skill);
        $disasters = Disaster::whereNotIn('status', ['DECLINE', 'RESOLVED'])
            ->latest()
            ->get(['id', 'title', 'location']);

        return view('volunteer.report-create', compact('volunteer', 'fields', 'disasters'));
    }

    /**
     * Store a new volunteer report
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $volunteer = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        $fields = VolunteerReport::getFieldsForSkill($volunteer->skill);

        // Build validation rules dynamically
        $rules = ['notes' => 'nullable|string|max:1000'];
        $rules['disaster_id'] = 'nullable|exists:disasters,id';
        foreach ($fields as $field) {
            $key = 'data.' . $field['name'];
            if ($field['type'] === 'number') {
                $rules[$key] = 'required|integer|min:0';
            } elseif ($field['type'] === 'textarea') {
                $rules[$key] = 'required|string|max:500';
            } else {
                $rules[$key] = 'required|string|max:255';
            }
        }

        $validated = $request->validate($rules);

        VolunteerReport::create([
            'volunteer_id' => $volunteer->id,
            'disaster_id'  => $request->disaster_id ?: null,
            'skill_type'   => $volunteer->skill,
            'report_data'  => $request->data,
            'notes'        => $request->notes,
        ]);

        return redirect()->route('volunteer.reports')
            ->with('msg', 'Laporan tugas berhasil dikirim.');
    }

    /**
     * Show volunteer's report history
     */
    public function index()
    {
        $user = auth()->user();
        $volunteer = Volunteer::where('user_id', $user->id)->first();

        if (!$volunteer) {
            return redirect()->route('dashboard');
        }

        $reports = VolunteerReport::where('volunteer_id', $volunteer->id)
            ->with('disaster')
            ->latest()
            ->paginate(10);

        $fields = VolunteerReport::getFieldsForSkill($volunteer->skill);

        return view('volunteer.report-history', compact('volunteer', 'reports', 'fields'));
    }
}
