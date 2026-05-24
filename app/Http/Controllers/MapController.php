<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use App\Models\Shelter;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Show interactive map with disaster markers
     */
    public function index()
    {
        return view('user.map');
    }

    /**
     * Show search & filter disasters page (sesuai Android SearchDisasterScreen)
     */
    public function search()
    {
        $user = auth()->user();
        $role = $user?->role ?? 'MASYARAKAT';

        $query = Disaster::latest();

        if (strtolower($role) !== 'admin') {
            // Citizens/Volunteers can see public disasters (RESOLVED, SIAGA_1, SIAGA_2, AWAS)
            // AND their own reports (including pending or rejected)
            $query->where(function($q) use ($user) {
                $q->whereIn('status', [
                    'RESOLVED',
                    'SIAGA_1',
                    'SIAGA_2',
                    'AWAS'
                ]);
                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            });
        }

        $disasters = $query->get();

        return view('laporan.search', compact('disasters'));
    }

    /**
     * Show shelter info page (sesuai Android ShelterInfoScreen)
     */
    public function shelterPage()
    {
        $query = Shelter::query();

        // Server-side filter
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('q')) {
            $query->where('name', 'like', '%' . request('q') . '%');
        }
        if (request('sort') === 'terdekat') {
            // Default order, client will sort
        }

        $shelters = $query->paginate(6)->through(fn($s) => $this->shelterToArray($s));
        return view('shelter.index', compact('shelters'));
    }

    /**
     * API: Get disasters as JSON for map markers
     */
    public function disasters()
    {
        $disasters = Disaster::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereIn('status', [
                'SIAGA_1',
                'SIAGA_2',
                'AWAS'
            ])
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->get()
            ->map(fn($d) => [
                'id'            => $d->id,
                'title'         => $d->title,
                'description'   => \Illuminate\Support\Str::limit($d->description, 100),
                'lat'           => $d->latitude,
                'lng'           => $d->longitude,
                'status'        => $d->status,
                'statusLabel'   => $d->status_label,
                'reporter'      => $d->reporter_name,
                'date'          => $d->created_at?->toIso8601String(),
                'type'          => 'disaster',
                'disaster_type' => $d->disaster_type,
                'type_icon'     => $d->type_icon,
                'type_color'    => $d->type_color,
                'type_name'     => $d->type_name,
                'photo'         => $d->photo_url ? (json_decode($d->photo_url)[0] ?? null) : null,
            ]);

        return response()->json($disasters);
    }

    /**
     * API: Get shelters as JSON for map markers
     */
    public function shelters()
    {
        $shelters = Shelter::all()->map(fn($s) => $this->shelterToArray($s))->toArray();
        return response()->json($shelters);
    }

    /**
     * Show edit shelter form (admin)
     */
    public function editShelter($id)
    {
        $shelter = Shelter::findOrFail($id);
        $assignedVolunteers = \App\Models\Volunteer::where('status', 'APPROVED')
            ->where('assignment', $shelter->name)
            ->get();
        return view('admin.shelter-edit', compact('shelter', 'assignedVolunteers'));
    }

    /**
     * Update shelter (admin)
     */
    public function updateShelter(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'capacity_current' => 'required|integer|min:0',
            'capacity_max' => 'required|integer|min:1',
            'status' => 'required|in:Tersedia,Penuh',
            'logistics' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:5120',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $shelter = Shelter::findOrFail($id);

        $data = [
            'name' => $request->name,
            'address' => $request->address,
            'capacity_current' => $request->capacity_current,
            'capacity_max' => $request->capacity_max,
            'status' => $request->status,
            'logistics' => $request->logistics ? array_map('trim', explode(',', $request->logistics)) : [],
            'contact_phone' => $request->contact_phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        // Upload foto ke Supabase jika ada
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'shelter-' . $shelter->id . '-' . time() . '.' . $file->getClientOriginalExtension();

            $supabaseUrl = rtrim(config('services.supabase.url'), '/');
            $supabaseKey = config('services.supabase.key');
            $bucketName = 'shelters';

            if ($supabaseUrl && $supabaseKey) {
                try {
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . $supabaseKey,
                        'Content-Type' => $file->getMimeType(),
                    ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
                      ->post($supabaseUrl . "/storage/v1/object/" . $bucketName . "/" . $filename);

                    if ($response->successful()) {
                        $data['photo_url'] = $supabaseUrl . "/storage/v1/object/public/" . $bucketName . "/" . $filename;
                    }
                } catch (\Exception $e) {
                    // Fallback: simpan lokal
                    $path = $file->store('shelters', 'public');
                    $data['photo_url'] = \Illuminate\Support\Facades\Storage::url($path);
                }
            } else {
                $path = $file->store('shelters', 'public');
                $data['photo_url'] = \Illuminate\Support\Facades\Storage::url($path);
            }
        }

        $shelter->update($data);

        return redirect()->route('shelter')->with('msg', 'Posko berhasil diperbarui.');
    }

    /**
     * Delete shelter (admin)
     */
    public function deleteShelter($id)
    {
        $shelter = Shelter::findOrFail($id);
        $shelter->delete();
        return redirect()->route('shelter')->with('msg', 'Posko berhasil dihapus.');
    }

    /**
     * Convert Shelter model to array format for views/API
     */
    private function shelterToArray(Shelter $s): array
    {
        return [
            'id'            => $s->id,
            'name'          => $s->name,
            'address'       => $s->address,
            'distance'      => '—', // Will be calculated client-side
            'capacity'      => $s->capacity_label,
            'status'        => $s->status,
            'lat'           => $s->latitude,
            'lng'           => $s->longitude,
            'logistics'     => $s->logistics ?? [],
            'contact_phone' => $s->contact_phone,
            'photo_url'     => $s->photo_url,
        ];
    }
}
