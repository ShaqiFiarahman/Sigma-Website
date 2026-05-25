<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shelter;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ShelterController extends Controller
{
    public function create()
    {
        return view('admin.shelter.create');
    }

    public function store(Request $request)
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

        $shelter = Shelter::create($data);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'shelter-' . $shelter->id . '-' . time() . '.' . $file->getClientOriginalExtension();

            $supabaseUrl = rtrim(config('services.supabase.url'), '/');
            $supabaseKey = config('services.supabase.key');
            $bucketName = 'shelters';

            if ($supabaseUrl && $supabaseKey) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $supabaseKey,
                        'Content-Type' => $file->getMimeType(),
                    ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
                      ->post($supabaseUrl . "/storage/v1/object/" . $bucketName . "/" . $filename);

                    if ($response->successful()) {
                        $shelter->update(['photo_url' => $supabaseUrl . "/storage/v1/object/public/" . $bucketName . "/" . $filename]);
                    }
                } catch (\Exception $e) {
                    $path = $file->store('shelters', 'public');
                    $shelter->update(['photo_url' => Storage::url($path)]);
                }
            } else {
                $path = $file->store('shelters', 'public');
                $shelter->update(['photo_url' => Storage::url($path)]);
            }
        }

        return redirect()->route('shelter')->with('msg', 'Posko berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $shelter = Shelter::findOrFail($id);
        $assignedVolunteers = Volunteer::where('status', 'APPROVED')
            ->where('assignment', $shelter->name)
            ->get();
        return view('admin.shelter.edit', compact('shelter', 'assignedVolunteers'));
    }

    public function update(Request $request, $id)
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

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'shelter-' . $shelter->id . '-' . time() . '.' . $file->getClientOriginalExtension();

            $supabaseUrl = rtrim(config('services.supabase.url'), '/');
            $supabaseKey = config('services.supabase.key');
            $bucketName = 'shelters';

            if ($supabaseUrl && $supabaseKey) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $supabaseKey,
                        'Content-Type' => $file->getMimeType(),
                    ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
                      ->post($supabaseUrl . "/storage/v1/object/" . $bucketName . "/" . $filename);

                    if ($response->successful()) {
                        $data['photo_url'] = $supabaseUrl . "/storage/v1/object/public/" . $bucketName . "/" . $filename;
                    }
                } catch (\Exception $e) {
                    $path = $file->store('shelters', 'public');
                    $data['photo_url'] = Storage::url($path);
                }
            } else {
                $path = $file->store('shelters', 'public');
                $data['photo_url'] = Storage::url($path);
            }
        }

        $shelter->update($data);

        return redirect()->route('shelter')->with('msg', 'Posko berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $shelter = Shelter::findOrFail($id);
        $shelter->delete();
        return redirect()->route('shelter')->with('msg', 'Posko berhasil dihapus.');
    }
}
