<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLaporanRequest;
use App\Models\Disaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function create()
    {
        $riwayat = Disaster::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        return view('laporan.create', compact('riwayat'));
    }

    public function store(StoreLaporanRequest $request)
    {
        $user = auth()->user();
        $photoUrls = [];

        // Reverse Geocoding via Nominatim
        $locationName = 'Lokasi tidak diketahui';
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'SigmaApp/1.0'
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $request->latitude,
                'lon' => $request->longitude,
                'zoom' => 18,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $locationName = $data['display_name'] ?? 'Lokasi tidak diketahui';
            }
        } catch (\Exception $e) {
            // Fallback
        }

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $path = $file->store('laporan', 'public');
                $absolutePath = storage_path('app/public/' . $path);
                $this->compressImage($absolutePath, $absolutePath, 60);

                $fileContent = file_get_contents($absolutePath);
                $filename = basename($absolutePath);

                $supabaseUrl = rtrim(config('services.supabase.url'), '/');
                $supabaseKey = config('services.supabase.key');
                $bucketName = config('services.supabase.bucket', 'laporan');

                if ($supabaseUrl && $supabaseKey) {
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $supabaseKey,
                            'Content-Type' => mime_content_type($absolutePath),
                        ])->withBody($fileContent, mime_content_type($absolutePath))
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

        Disaster::create([
            'user_id'       => $user->id,
            'title'         => $request->judul,
            'description'   => $request->deskripsi,
            'photo_url'     => json_encode($photoUrls),
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'location'      => $locationName,
            'status'        => Disaster::STATUS_PENDING,
            'disaster_type' => 'unknown',
            'reporter_name' => $user->full_name ?? $user->email,
        ]);

        return redirect()->route('laporan.index')->with('msg', 'created');
    }

    public function show($id)
    {
        $disaster = Disaster::with('user')->findOrFail($id);
        $laporan = $this->toArray($disaster);

        // Fetch the latest reports
        $latestMedis = \App\Models\VolunteerReport::where('disaster_id', $id)
            ->where('skill_type', 'MEDIS')
            ->latest()
            ->first();

        $latestSar = \App\Models\VolunteerReport::where('disaster_id', $id)
            ->where('skill_type', 'SAR')
            ->latest()
            ->first();

        return view('laporan.show', compact('laporan', 'latestMedis', 'latestSar'));
    }

    private function toArray(Disaster $d): array
    {
        $lokasi = $d->location
            ? $d->location
            : (($d->latitude && $d->longitude)
                ? 'Lat: ' . round($d->latitude, 4) . ', Long: ' . round($d->longitude, 4)
                : 'Lokasi tidak diketahui');

        return [
            'id'              => $d->id,
            'judul'           => $d->title,
            'lokasi'          => $lokasi,
            'location'        => $d->location,
            'tanggal'         => $d->created_at?->format('d M Y') ?? '-',
            'status'          => $d->status_label,
            'status_raw'      => $d->status,
            'tingkat_bencana' => $d->tingkat,
            'deskripsi'       => $d->description,
            'photo_url'       => $d->photo_url,
            'latitude'        => $d->latitude,
            'longitude'       => $d->longitude,
            'reporter_name'   => $d->reporter_name,
            'disaster_type'   => $d->disaster_type,
            'type_icon'       => $d->type_icon,
            'type_color'      => $d->type_color,
            'type_name'       => $d->type_name,
        ];
    }

    private function compressImage($sourceFile, $destinationPath, $quality = 60)
    {
        $info = getimagesize($sourceFile);

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($sourceFile);
            imagejpeg($image, $destinationPath, $quality);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($sourceFile);
            imagepng($image, $destinationPath, 6);
        } elseif ($info['mime'] == 'image/webp') {
            $image = imagecreatefromwebp($sourceFile);
            imagewebp($image, $destinationPath, $quality);
        } else {
            return false;
        }

        imagedestroy($image);
        return true;
    }
}
