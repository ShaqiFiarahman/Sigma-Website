<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class LaporanController extends Controller
{
    public function adminDashboard()
    {
        // ─── Statistics ───────────────────────────────────
        $total   = Disaster::count();
        $pending = Disaster::where('status', 'PENDING')->count();
        $selesai = Disaster::where('status', 'RESOLVED')->count();
        $decline = Disaster::where('status', 'DECLINE')->count();
        $awas    = Disaster::where('status', 'AWAS')->count();
        $siaga1  = Disaster::where('status', 'SIAGA_1')->count();
        $siaga2  = Disaster::where('status', 'SIAGA_2')->count();

        // ─── Volunteer Stats ─────────────────────────────
        $totalVolunteers    = Volunteer::count();
        $approvedVolunteers = Volunteer::where('status', Volunteer::STATUS_APPROVED)->count();
        $pendingVolunteers  = Volunteer::where('status', Volunteer::STATUS_PENDING)->count();

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
                                ->whereNotIn('status', ['PENDING', 'DECLINE'])
                                ->count();
            $chartPending[]  = Disaster::whereDate('created_at', $date->toDateString())
                                ->where('status', 'PENDING')
                                ->count();
        }

        // ─── Recent Pending Reports (5 terbaru) ─────────
        $recentPending = Disaster::with('user')
            ->where('status', 'PENDING')
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
                'type'      => $d->disaster_type,
                'type_icon' => $d->type_icon,
                'type_color'=> $d->type_color,
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
        $role = $user?->role ?? 'Masyarakat';

        $news = $this->getDashboardNews();
        $menu = $this->getDashboardMenu($role);

        // Data relawan (pending/rejected/approved)
        $volunteerData = Volunteer::where('user_id', $user->id)->first();

        // Data tambahan khusus relawan approved (untuk section dashboard relawan)
        $volunteerDashboard = null;
        if ($volunteerData && $volunteerData->status === Volunteer::STATUS_APPROVED) {
            // Redirect ke dashboard relawan terpisah
            return view('volunteer.dashboard', [
                'user' => $user,
                'volunteer' => $volunteerData,
                'news' => $news,
            ]);
        }

        return view('user.dashboard', compact('user', 'news', 'menu', 'volunteerData', 'volunteerDashboard'));
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
            'disaster_type' => 'unknown',
            'reporter_name' => $user->full_name ?? $user->email,
        ]);

        return redirect()->route('laporan.index')->with('msg', 'created');
    }

    public function show($id)
    {
        $disaster = Disaster::with('user')->findOrFail($id);
        $laporan  = $this->toArray($disaster);

        return view('laporan.show', compact('laporan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $disaster = Disaster::findOrFail($id);

        $request->validate([
            'disaster_type' => 'required|string|in:flood,fire,earthquake,landslide,tsunami,storm,volcano,unknown',
            'status'        => 'required|string|in:PENDING,AWAS,SIAGA_1,SIAGA_2,RESOLVED,DECLINE',
        ]);

        $disaster->update([
            'disaster_type' => $request->disaster_type,
            'status'        => $request->status,
        ]);

        // Jika bencana selesai (RESOLVED) atau ditolak (DECLINE),
        // otomatis hapus penugasan semua relawan yang ditugaskan pada bencana ini
        if (in_array($request->status, ['RESOLVED', 'DECLINE'])) {
            Volunteer::where('disaster_id', $disaster->id)
                ->update([
                    'disaster_id'            => null,
                    'assignment'             => null,
                    'assignment_notified_at' => null,
                ]);
        }

        $msg = $request->status === 'DECLINE' ? 'rejected' : 'approved';

        return redirect()->route('laporan.show', $id)->with('msg', $msg);
    }

    public function updateType(Request $request, $id)
    {
        $request->validate([
            'disaster_type' => 'required|string|in:flood,fire,earthquake,landslide,tsunami,storm,volcano,unknown',
        ]);

        $disaster = Disaster::findOrFail($id);
        $disaster->update(['disaster_type' => $request->disaster_type]);

        return response()->json(['success' => true]);
    }

    public function adminLaporan()
    {
        $stats = [
            'active' => Disaster::whereIn('status', [Disaster::STATUS_AWAS, Disaster::STATUS_SIAGA_1, Disaster::STATUS_SIAGA_2])->count(),
            'need_verify' => Disaster::where('status', Disaster::STATUS_PENDING)->count(),
        ];

        // Exclude RESOLVED and DECLINE by default, paginate 20 per page
        $disasters = Disaster::whereNotIn('status', [Disaster::STATUS_RESOLVED, Disaster::STATUS_DECLINE])
            ->latest()
            ->get();

        return view('admin.laporan', compact('stats', 'disasters'));
    }

    public function adminLaporanDetail($id)
    {
        $disaster = Disaster::with('user')->findOrFail($id);

        return response()->json([
            'id' => $disaster->id,
            'title' => $disaster->title,
            'description' => $disaster->description,
            'photo_url' => $disaster->photo_url,
            'latitude' => $disaster->latitude,
            'longitude' => $disaster->longitude,
            'location' => $disaster->location,
            'status' => $disaster->status,
            'status_label' => $disaster->status_label,
            'disaster_type' => $disaster->disaster_type ?? 'unknown',
            'type_name' => $disaster->type_name,
            'reporter_name' => $disaster->reporter_name,
            'created_at' => $disaster->created_at->format('d M Y, H:i'),
            'time_ago' => $disaster->created_at->diffForHumans(),
        ]);
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
        // Menu khusus Relawan (approved)
        if (strtolower($role) === 'relawan') {
            return [
                ['id' => 12, 'title' => 'Lapor Tugas',       'description' => 'Kirim laporan tugas',  'icon' => 'bi-send-fill'],
                ['id' => 3,  'title' => 'Info Posko',        'description' => 'Titik pengungsian',    'icon' => 'bi-house-heart-fill'],
                ['id' => 10, 'title' => 'Panduan Bencana',   'description' => 'Tips mitigasi',        'icon' => 'bi-book-fill'],
                ['id' => 7,  'title' => 'Cari Bencana',      'description' => 'Pencarian & filter',   'icon' => 'bi-search'],
            ];
        }

        // Menu dasar untuk Masyarakat
        $baseMenu = [
            ['id' => 2,  'title' => 'Lapor Bencana',      'description' => 'Kirim laporan',      'icon' => 'bi-megaphone-fill'],
            ['id' => 3,  'title' => 'Info Posko',         'description' => 'Titik pengungsian',  'icon' => 'bi-house-heart-fill'],
            ['id' => 10, 'title' => 'Panduan Bencana',    'description' => 'Tips mitigasi',      'icon' => 'bi-book-fill'],
            ['id' => 7,  'title' => 'Cari Bencana',       'description' => 'Pencarian & filter', 'icon' => 'bi-search'],
            ['id' => 5,  'title' => 'Daftar Relawan',     'description' => 'Bergabung jadi relawan', 'icon' => 'bi-person-plus-fill'],
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
