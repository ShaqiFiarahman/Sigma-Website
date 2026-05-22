@extends('layouts.app')
@section('title', 'Dashboard Relawan')

@section('content')

    <x-welcome-banner />

    <div class="space-y-8 pb-6">

        {{-- Warning Banner --}}
        <x-warning-banner />

        {{-- Status Card --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                         style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
                        <i class="bi bi-person-badge-fill text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ $volunteer->name }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $volunteer->skill }} &middot; REL-{{ str_pad($volunteer->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
                <div>
                    @if($volunteer->status === 'APPROVED')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                        </span>
                    @elseif($volunteer->status === 'PENDING')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu Verifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        @if($volunteer->status === 'APPROVED')
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5" style="box-shadow: 0 1px 3px rgba(10,15,30,0.05);">
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Penugasan</p>
                    <p class="text-lg font-extrabold text-slate-900">{{ $volunteer->assignment ?? 'Belum ada' }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Lokasi tugas saat ini</p>
                </div>
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5" style="box-shadow: 0 1px 3px rgba(10,15,30,0.05);">
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Keahlian</p>
                    <p class="text-lg font-extrabold text-slate-900">{{ $volunteer->skill }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Bidang spesialisasi</p>
                </div>
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5" style="box-shadow: 0 1px 3px rgba(10,15,30,0.05);">
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Bergabung</p>
                    <p class="text-lg font-extrabold text-slate-900">{{ $volunteer->created_at->format('d M Y') }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">Tanggal terdaftar</p>
                </div>
            </div>

            {{-- Assignment Detail --}}
            @if($volunteer->assignment)
                <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-geo-alt-fill" style="color: #3B6FE8;"></i> Penugasan Aktif
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-blue-50 shrink-0">
                                <i class="bi bi-pin-map-fill text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $volunteer->assignment }}</p>
                                <p class="text-xs text-slate-500 mt-1">Anda ditugaskan di lokasi ini. Hubungi Admin jika ada kendala.</p>
                                <button type="button"
                                        onclick="window.open('https://api.whatsapp.com/send?phone=6285934415914&text={{ urlencode('Halo Admin, saya relawan ' . $volunteer->name . ' (REL-' . str_pad($volunteer->id, 5, '0', STR_PAD_LEFT) . ') ingin bertanya mengenai penugasan di ' . $volunteer->assignment) }}', '_blank')"
                                        class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white rounded-xl cursor-pointer"
                                        style="background: #25D366;">
                                    <i class="bi bi-whatsapp text-[11px]"></i> Hubungi Admin
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Berita --}}
        <x-news-section :news="$news" />

        {{-- Peta Bencana --}}
        <x-disaster-map />

    </div>

@section('footer')
    <x-footer />
@endsection

@endsection
