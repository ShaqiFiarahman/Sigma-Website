<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class LaporanController extends Controller
{
    public function adminDashboard()
    {
        // ─── Statistics ───────────────────────────────────
        $total   = Disaster::count();
        $pending = Disaster::where('status', Disaster::STATUS_PENDING)->count();
        $selesai = Disaster::where('status', Disaster::STATUS_RESOLVED)->count();
        $decline = Disaster::where('status', Disaster::STATUS_DECLINE)->count();
        $awas    = Disaster::where('status', Disaster::STATUS_AWAS)->count();
        $siaga1  = Disaster::where('status', Disaster::STATUS_SIAGA_1)->count();
        $siaga2  = Disaster::where('status', Disaster::STATUS_SIAGA_2)->count();

        // ─── Volunteer Stats ─────────────────────────────
        $totalVolunteers    = \App\Models\Volunteer::count();
        $approvedVolunteers = \App\Models\Volunteer::where('status', \App\Models\Volunteer::STATUS_APPROVED)->count();
        $pendingVolunteers  = \App\Models\Volunteer::where('status', \App\Models\Volunteer::STATUS_PENDING)->count();

        // ─── Chart: 7 hari terakhir ─────────────────────
        $chartLabels = [];
        $chartData   = [];
        $chartVerified = [];
        $chartPending  = [];
        for ($i = 6; $i >= 0; $i--) {
            $date            = now()->subDays($i);
            $chartLabels[]   = $date->format('d M');
            $chartData[]     = Disaster::whereDate('created_at', $date->toDateString())->count();
            $chartVerified[] = Disaster::whereDate('created_at', $date->toDateString())
                                ->whereNotIn('status', [Disaster::STATUS_PENDING, Disaster::STATUS_DECLINE])
                                ->count();
            $chartPending[]  = Disaster::whereDate('created_at', $date->toDateString())
                                ->where('status', Disaster::STATUS_PENDING)
                                ->count();
        }

        // ─── Recent Pending Reports (5 terbaru) ─────────
        $recentPending = Disaster::with('user')
            ->where('status', Disaster::STATUS_PENDING)
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn(Disaster $d) => $this->toArray($d));

        // ─── Map Data: Semua disasters dengan koordinat ──
        $mapDisasters = Disaster::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get()
            ->map(fn(Disaster $d) => [
                'id'        => $d->id,
                'judul'     => $d->title,
                'lokasi'    => $d->location ?? 'Lokasi tidak diketahui',
                'latitude'  => $d->latitude,
                'longitude' => $d->longitude,
                'status'    => $d->status,
                'status_label' => $d->status_label,
                'tingkat'   => $d->tingkat,
                'tanggal'   => $d->created_at?->format('d M Y') ?? '-',
                'deskripsi' => \Illuminate\Support\Str::limit($d->description, 120),
            ]);

        // ─── All disasters for client-side period filtering ──
        $allDisasters = Disaster::select('id', 'status', 'created_at')
            ->latest()
            ->get()
            ->map(fn($d) => [
                'status' => $d->status,
                'date' => $d->created_at->toISOString(),
            ]);

        return view('admin.dashboard', compact(
            'total', 'pending', 'selesai', 'decline', 'awas', 'siaga1', 'siaga2',
            'totalVolunteers', 'approvedVolunteers', 'pendingVolunteers',
            'chartLabels', 'chartData', 'chartVerified', 'chartPending',
            'recentPending', 'mapDisasters', 'allDisasters'
        ));
    }

    public function userDashboard()
    {
        $user = auth()->user();
        $role = $user?->role ?? 'MASYARAKAT';

        $news = $this->getDashboardNews();
        $menu = $this->getDashboardMenu($role);

        return view('user.dashboard', compact('user', 'news', 'menu'));
    }

    public function index()
    {
        return redirect()->route('search');
    }

    public function create()
    {
        $riwayat = Disaster::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        return view('laporan.create', compact('riwayat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto'      => 'required|array|max:3',
            'foto.*'    => 'image|max:25600', // max 25MB per file
        ]);

        $user     = auth()->user();
        $photoUrls = [];

        // Reverse Geocoding via Nominatim (OpenStreetMap) secara gratis
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
            // Jika gagal, biarkan default
        }

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $path = $file->store('laporan', 'public');
                
                // Kompres gambar setelah disimpan
                $absolutePath = storage_path('app/public/' . $path);
                $this->compressImage($absolutePath, $absolutePath, 60);
                
                // Upload ke Supabase Storage
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
                            // Simpan URL publik Supabase
                            $photoUrls[] = $supabaseUrl . "/storage/v1/object/public/" . $bucketName . "/" . $filename;
                            
                            // Hapus file lokal setelah diupload ke Supabase
                            @unlink($absolutePath);
                        } else {
                            // Jika gagal upload ke Supabase, fallback ke URL lokal
                            $photoUrls[] = Storage::url($path);
                        }
                    } catch (\Exception $e) {
                        // Jika error request, fallback ke URL lokal
                        $photoUrls[] = Storage::url($path);
                    }
                } else {
                    // Jika config Supabase belum ada di .env, gunakan URL lokal
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
            'reporter_name' => $user->full_name ?? $user->email,
        ]);

        return redirect()->route('laporan.index')->with('msg', 'created');
    }

    public function show($id)
    {
        $disaster = Disaster::with('user')->findOrFail($id);
        $laporan  = $this->toArray($disaster);

        return view('laporan.detail', compact('laporan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:DECLINE,RESOLVED,SIAGA_1,SIAGA_2,AWAS',
        ]);

        $disaster = Disaster::findOrFail($id);
        $disaster->update(['status' => $request->status]);

        $msg = $request->status === Disaster::STATUS_DECLINE ? 'rejected' : 'approved';

        // Redirect kembali ke detail laporan dengan flash message
        return redirect()->route('laporan.show', $id)->with('msg', $msg);
    }

    private function toArray(Disaster $d): array
    {
        // Lokasi: gunakan nama lokasi jika ada, fallback ke koordinat
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
            'tingkat_bencana' => $d->tingkat,
            'deskripsi'       => $d->description,
            'photo_url'       => $d->photo_url,
            'latitude'        => $d->latitude,
            'longitude'       => $d->longitude,
            'reporter_name'   => $d->reporter_name,
        ];
    }

    private function getDashboardNews(): array
    {
        return \App\Models\News::latest('published_at')
            ->limit(6)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'time' => $item->published_at->diffForHumans(),
                    'category' => strtoupper($item->source),
                    'tone' => 'info',
                    'image_url' => $item->image_url,
                    'url' => $item->url,
                    'source' => $item->source,
                ];
            })
            ->toArray();
    }

    private function getDashboardMenu(string $role): array
    {
        $baseMenu = [
            ['id' => 1,  'title' => 'Peta Bencana',       'description' => 'Zona bahaya',        'icon' => 'bi-map-fill'],
            ['id' => 2,  'title' => 'Lapor Bencana',      'description' => 'Kirim laporan',      'icon' => 'bi-megaphone-fill'],
            ['id' => 3,  'title' => 'Info Posko',         'description' => 'Titik pengungsian',  'icon' => 'bi-house-heart-fill'],
            ['id' => 10, 'title' => 'Panduan Bencana',    'description' => 'Tips mitigasi',      'icon' => 'bi-book-fill'],
            ['id' => 5,  'title' => 'Registrasi Relawan', 'description' => 'Daftar relawan',     'icon' => 'bi-person-plus-fill'],
            ['id' => 7,  'title' => 'Cari Bencana',       'description' => 'Pencarian & filter', 'icon' => 'bi-search'],
        ];

        return $baseMenu;
    }

    private function compressImage($sourceFile, $destinationPath, $quality = 60)
    {
        $info = getimagesize($sourceFile);
        
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($sourceFile);
            imagejpeg($image, $destinationPath, $quality);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($sourceFile);
            imagepng($image, $destinationPath, 6); // 0-9 scale
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
