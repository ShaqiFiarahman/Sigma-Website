<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Helper to initialize session data
function getInitLaporans() {
    $yesterday = date('d M Y', strtotime("-1 days"));
    $twoDaysAgo = date('d M Y', strtotime("-2 days"));
    
    return [
        ['id' => 2, 'judul' => 'Pohon tumbang menutup jalan provinsi', 'lokasi' => 'Bantul', 'tanggal' => $yesterday, 'tingkat_bencana' => 'Waspada', 'status' => 'Verified', 'deskripsi' => 'Pohon beringin besar tumbang akibat angin kencang. Menutupi seluruh ruas jalan provinsi dan menyebabkan kemacetan total sepanjang 5 km.'],
        ['id' => 3, 'judul' => 'Tanah longsor di lereng gunung', 'lokasi' => 'Sleman', 'tanggal' => $twoDaysAgo, 'tingkat_bencana' => null, 'status' => 'Decline', 'deskripsi' => 'Longsor terjadi setelah hujan deras berturut-turut selama 2 hari. Beberapa rumah warga rusak berat.'],
    ];
}

Route::get('/', function () {
    return view('login');
})->name('login');

Route::get('/dashboard', function () {
    if (!session()->has('laporans')) {
        session()->put('laporans', getInitLaporans());
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

    return view('dashboard', compact('total', 'pending', 'selesai', 'chartLabels', 'chartData'));
})->name('dashboard');

Route::get('/laporan', function () {
    if (!session()->has('laporans')) {
        session()->put('laporans', getInitLaporans());
    }
    
    $laporans = collect(session('laporans'))->sortByDesc('id')->values()->all();
    return view('laporan', compact('laporans'));
})->name('laporan');

Route::get('/laporan/create', function () {
    return view('create');
})->name('create');

Route::post('/laporan/store', function (Request $request) {
    if (!session()->has('laporans')) session()->put('laporans', getInitLaporans());
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
    return redirect()->route('laporan')->with('msg', 'created');
})->name('laporan.store');

Route::get('/laporan/detail/{id}', function ($id) {
    $laporans = collect(session('laporans', getInitLaporans()));
    $laporan = $laporans->firstWhere('id', (int)$id);
    
    if (!$laporan) abort(404);
    
    return view('detail', compact('laporan'));
})->name('detail');

Route::post('/laporan/update-status/{id}', function (Request $request, $id) {
    $laporans = session('laporans', getInitLaporans());
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
    return redirect()->route('laporan')->with('msg', $msg);
})->name('laporan.update_status');

