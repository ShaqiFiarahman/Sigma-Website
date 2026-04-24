<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Helper to initialize session data
function getInitLaporans() {
    return [
        ['id' => 1, 'judul' => 'Banjir bandang di pemukiman warga', 'lokasi' => 'Sukoharjo', 'tanggal' => '15 Apr 2026', 'tingkat_bencana' => 'Awas', 'status' => 'Pending', 'deskripsi' => 'Telah terjadi banjir bandang yang merendam lebih dari 50 rumah warga di sekitar bantaran sungai. Air mulai naik sejak dini hari setelah hujan lebat yang tidak kunjung reda dari semalam. Saat ini warga banyak yang terjebak di atap rumah dan membutuhkan bantuan evakuasi, perahu karet, tenda darurat, dan logistik secepatnya. Aliran listrik terputus total sejak jam 3 pagi dan stok makanan mulai menipis. Harap tim segera meluncur sebelum air semakin tinggi karena cuaca masih mendung gelap.'],
        ['id' => 2, 'judul' => 'Pohon tumbang menutup jalan provinsi', 'lokasi' => 'Bantul', 'tanggal' => '14 Apr 2026', 'tingkat_bencana' => 'Waspada', 'status' => 'Verified', 'deskripsi' => 'Pohon beringin besar tumbang akibat angin kencang. Menutupi seluruh ruas jalan provinsi dan menyebabkan kemacetan total sepanjang 5 km.'],
        ['id' => 3, 'judul' => 'Tanah longsor di lereng gunung', 'lokasi' => 'Sleman', 'tanggal' => '12 Apr 2026', 'tingkat_bencana' => 'Siaga 1', 'status' => 'Danger', 'deskripsi' => 'Longsor terjadi setelah hujan deras berturut-turut selama 2 hari. Beberapa rumah warga rusak berat.'],
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

    return view('dashboard', compact('total', 'pending', 'selesai'));
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
        'tingkat_bencana' => $request->tingkat_bencana,
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
            $l['status'] = $request->status; // 'Verified' or 'Danger'
            if ($request->status == 'Danger') {
                $msg = 'rejected';
            }
            break;
        }
    }
    
    session()->put('laporans', $laporans);
    return redirect()->route('laporan')->with('msg', $msg);
})->name('laporan.update_status');

