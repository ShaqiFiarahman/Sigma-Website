<?php

namespace App\Http\Controllers;

use App\Models\Disaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    // ─────────────────────────────────────────────
    //  DASHBOARD
    // ─────────────────────────────────────────────

    public function adminDashboard()
    {
        $total   = Disaster::count();
        $pending = Disaster::where('status', Disaster::STATUS_PENDING)->count();
        $selesai = Disaster::where('status', Disaster::STATUS_RESOLVED)->count();

        // Chart: 7 hari terakhir
        $chartLabels = [];
        $chartData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date          = now()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartData[]   = Disaster::whereDate('created_at', $date->toDateString())->count();
        }

        return view('admin.dashboard', compact('total', 'pending', 'selesai', 'chartLabels', 'chartData'));
    }

    public function userDashboard()
    {
        $user = auth()->user();
        $role = $user?->role ?? 'MASYARAKAT';

        $news = $this->getDashboardNews();
        $menu = $this->getDashboardMenu($role);

        return view('user.dashboard', compact('user', 'news', 'menu'));
    }

    // ─────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────

    public function index()
    {
        $user  = auth()->user();
        $role  = $user?->role ?? 'MASYARAKAT';

        // Admin & BNPB lihat semua laporan, user biasa lihat laporan sendiri
        $query = Disaster::with('user')->latest();
        if (!in_array($role, ['admin', 'BNPB'])) {
            $query->where('user_id', $user->id);
        }

        $laporans = $query->get()->map(fn($d) => $this->toArray($d));

        return view('laporan.index', compact('laporans'));
    }

    // ─────────────────────────────────────────────
    //  CREATE
    // ─────────────────────────────────────────────

    public function create()
    {
        $riwayat = Disaster::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        return view('laporan.create', compact('riwayat'));
    }

    // ─────────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'lokasi'    => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto'      => 'nullable|image|max:5120', // maks 5 MB
        ]);

        $user     = auth()->user();
        $photoUrl = null;

        // Simpan foto ke storage/public/laporan
        if ($request->hasFile('foto')) {
            $path     = $request->file('foto')->store('laporan', 'public');
            $photoUrl = Storage::url($path);
        }

        Disaster::create([
            'user_id'       => $user->id,
            'title'         => $request->judul,
            'description'   => $request->deskripsi,
            'photo_url'     => $photoUrl,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'status'        => Disaster::STATUS_PENDING,
            'reporter_name' => $user->full_name ?? $user->email,
        ]);

        return redirect()->route('laporan.index')->with('msg', 'created');
    }

    // ─────────────────────────────────────────────
    //  SHOW
    // ─────────────────────────────────────────────

    public function show($id)
    {
        $disaster = Disaster::with('user')->findOrFail($id);
        $laporan  = $this->toArray($disaster);

        return view('laporan.detail', compact('laporan'));
    }

    // ─────────────────────────────────────────────
    //  UPDATE STATUS (Admin / BNPB)
    // ─────────────────────────────────────────────

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

    // ─────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────

    /**
     * Convert Disaster model to array format used by views
     */
    private function toArray(Disaster $d): array
    {
        // Lokasi: gunakan nama lokasi jika ada, fallback ke koordinat
        $lokasi = ($d->latitude && $d->longitude)
            ? 'Lat: ' . round($d->latitude, 4) . ', Long: ' . round($d->longitude, 4)
            : 'Lokasi tidak diketahui';

        return [
            'id'              => $d->id,
            'judul'           => $d->title,
            'lokasi'          => $lokasi,
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

        if ($role === 'BNPB') {
            $baseMenu[] = ['id' => 6, 'title' => 'Verifikasi Laporan', 'description' => 'Validasi data', 'icon' => 'bi-shield-check'];
        }

        return $baseMenu;
    }
}
