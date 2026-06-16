<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLaporanRequest;
use App\Models\Disaster;
use App\Models\VolunteerReport;
use App\Services\GeocodingService;
use App\Services\ImageUploadService;

class LaporanController extends Controller
{
    public function __construct(
        private GeocodingService $geocoding,
        private ImageUploadService $imageUpload,
    ) {}

    // Buat Laporan
    public function create()
    {
        // Ambil riwayat laporan bencana terakhir milik user jika sudah login
        $riwayat = auth()->check()
            ? Disaster::where('user_id', auth()->id())->latest()->limit(5)->get()
            : collect();

        return view('laporan.create', compact('riwayat'));
    }

    // Simpan Laporan
    public function store(StoreLaporanRequest $request)
    {
        $user = auth()->user();

        // Dapatkan nama lokasi berdasarkan koordinat
        $locationName = $this->geocoding->reverseGeocode(
            (float) $request->latitude,
            (float) $request->longitude
        );

        $photoUrls = $this->uploadPhotos($request);

        // Simpan data laporan bencana baru ke database
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

    // Tampilkan Laporan
    public function show($id)
    {
        // Cari data bencana beserta relasi user pembuat laporan berdasarkan ID
        $disaster = Disaster::with('user')->findOrFail($id);
        $laporan = $this->toArray($disaster);

        // Ambil laporan medis terakhir dari relawan di lokasi bencana
        $latestMedis = VolunteerReport::where('disaster_id', $id)
            ->where('skill_type', 'MEDIS')
            ->latest()
            ->first();

        // Ambil laporan SAR terakhir dari relawan di lokasi bencana
        $latestSar = VolunteerReport::where('disaster_id', $id)
            ->where('skill_type', 'SAR')
            ->latest()
            ->first();

        return view('laporan.show', compact('laporan', 'latestMedis', 'latestSar'));
    }

    // Fungsi Upload
    private function uploadPhotos(StoreLaporanRequest $request): array
    {
        $photoUrls = [];

        // Jika ada file foto yang diunggah, proses masing-masing file
        if ($request->hasFile('foto')) {
            // Iterasi dan upload setiap file foto ke Supabase Storage
            foreach ($request->file('foto') as $file) {
                $photoUrls[] = $this->imageUpload->upload(
                    file: $file,
                    bucket: config('services.supabase.bucket', 'laporan'),
                );
            }
        }

        return $photoUrls;
    }

    private function toArray(Disaster $d): array
    {
        // Tentukan teks lokasi berdasarkan ketersediaan koordinat dan nama tempat
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
}
