<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Disaster;
use App\Models\Volunteer;
use App\Models\VolunteerReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
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

        return view('volunteer.reports.index', compact('volunteer', 'reports', 'fields'));
    }

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
        $disasters = Disaster::whereNotIn('status', ['PENDING', 'DECLINE', 'RESOLVED'])
            ->latest()
            ->get(['id', 'title', 'location']);

        $recentReports = VolunteerReport::where('volunteer_id', $volunteer->id)
            ->with('disaster')
            ->latest()
            ->limit(5)
            ->get();

        return view('volunteer.reports.create', compact('volunteer', 'fields', 'disasters', 'recentReports'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $volunteer = Volunteer::where('user_id', $user->id)
            ->where('status', Volunteer::STATUS_APPROVED)
            ->firstOrFail();

        $fields = VolunteerReport::getFieldsForSkill($volunteer->skill);

        // Build validation rules dynamically
        $rules = [
            'notes' => 'nullable|string|max:1000',
            'disaster_id' => 'required|exists:disasters,id',
        ];
        foreach ($fields as $field) {
            $key = 'data.' . $field['name'];
            $isOptional = isset($field['optional']) && $field['optional'];
            $rulePrefix = $isOptional ? 'nullable' : 'required';

            if ($field['type'] === 'number') {
                $rules[$key] = "$rulePrefix|integer|min:0";
            } elseif ($field['type'] === 'textarea') {
                $rules[$key] = "$rulePrefix|string|max:500";
            } else {
                $rules[$key] = "$rulePrefix|string|max:255";
            }
        }

        $request->validate($rules);

        VolunteerReport::create([
            'volunteer_id' => $volunteer->id,
            'disaster_id'  => $request->disaster_id,
            'skill_type'   => $volunteer->skill,
            'report_data'  => $request->data,
            'notes'        => $request->notes,
            'photo_urls'   => null,
        ]);

        // Sync needs to the assigned shelter's logistics list
        if (!empty($volunteer->assignment)) {
            $shelter = \App\Models\Shelter::where('name', $volunteer->assignment)->first();
            if ($shelter) {
                $needsInput = '';
                if ($volunteer->skill === 'MEDIS' && !empty($request->data['kebutuhan_medis'])) {
                    $needsInput = $request->data['kebutuhan_medis'];
                } elseif ($volunteer->skill === 'LOGISTIK' && !empty($request->data['kebutuhan_mendesak'])) {
                    $needsInput = $request->data['kebutuhan_mendesak'];
                }

                if (!empty($needsInput)) {
                    // Split the comma-separated, semicolon-separated, or newline-separated needs
                    $splitNeeds = preg_split('/[,;\n\r]+/', $needsInput);
                    $currentLogistics = $shelter->logistics ?? [];
                    if (!is_array($currentLogistics)) {
                        $currentLogistics = [];
                    }

                    foreach ($splitNeeds as $needItem) {
                        $trimmed = trim($needItem);
                        if (!empty($trimmed) && !in_array($trimmed, $currentLogistics)) {
                            $currentLogistics[] = $trimmed;
                        }
                    }

                    $shelter->logistics = $currentLogistics;
                    $shelter->save();
                }
            }
        }

        return redirect()->route('volunteer.reports')
            ->with('msg', 'Laporan tugas berhasil dikirim.');
    }
}
