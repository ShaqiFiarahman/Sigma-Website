<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShelterRequest;
use App\Models\Shelter;
use App\Models\Volunteer;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;

class ShelterController extends Controller
{
    public function __construct(
        private ImageUploadService $imageUpload,
    ) {}

    public function create()
    {
        return view('admin.shelter.create');
    }

    public function store(StoreShelterRequest $request)
    {
        $data = $this->buildShelterData($request);

        $shelter = Shelter::create($data);

        if ($request->hasFile('photo')) {
            $photoUrl = $this->uploadShelterPhoto($request->file('photo'), $shelter->id);
            $shelter->update(['photo_url' => $photoUrl]);
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

    public function update(StoreShelterRequest $request, $id)
    {
        $shelter = Shelter::findOrFail($id);
        $data = $this->buildShelterData($request);

        if ($request->hasFile('photo')) {
            $data['photo_url'] = $this->uploadShelterPhoto($request->file('photo'), $shelter->id);
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

    /**
     * Build the shelter data array from the request.
     */
    private function buildShelterData(StoreShelterRequest $request): array
    {
        return [
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
    }

    /**
     * Upload a shelter photo using the ImageUploadService.
     */
    private function uploadShelterPhoto(UploadedFile $file, int $shelterId): string
    {
        return $this->imageUpload->upload(
            file: $file,
            bucket: 'shelters',
        );
    }
}
