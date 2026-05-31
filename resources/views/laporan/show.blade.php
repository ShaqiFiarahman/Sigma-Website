@extends('layouts.app')
@section('title', 'Detail Laporan')
@section('subtitle', 'Pantau detail laporan dan lokasi kejadian.')

@section('page-actions')
    <x-ui.back-button :useHistory="true" />
@endsection

@section('content')

@php
    $s = $laporan['status_raw'];
    $borderColor = match($s) {
        'AWAS'     => 'border-l-red-500',
        'SIAGA_1'  => 'border-l-orange-500',
        'SIAGA_2'  => 'border-l-violet-500',
        'RESOLVED' => 'border-l-emerald-500',
        'DECLINE'  => 'border-l-slate-400',
        default    => 'border-l-amber-400',
    };

    $typeIcon = $laporan['type_icon'];
    $typeColor = $laporan['type_color'];
    $typeName = $laporan['type_name'];
@endphp

{{-- Flash message --}}
@if(session('msg'))
    <div class="mb-5 p-4 rounded-xl flex items-center gap-3 text-sm font-medium
        {{ session('msg') === 'approved' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' }}">
        @if(session('msg') === 'approved')
            <i class="bi bi-check-circle-fill text-emerald-500"></i> Status laporan berhasil diperbarui.
        @else
            <i class="bi bi-x-circle-fill text-red-500"></i> Laporan berhasil ditolak.
        @endif
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100"><i class="bi bi-x-lg"></i></button>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kiri: Detail Utama --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Card Utama --}}
        <div class="bg-white border border-slate-200/80 border-l-4 {{ $borderColor }} rounded-2xl overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

            {{-- Header --}}
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight">{{ $laporan['judul'] }}</h2>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                            <span class="flex items-center gap-1"><i class="bi bi-calendar2-event text-slate-400"></i> {{ $laporan['tanggal'] }}</span>
                            <span class="flex items-center gap-1"><i class="bi bi-person text-slate-400"></i> {{ $laporan['reporter_name'] }}</span>
                        </div>
                    </div>
                    {{-- Status Badge --}}
                    @if($s === 'PENDING')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                            Pending
                        </span>
                    @elseif($s === 'DECLINE')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                            Ditolak
                        </span>
                    @elseif($s === 'AWAS')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-200 shrink-0">
                            Awas
                        </span>
                    @elseif($s === 'SIAGA_1')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200 shrink-0">
                            Siaga 1
                        </span>
                    @elseif($s === 'SIAGA_2')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold bg-violet-50 text-violet-700 border border-violet-200 shrink-0">
                            Siaga 2
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                            Terverifikasi
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">

                {{-- Foto --}}
                @php
                    $photos = [];
                    if (!empty($laporan['photo_url'])) {
                        $decoded = json_decode($laporan['photo_url'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $photos = $decoded;
                        } else {
                            $photos = [$laporan['photo_url']];
                        }
                    }
                @endphp

                @if(count($photos) > 0)
                    <div class="rounded-xl overflow-hidden border border-slate-100" style="box-shadow: 0 2px 8px rgba(10,15,30,0.05);">
                        {{-- Main photo --}}
                        <img src="{{ $photos[0] }}"
                             alt="Dokumentasi Bencana"
                             class="w-full object-cover cursor-pointer"
                             style="max-height: 340px;"
                             loading="lazy"
                             onclick="window.open(this.src, '_blank')">
                        {{-- Thumbnails if multiple --}}
                        @if(count($photos) > 1)
                            <div class="flex gap-2 p-2 bg-slate-50 border-t border-slate-100">
                                @foreach($photos as $i => $photo)
                                    <img src="{{ $photo }}"
                                         alt="Foto {{ $i + 1 }}"
                                         class="w-16 h-16 object-cover rounded-lg border border-slate-200 cursor-pointer {{ $i === 0 ? 'ring-2 ring-blue-400' : '' }}"
                                         loading="lazy"
                                         onclick="this.closest('.rounded-xl').querySelector('img:first-child').src = this.src">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="w-full h-40 flex flex-col items-center justify-center bg-slate-50 text-slate-400 rounded-xl border border-slate-100">
                        <i class="bi bi-image text-3xl mb-2"></i>
                        <p class="text-xs">Tidak ada foto dokumentasi</p>
                    </div>
                @endif

                {{-- Deskripsi --}}
                <div>
                    <h3 class="text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-text-paragraph text-slate-400"></i> Deskripsi
                    </h3>
                    <div class="text-sm text-slate-600 leading-relaxed p-4 bg-slate-50 rounded-xl border border-slate-100">
                        @if($laporan['deskripsi'])
                            <p>{{ $laporan['deskripsi'] }}</p>
                        @else
                            <p class="text-slate-400 italic">Belum ada deskripsi lengkap.</p>
                        @endif
                    </div>
                </div>

                {{-- Lokasi --}}
                <div>
                    <h3 class="text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-geo-alt text-slate-400"></i> Lokasi Kejadian
                    </h3>
                    <div class="p-4 rounded-xl border border-slate-100 bg-slate-50">
                        <p class="text-sm font-semibold text-slate-800">{{ $laporan['location'] ?? $laporan['lokasi'] ?? 'Lokasi tidak diketahui' }}</p>
                        @if($laporan['latitude'] && $laporan['longitude'])
                            <p class="text-[11px] text-slate-400 mt-1 font-mono">{{ round($laporan['latitude'], 6) }}, {{ round($laporan['longitude'], 6) }}</p>
                        @endif
                    </div>
                </div>

                {{-- Data Korban & Evakuasi (Relawan Update) --}}
                <div class="border-t border-slate-100/60 pt-5">
                    <h3 class="text-xs font-bold text-slate-700 mb-3 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-people-fill text-slate-400"></i> Kondisi Korban & Evakuasi <span class="text-[10px] font-bold text-blue-600 ml-1.5">Update Relawan</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Medis Update --}}
                        <div class="bg-red-50/20 border border-red-100 rounded-xl p-4.5">
                            <div class="flex items-center gap-2 mb-3 text-red-700">
                                <i class="bi bi-heart-pulse-fill text-sm"></i>
                                <h4 class="text-xs font-bold uppercase tracking-wider">Kondisi Medis & Korban</h4>
                            </div>
                            @if($latestMedis && !empty($latestMedis->report_data))
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Total Korban</span>
                                        <span class="text-base font-bold text-red-600 mt-0.5">{{ $latestMedis->report_data['total_korban'] ?? 0 }}</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Selamat</span>
                                        <span class="text-base font-bold text-emerald-600 mt-0.5">{{ $latestMedis->report_data['selamat'] ?? 0 }}</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Luka Ringan</span>
                                        <span class="text-base font-bold text-amber-500 mt-0.5">{{ $latestMedis->report_data['luka_ringan'] ?? 0 }}</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Luka Berat</span>
                                        <span class="text-base font-bold text-orange-600 mt-0.5">{{ $latestMedis->report_data['luka_berat'] ?? 0 }}</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Kritis</span>
                                        <span class="text-base font-bold text-purple-600 mt-0.5">{{ $latestMedis->report_data['kritis'] ?? 0 }}</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Meninggal</span>
                                        <span class="text-base font-bold text-slate-800 mt-0.5">{{ $latestMedis->report_data['meninggal'] ?? 0 }}</span>
                                    </div>
                                </div>
                                <p class="text-[9px] text-slate-400 mt-2.5 italic">Dilaporkan oleh: {{ $latestMedis->volunteer->name }} · {{ $latestMedis->created_at->diffForHumans() }}</p>
                            @else
                                <div class="grid grid-cols-2 gap-2 text-xs opacity-50">
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Total Korban</span>
                                        <span class="text-base font-bold text-red-600 mt-0.5">0</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Selamat</span>
                                        <span class="text-base font-bold text-emerald-600 mt-0.5">0</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Luka Ringan</span>
                                        <span class="text-base font-bold text-amber-500 mt-0.5">0</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Luka Berat</span>
                                        <span class="text-base font-bold text-orange-600 mt-0.5">0</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Kritis</span>
                                        <span class="text-base font-bold text-purple-600 mt-0.5">0</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-red-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Meninggal</span>
                                        <span class="text-base font-bold text-slate-800 mt-0.5">0</span>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-450 mt-3.5 italic font-medium">Belum ada update data medis dari relawan.</p>
                            @endif
                        </div>

                        {{-- SAR Update --}}
                        <div class="bg-orange-50/20 border border-orange-100 rounded-xl p-4.5">
                             <div class="flex items-center gap-2 mb-3 text-orange-700">
                                <svg class="w-5.5 h-5.5 shrink-0 shadow-sm rounded-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Basarnas Official Circle Emblem -->
                                    <circle cx="12" cy="12" r="11.5" fill="#facc15" />
                                    <circle cx="12" cy="12" r="11" fill="#0b3c1b" />
                                    <circle cx="12" cy="12" r="8" fill="#facc15" />
                                    <!-- Map of Indonesia -->
                                    <path d="M4.6 11.2c.2-.4.7-.2.9.1.4.5.8 1.1 1.2 1.6.2.3 0 .6-.3.6-.4 0-.8-.5-1.2-1-.3-.4-.6-.9-.4-1.3z" fill="#0b3c1b" />
                                    <path d="M9.4 10c.5-.2 1.1.1 1.3.5.2.4-.1.9-.4 1.1-.3.2-.9-.1-1.1-.4-.2-.4.1-.6.2-.8z" fill="#0b3c1b" />
                                    <path d="M7.4 14.3c.7-.1 1.4 0 2.1.1.2 0 .2.2 0 .2-.7 0-1.4-.1-2.1-.2-.2 0-.2-.1 0-.1z" fill="#0b3c1b" />
                                    <path d="M12.6 10.5c.2-.2.7-.1.6.2 0 .3-.3.4-.3.7s.4.4.3.7c-.1.2-.3.1-.4-.1 0-.2.2-.4.1-.7 0-.3-.3-.3-.3-.6 0-.1 0-.2 0-.2z" fill="#0b3c1b" />
                                    <path d="M16.8 11.8c.3-.3.7-.3 1.1-.2.3.2.7.2 1.1-.1.2-.1.3 0 .2.2-.2.3-.5.5-.9.4-.3 0-.5-.2-.6-.3z" fill="#0b3c1b" />
                                    <!-- Minor Islands -->
                                    <circle cx="10.8" cy="14.3" r="0.3" fill="#0b3c1b" />
                                    <circle cx="11.6" cy="14.3" r="0.3" fill="#0b3c1b" />
                                    <circle cx="12.4" cy="14.2" r="0.3" fill="#0b3c1b" />
                                    <circle cx="13.2" cy="13.8" r="0.3" fill="#0b3c1b" />
                                    <circle cx="14.5" cy="12.8" r="0.3" fill="#0b3c1b" />
                                    <circle cx="15.2" cy="12.0" r="0.3" fill="#0b3c1b" />
                                    <!-- Stars -->
                                    <polygon points="12,2.2 12.2,2.7 12.8,2.7 12.3,3.1 12.5,3.6 12,3.3 11.5,3.6 11.7,3.1 11.2,2.7 11.8,2.7" fill="#facc15" />
                                    <polygon points="9.5,2.6 9.7,3.1 10.3,3.1 9.8,3.5 10,4 9.5,3.7 9,4 9.2,3.5 8.7,3.1 9.3,3.1" fill="#facc15" />
                                    <polygon points="14.5,2.6 14.7,3.1 15.3,3.1 14.8,3.5 15,4 14.5,3.7 14,4 14.2,3.5 13.7,3.1 14.3,3.1" fill="#facc15" />
                                    <polygon points="7.2,3.6 7.4,4.1 8,4.1 7.5,4.5 7.7,5 7.2,4.7 6.7,5 6.9,4.5 6.4,4.1 7,4.1" fill="#facc15" />
                                    <polygon points="16.8,3.6 17,4.1 17.6,4.1 17.1,4.5 17.3,5 16.8,4.7 16.3,5 16.5,4.5 16,4.1 16.6,4.1" fill="#facc15" />
                                    <polygon points="5.2,5.2 5.4,5.7 6,5.7 5.5,6.1 5.7,6.6 5.2,6.3 4.7,6.6 4.9,6.1 4.4,5.7 5,5.7" fill="#facc15" />
                                    <polygon points="18.8,5.2 19,5.7 19.6,5.7 19.1,6.1 19.3,6.6 18.8,6.3 18.3,6.6 18.5,6.1 18,5.7 18.6,5.7" fill="#facc15" />
                                    <!-- Red Text -->
                                    <text x="12" y="8" fill="#dc2626" font-size="2.6" font-weight="900" font-family="system-ui, -apple-system, sans-serif" text-anchor="middle" letter-spacing="0.1">SAR</text>
                                    <text x="12" y="16.8" fill="#dc2626" font-size="1.8" font-weight="900" font-family="system-ui, -apple-system, sans-serif" text-anchor="middle" letter-spacing="0.1">NASIONAL</text>
                                    <!-- Bottom Text -->
                                    <text x="12" y="21.3" fill="#facc15" font-size="0.95" font-weight="800" font-family="system-ui, -apple-system, sans-serif" text-anchor="middle" letter-spacing="0.1">AVIGNAM JAGAT SAMAGRAM</text>
                                </svg>
                                <h4 class="text-xs font-bold uppercase tracking-wider">Pencarian & Evakuasi (SAR)</h4>
                            </div>
                            @if($latestSar && !empty($latestSar->report_data))
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-orange-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Total Dievakuasi</span>
                                        <span class="text-base font-bold text-orange-600 mt-0.5">{{ $latestSar->report_data['total_dievakuasi'] ?? 0 }}</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-orange-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Masih Dicari</span>
                                        <span class="text-base font-bold text-red-500 mt-0.5">{{ $latestSar->report_data['masih_dicari'] ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="mt-2.5 space-y-2 text-[11px]">
                                    <div class="bg-white/60 p-2.5 rounded-lg border border-orange-50">
                                        <span class="text-slate-500 font-bold block mb-0.5 uppercase text-[9px]">Status Pencarian:</span>
                                        <span class="font-bold text-slate-700">{{ $latestSar->report_data['status_pencarian'] ?? 'Dalam proses' }}</span>
                                    </div>
                                    <div class="bg-white/60 p-2.5 rounded-lg border border-orange-50">
                                        <span class="text-slate-500 font-bold block mb-0.5 uppercase text-[9px]">Lokasi Evakuasi:</span>
                                        <span class="text-slate-700 font-semibold leading-relaxed">{{ $latestSar->report_data['lokasi_evakuasi'] ?? '-' }}</span>
                                    </div>
                                </div>
                                @if(!empty($latestSar->report_data['kendala']))
                                    <div class="mt-3 bg-white/60 p-2.5 rounded-lg border border-orange-50 text-[11px]">
                                        <span class="text-slate-500 font-bold block mb-0.5 uppercase text-[9px]">Kendala di Lapangan:</span>
                                        <span class="text-slate-700 font-semibold leading-relaxed">{{ $latestSar->report_data['kendala'] }}</span>
                                    </div>
                                @endif
                                <p class="text-[9px] text-slate-400 mt-2.5 italic">Dilaporkan oleh: {{ $latestSar->volunteer->name }} · {{ $latestSar->created_at->diffForHumans() }}</p>
                            @else
                                <div class="grid grid-cols-2 gap-2 text-xs opacity-50">
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-orange-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Total Dievakuasi</span>
                                        <span class="text-base font-bold text-orange-600 mt-0.5">0</span>
                                    </div>
                                    <div class="bg-white/80 p-2.5 rounded-lg border border-orange-50 flex flex-col">
                                        <span class="text-slate-400 font-semibold text-[9px] uppercase">Masih Dicari</span>
                                        <span class="text-base font-bold text-red-500 mt-0.5">0</span>
                                    </div>
                                </div>
                                <div class="mt-2.5 space-y-2 text-[11px] opacity-50">
                                    <div class="bg-white/60 p-2.5 rounded-lg border border-orange-50">
                                        <span class="text-slate-500 font-bold block mb-0.5 uppercase text-[9px]">Status Pencarian:</span>
                                        <span class="font-bold text-slate-700">Belum dimulai</span>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-450 mt-3.5 italic font-medium">Belum ada update pencarian dari relawan SAR.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Kanan: Panel --}}
    <div class="lg:col-span-1 space-y-5">

        {{-- Info Pelapor --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-person-circle" style="color: #3B6FE8;"></i> Informasi Pelapor
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Pelapor</span>
                    <span class="font-semibold text-slate-800">{{ $laporan['reporter_name'] }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Dilaporkan</span>
                    <span class="font-semibold text-slate-800">{{ $laporan['tanggal'] }}</span>
                </div>
                @if(!empty($laporan['tingkat_bencana']))
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Tingkat</span>
                        <span class="font-semibold text-slate-800">{{ $laporan['tingkat_bencana'] }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Aksi Cepat --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <h3 class="text-sm font-semibold text-slate-800">Aksi Cepat</h3>
            </div>
            <div class="p-5 space-y-2">
                <button type="button"
                        onclick="window.open('https://www.google.com/maps/dir/?api=1&destination={{ $laporan['latitude'] }},{{ $laporan['longitude'] }}', '_blank')"
                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-all cursor-pointer">
                    <i class="bi bi-signpost-2 text-sm"></i> Petunjuk Arah
                </button>
                <button type="button"
                        onclick="navigator.share ? navigator.share({title: '{{ addslashes($laporan['judul']) }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Link disalin!'))"
                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-all cursor-pointer">
                    <i class="bi bi-share text-sm"></i> Bagikan Laporan
                </button>
            </div>
        </div>

        {{-- Panel Tindakan (Admin only) --}}
        @if(strtolower(auth()->user()->role) === 'admin')
        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <h3 class="text-sm font-semibold text-slate-800">Panel Tindakan</h3>
            </div>
            <div class="p-5">
                    @php 
                        $currentStatus = $laporan['status_raw'] ?? 'PENDING'; 
                        $currentType = $laporan['disaster_type'] ?? 'unknown';
                    @endphp

                    <p class="text-xs text-slate-500 mb-4">
                        {{ $currentStatus === 'PENDING' ? 'Tinjau laporan ini, tentukan jenis bencana dan statusnya.' : 'Update status sesuai kondisi terkini.' }}
                    </p>

                    <form id="updateStatusForm" action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1.5">Jenis Bencana</label>
                            <select name="disaster_type" required
                                    class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white text-slate-700">
                                <option value="unknown"    {{ $currentType === 'unknown'    ? 'selected' : '' }}>Belum Diketahui</option>
                                <option value="flood"      {{ $currentType === 'flood'      ? 'selected' : '' }}>Banjir</option>
                                <option value="fire"       {{ $currentType === 'fire'       ? 'selected' : '' }}>Kebakaran</option>
                                <option value="earthquake" {{ $currentType === 'earthquake' ? 'selected' : '' }}>Gempa Bumi</option>
                                <option value="landslide"  {{ $currentType === 'landslide'  ? 'selected' : '' }}>Tanah Longsor</option>
                                <option value="tsunami"    {{ $currentType === 'tsunami'    ? 'selected' : '' }}>Tsunami</option>
                                <option value="storm"      {{ $currentType === 'storm'      ? 'selected' : '' }}>Badai</option>
                                <option value="volcano"    {{ $currentType === 'volcano'    ? 'selected' : '' }}>Gunung Meletus</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1.5">Status</label>
                            <select name="status" required
                                    class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white text-slate-700">
                                <option value="PENDING"  {{ $currentStatus === 'PENDING'  ? 'selected' : '' }}>Pending</option>
                                <option value="AWAS"     {{ $currentStatus === 'AWAS'     ? 'selected' : '' }}>Awas</option>
                                <option value="SIAGA_1"  {{ $currentStatus === 'SIAGA_1'  ? 'selected' : '' }}>Siaga 1</option>
                                <option value="SIAGA_2"  {{ $currentStatus === 'SIAGA_2'  ? 'selected' : '' }}>Siaga 2</option>
                                <option value="DECLINE"  {{ $currentStatus === 'DECLINE'  ? 'selected' : '' }}>Tolak</option>
                            </select>
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 text-sm font-semibold text-white rounded-xl cursor-pointer"
                                style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%);">
                            Simpan Perubahan
                        </button>
                    </form>

                    {{-- Tombol Selesai terpisah --}}
                    @if($currentStatus !== 'RESOLVED')
                        <form id="resolveForm" action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="status" value="RESOLVED">
                            <input type="hidden" name="disaster_type" value="{{ $currentType }}">
                            <button type="submit"
                                    class="w-full py-2.5 text-sm font-semibold text-white rounded-xl cursor-pointer transition-all hover:opacity-90"
                                    style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                                Tandai Selesai
                            </button>
                        </form>
                    @endif
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Custom Confirm Modal --}}
<div id="confirmModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm sigma-modal-backdrop"></div>
    <div class="relative bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl border border-slate-100/80 sigma-modal-content">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center mx-auto mb-5 shadow-lg shadow-emerald-500/20 sigma-modal-icon" style="transform: rotate(3deg);">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Selesaikan Laporan?</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-6">Apakah Anda yakin ingin mengubah status laporan ini menjadi <strong>Selesai</strong>? Laporan yang diselesaikan akan ditandai sebagai teratasi.</p>
            <div class="flex items-center gap-2.5">
                <button type="button" id="confirmCancelBtn" class="flex-1 py-2.5 text-xs font-bold text-slate-600 border border-slate-200 bg-white rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">Batalkan</button>
                <button type="button" id="confirmOkBtn" class="flex-1 py-2.5 text-xs font-bold text-white rounded-xl shadow-md transition-all hover:opacity-95 cursor-pointer" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">Ya, Selesai</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showConfirmModal(callback) {
        const modal = document.getElementById('confirmModal');
        const backdrop = modal.querySelector('.sigma-modal-backdrop');
        const content = modal.querySelector('.sigma-modal-content');
        const okBtn = document.getElementById('confirmOkBtn');
        const cancelBtn = document.getElementById('confirmCancelBtn');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.add('is-visible');
            content.classList.add('is-visible');
        });

        const closeModal = () => {
            backdrop.classList.remove('is-visible');
            backdrop.classList.add('is-hiding');
            content.classList.remove('is-visible');
            content.classList.add('is-hiding');
            setTimeout(() => {
                modal.classList.add('hidden');
                backdrop.classList.remove('is-hiding');
                content.classList.remove('is-hiding');
            }, 300);
        };

        const newOkBtn = okBtn.cloneNode(true);
        const newCancelBtn = cancelBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(newOkBtn, okBtn);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

        newOkBtn.addEventListener('click', () => { closeModal(); callback(true); });
        newCancelBtn.addEventListener('click', () => { closeModal(); callback(false); });
        modal.onclick = (e) => { if (e.target === modal || e.target === backdrop) { closeModal(); callback(false); } };
    }

    document.getElementById('updateStatusForm')?.addEventListener('submit', function(e) {
        const select = this.querySelector('select[name="status"]');
        if (select && select.value === 'RESOLVED') {
            e.preventDefault();
            showConfirmModal((confirmed) => {
                if (confirmed) {
                    this.submit();
                }
            });
        }
    });

    document.getElementById('resolveForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        showConfirmModal((confirmed) => {
            if (confirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection
