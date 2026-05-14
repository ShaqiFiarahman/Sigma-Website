<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LaporanController extends Controller
{
    private function getInitLaporans() {
        $yesterday = date('d M Y', strtotime("-1 days"));
        $twoDaysAgo = date('d M Y', strtotime("-2 days"));
        
        return [
            ['id' => 2, 'judul' => 'Pohon tumbang menutup jalan provinsi', 'lokasi' => 'Bantul', 'tanggal' => $yesterday, 'tingkat_bencana' => 'Waspada', 'status' => 'Verified', 'deskripsi' => 'Pohon beringin besar tumbang akibat angin kencang. Menutupi seluruh ruas jalan provinsi dan menyebabkan kemacetan total sepanjang 5 km.'],
            ['id' => 3, 'judul' => 'Tanah longsor di lereng gunung', 'lokasi' => 'Sleman', 'tanggal' => $twoDaysAgo, 'tingkat_bencana' => null, 'status' => 'Decline', 'deskripsi' => 'Longsor terjadi setelah hujan deras berturut-turut selama 2 hari. Beberapa rumah warga rusak berat.'],
        ];
    }

    public function dashboard()
    {
        $user = auth()->user();
        $role = $user?->role ?? 'masyarakat';

        // Jika Admin/BNPB, tampilkan dashboard admin (dengan chart & map)
        if (in_array($role, ['admin', 'BNPB'])) {
            if (!session()->has('laporans')) {
                session()->put('laporans', $this->getInitLaporans());
            }
            
            $laporans = session('laporans');
            $total = count($laporans);
            $pending = collect($laporans)->where('status', 'Pending')->count();
            $selesai = collect($laporans)->where('status', 'Verified')->count();

            $chartLabels = [];
            $chartData = [];
            for ($i = 6; $i >= 0; $i--) {
                $dateStr = date('d M Y', strtotime("-$i days"));
                $labelStr = date('d M', strtotime("-$i days"));
                $chartLabels[] = $labelStr;
                
                $count = collect($laporans)->filter(function ($laporan) use ($dateStr) {
                    return $laporan['tanggal'] == $dateStr;
                })->count();
                $chartData[] = $count;
            }

            return view('pages.dashboard', compact('total', 'pending', 'selesai', 'chartLabels', 'chartData'));
        }

        // Jika Masyarakat/Relawan, tampilkan dashboard user (sesuai Android)
        $news = $this->getDashboardNews();
        $menu = $this->getDashboardMenu($role);

        return view('pages.dashboard-user', compact('user', 'news', 'menu'));
    }

    private function getDashboardNews()
    {
        return [
            ['id' => 1, 'title' => 'Banjir bandang melanda wilayah Sukoharjo',                                'time' => '10 menit lalu', 'category' => 'INFO',    'tone' => 'info'],
            ['id' => 2, 'title' => 'Gempa bumi M 5,0 SR di Ternate, Maluku Utara',                            'time' => '3 jam lalu',    'category' => 'DARURAT', 'tone' => 'danger'],
            ['id' => 3, 'title' => 'Prakiraan cuaca: Hujan lebat esok hari di Soloraya',                      'time' => '1 jam lalu',    'category' => 'WASPADA', 'tone' => 'warning'],
            ['id' => 4, 'title' => 'Penyaluran bantuan logistik terkendala jembatan terputus',                'time' => '2 jam lalu',    'category' => 'INFO',    'tone' => 'info'],
        ];
    }

    private function getDashboardMenu($role)
    {
        $baseMenu = [
            ['id' => 1,  'title' => 'Peta Bencana',        'description' => 'Zona bahaya',       'icon' => 'bi-map-fill'],
            ['id' => 2,  'title' => 'Lapor Bencana',       'description' => 'Kirim laporan',     'icon' => 'bi-megaphone-fill'],
            ['id' => 3,  'title' => 'Info Posko',          'description' => 'Titik pengungsian', 'icon' => 'bi-house-heart-fill'],
            ['id' => 10, 'title' => 'Panduan Bencana',     'description' => 'Tips mitigasi',     'icon' => 'bi-book-fill'],
            ['id' => 5,  'title' => 'Registrasi Relawan',  'description' => 'Daftar relawan',    'icon' => 'bi-person-plus-fill'],
            ['id' => 7,  'title' => 'Cari Bencana',        'description' => 'Pencarian & filter','icon' => 'bi-search'],
        ];

        if ($role === 'BNPB') {
            $baseMenu[] = ['id' => 6, 'title' => 'Verifikasi Laporan', 'description' => 'Validasi data', 'icon' => 'bi-shield-check'];
        }

        return $baseMenu;
    }

    public function index()
    {
        if (!session()->has('laporans')) {
            session()->put('laporans', $this->getInitLaporans());
        }
        
        $laporans = collect(session('laporans'))->sortByDesc('id')->values()->all();
        return view('laporan.index', compact('laporans'));
    }

    public function create()
    {
        return view('laporan.create');
    }

    public function store(Request $request)
    {
        if (!session()->has('laporans')) session()->put('laporans', $this->getInitLaporans());
        $laporans = session('laporans');
        
        $newId = count($laporans) > 0 ? max(array_column($laporans, 'id')) + 1 : 1;
        
        $laporans[] = [
            'id' => $newId,
            'judul' => $request->judul,
            'lokasi' => $request->lokasi,
            'tanggal' => date('d M Y'),
            'tingkat_bencana' => null,
            'status' => 'Pending',
            'deskripsi' => $request->deskripsi
        ];
        
        session()->put('laporans', $laporans);
        return redirect()->route('laporan.index')->with('msg', 'created');
    }

    public function show($id)
    {
        $laporans = collect(session('laporans', $this->getInitLaporans()));
        $laporan = $laporans->firstWhere('id', (int)$id);
        
        if (!$laporan) abort(404);
        
        return view('laporan.detail', compact('laporan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $laporans = session('laporans', $this->getInitLaporans());
        $msg = 'approved';
        
        foreach ($laporans as &$l) {
            if ($l['id'] == (int)$id) {
                $l['status'] = $request->status; // 'Verified' or 'Decline'
                if ($request->status == 'Verified') {
                    $l['tingkat_bencana'] = $request->tingkat_bencana;
                } elseif ($request->status == 'Decline') {
                    $msg = 'rejected';
                    $l['tingkat_bencana'] = null;
                }
                break;
            }
        }
        
        session()->put('laporans', $laporans);
        return redirect()->route('laporan.index')->with('msg', $msg);
    }
}
