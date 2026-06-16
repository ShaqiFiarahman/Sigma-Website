<?php

return [

    // Menu Layanan Dashboard
    // Menu layanan yang ditampilkan di dashboard, dipisahkan per peran.
    // Memindahkan ini ke config membuatnya mudah diubah tanpa menyentuh
    // controller, dan bisa di-cache lewat `php artisan config:cache`.

    // Menu untuk masyarakat umum
    'masyarakat' => [
        ['id' => 2,  'title' => 'Lapor Bencana',   'description' => 'Kirim laporan',          'icon' => 'bi-megaphone-fill'],
        ['id' => 3,  'title' => 'Info Posko',      'description' => 'Titik pengungsian',      'icon' => 'bi-house-heart-fill'],
        ['id' => 10, 'title' => 'Panduan Bencana', 'description' => 'Tips mitigasi',          'icon' => 'bi-book-fill'],
        ['id' => 7,  'title' => 'Cari Bencana',    'description' => 'Pencarian & filter',     'icon' => 'bi-search'],
        ['id' => 5,  'title' => 'Daftar Relawan',  'description' => 'Bergabung jadi relawan', 'icon' => 'bi-person-plus-fill'],
    ],

    // Menu untuk relawan terverifikasi
    'relawan' => [
        ['id' => 12, 'title' => 'Lapor Tugas',     'description' => 'Kirim laporan tugas', 'icon' => 'bi-send-fill'],
        ['id' => 3,  'title' => 'Info Posko',      'description' => 'Titik pengungsian',   'icon' => 'bi-house-heart-fill'],
        ['id' => 10, 'title' => 'Panduan Bencana', 'description' => 'Tips mitigasi',       'icon' => 'bi-book-fill'],
        ['id' => 7,  'title' => 'Cari Bencana',    'description' => 'Pencarian & filter',  'icon' => 'bi-search'],
    ],

];
