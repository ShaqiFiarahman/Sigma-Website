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
        $disasters = Disaster::whereNotIn('status', ['DECLINE', 'RESOLVED'])
            ->latest()
            ->get(['id', 'title', 'location']);

        return view('volunteer.reports.create', compact('volunteer', 'fields', 'disasters'));
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
            'disaster_id' => 'nullable|exists:disasters,id',
            'photos' => 'nullable|array|max:3',
            'photos.*' => 'image|max:10240',
        ];
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

        $request->validate($rules);

        // Handle photo uploads
        $photoUrls = [];
        if ($request->hasFile('photos')) {
            $supabaseUrl = rtrim(config('services.supabase.url'), '/');
            $supabaseKey = config('services.supabase.key');
            $bucketName = config('services.supabase.bucket', 'laporan');

            foreach ($request->file('photos') as $file) {
                $path = $file->store('volunteer-reports', 'public');
                $absolutePath = storage_path('app/public/' . $path);
                $filename = 'vr_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                if ($supabaseUrl && $supabaseKey) {
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $supabaseKey,
                            'Content-Type' => $file->getMimeType(),
                        ])->withBody(file_get_contents($absolutePath), $file->getMimeType())
                          ->post($supabaseUrl . "/storage/v1/object/" . $bucketName . "/" . $filename);

                        if ($response->successful()) {
                            $photoUrls[] = $supabaseUrl . "/storage/v1/object/public/" . $bucketName . "/" . $filename;
                            @unlink($absolutePath);
                        } else {
                            $photoUrls[] = Storage::url($path);
                        }
                    } catch (\Exception $e) {
                        $photoUrls[] = Storage::url($path);
                    }
                } else {
                    $photoUrls[] = Storage::url($path);
                }
            }
        }

        VolunteerReport::create([
            'volunteer_id' => $volunteer->id,
            'disaster_id'  => $request->disaster_id ?: null,
            'skill_type'   => $volunteer->skill,
            'report_data'  => $request->data,
            'notes'        => $request->notes,
            'photo_urls'   => !empty($photoUrls) ? $photoUrls : null,
        ]);

        return redirect()->route('volunteer.reports')
            ->with('msg', 'Laporan tugas berhasil dikirim.');
    }
}
