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

    // Manajemen Posko
    public function create()
    {
        return view('admin.shelter.create');
    }

    public function store(StoreShelterRequest $request)
    {
        $data = $this->buildShelterData($request);

        // Simpan data posko baru ke database
        $shelter = Shelter::create($data);

        // Jika file foto diunggah, simpan foto posko ke storage
        if ($request->hasFile('photo')) {
            $photoUrl = $this->uploadShelterPhoto($request->file('photo'));
            $shelter->update(['photo_url' => $photoUrl]);
        }

        return redirect()->route('shelter')->with('msg', 'Posko berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Cari data posko pengungsian berdasarkan ID
        $shelter = Shelter::findOrFail($id);
        // Cari daftar relawan aktif yang ditugaskan ke posko pengungsian ini
        $assignedVolunteers = Volunteer::where('status', 'APPROVED')
            ->where('assignment', $shelter->name)
            ->get();
        return view('admin.shelter.edit', compact('shelter', 'assignedVolunteers'));
    }

    public function update(StoreShelterRequest $request, $id)
    {
        // Cari data posko pengungsian yang akan diperbarui
        $shelter = Shelter::findOrFail($id);
        $data = $this->buildShelterData($request);

        // Jika ada upload foto baru, ganti foto lama dengan mengunggah foto baru ke storage
        if ($request->hasFile('photo')) {
            $data['photo_url'] = $this->uploadShelterPhoto($request->file('photo'));
        }

        $shelter->update($data);

        return redirect()->route('shelter')->with('msg', 'Posko berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Cari data posko pengungsian yang akan dihapus
        $shelter = Shelter::findOrFail($id);
        $shelter->delete();
        return redirect()->route('shelter')->with('msg', 'Posko berhasil dihapus.');
    }

    // Fungsi Bantuan
    private function buildShelterData(StoreShelterRequest $request): array
    {
        return [
            'name' => $request->name,
            'address' => $request->address,
            'capacity_current' => $request->capacity_current,
            'capacity_max' => $request->capacity_max,
            'status' => $request->status,
            // Pecah logistik yang berbentuk string koma menjadi array yang dibersihkan spasinya
            'logistics' => $request->logistics ? array_map('trim', explode(',', $request->logistics)) : [],
            'contact_phone' => $request->contact_phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];
    }

    private function uploadShelterPhoto(UploadedFile $file): string
    {
        return $this->imageUpload->upload(
            file: $file,
            bucket: 'shelters',
        );
    }
}
