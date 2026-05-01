@extends('layout')
@section('title', 'Dashboard')

@section('content')

{{-- ── STAT CARDS ─────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Total --}}
    <div class="relative rounded-2xl overflow-hidden p-6 flex items-center gap-4
                bg-gradient-to-br from-blue-700 to-blue-400
                shadow-[0_8px_24px_rgba(29,78,216,0.28)]
                transition-transform duration-250 hover:-translate-y-1 cursor-default">
        <div class="relative z-10">
            <p class="text-xs font-bold text-blue-100 tracking-wide uppercase mb-1">Total Laporan</p>
            <p class="text-4xl font-extrabold text-white tracking-tight leading-none mb-2.5">{{ $total ?? 0 }}</p>
            <span class="inline-block bg-white/15 border border-white/20 backdrop-blur text-white/90
                         text-[0.68rem] font-semibold px-2.5 py-0.5 rounded-full">Semua waktu</span>
        </div>
        <div class="ml-auto w-14 h-14 rounded-xl bg-white/15 border border-white/20 backdrop-blur
                    flex items-center justify-center text-2xl text-white/90 shrink-0 relative z-10">
            <i class="bi bi-file-earmark-text"></i>
        </div>
        <div class="absolute w-36 h-36 rounded-full bg-white/5 -bottom-12 -right-8 pointer-events-none"></div>
    </div>

    {{-- Pending --}}
    <div class="relative rounded-2xl overflow-hidden p-6 flex items-center gap-4
                bg-gradient-to-br from-amber-600 to-amber-400
                shadow-[0_8px_24px_rgba(180,83,9,0.28)]
                transition-transform duration-250 hover:-translate-y-1 cursor-default">
        <div class="relative z-10">
            <p class="text-xs font-bold text-amber-100 tracking-wide uppercase mb-1">Menunggu Tindakan</p>
            <p class="text-4xl font-extrabold text-white tracking-tight leading-none mb-2.5">{{ $pending ?? 0 }}</p>
            <span class="inline-block bg-white/15 border border-white/20 backdrop-blur text-white/90
                         text-[0.68rem] font-semibold px-2.5 py-0.5 rounded-full">Perlu review</span>
        </div>
        <div class="ml-auto w-14 h-14 rounded-xl bg-white/15 border border-white/20 backdrop-blur
                    flex items-center justify-center text-2xl text-white/90 shrink-0 relative z-10">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div class="absolute w-36 h-36 rounded-full bg-white/5 -bottom-12 -right-8 pointer-events-none"></div>
    </div>

    {{-- Verified --}}
    <div class="relative rounded-2xl overflow-hidden p-6 flex items-center gap-4
                bg-gradient-to-br from-emerald-600 to-emerald-400
                shadow-[0_8px_24px_rgba(5,150,105,0.28)]
                transition-transform duration-250 hover:-translate-y-1 cursor-default">
        <div class="relative z-10">
            <p class="text-xs font-bold text-emerald-100 tracking-wide uppercase mb-1">Terverifikasi</p>
            <p class="text-4xl font-extrabold text-white tracking-tight leading-none mb-2.5">{{ $selesai ?? 0 }}</p>
            <span class="inline-block bg-white/15 border border-white/20 backdrop-blur text-white/90
                         text-[0.68rem] font-semibold px-2.5 py-0.5 rounded-full">Sudah diproses</span>
        </div>
        <div class="ml-auto w-14 h-14 rounded-xl bg-white/15 border border-white/20 backdrop-blur
                    flex items-center justify-center text-2xl text-white/90 shrink-0 relative z-10">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="absolute w-36 h-36 rounded-full bg-white/5 -bottom-12 -right-8 pointer-events-none"></div>
    </div>
</div>

{{-- ── WELCOME + QUICK ACTIONS ─────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Welcome Banner (spans 2 cols) --}}
    <div class="lg:col-span-2 relative rounded-2xl overflow-hidden
                bg-gradient-to-br from-slate-900 via-blue-800 to-sky-500
                shadow-[0_8px_32px_rgba(29,78,216,0.22)] p-10 flex items-center gap-8">

        {{-- Blobs --}}
        <div class="absolute w-80 h-80 rounded-full bg-white/[0.03] -top-28 -left-20 pointer-events-none"></div>
        <div class="absolute w-52 h-52 rounded-full bg-white/[0.04] bottom-[-80px] left-[35%] pointer-events-none"></div>

        {{-- Content --}}
        <div class="relative z-10 flex-1 min-w-0">
            <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/15 backdrop-blur
                         text-white/85 text-[0.72rem] font-bold tracking-wide px-3.5 py-1 rounded-full mb-4">
                <i class="bi bi-shield-fill-check"></i> SIGMA Admin Panel
            </span>
            <h2 class="text-2xl font-extrabold text-white tracking-tight leading-tight mb-3">
                Selamat datang kembali, <span class="text-sky-300">Admin!</span> 👋
            </h2>
            <p class="text-white/65 text-sm leading-relaxed mb-6 max-w-md">
                Panel kontrol <strong class="text-white/85">SIGMA</strong> — Sistem Informasi Gerak Maju Tanggap Bencana.
                Pantau laporan masuk, verifikasi kejadian, dan koordinasikan respons tim di lapangan.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('laporan') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-blue-700
                          text-sm font-bold shadow-[0_4px_14px_rgba(0,0,0,0.15)]
                          hover:bg-blue-50 hover:-translate-y-0.5 hover:shadow-[0_6px_18px_rgba(0,0,0,0.18)]
                          transition-all duration-200">
                    <i class="bi bi-file-earmark-text"></i> Lihat Data Laporan
                </a>
                <a href="{{ route('create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                          bg-white/10 border border-white/25 backdrop-blur text-white
                          text-sm font-semibold hover:bg-white/20 hover:border-white/40
                          hover:-translate-y-0.5 transition-all duration-200">
                    <i class="bi bi-plus-circle"></i> Buat Laporan Baru
                </a>
            </div>
        </div>

        {{-- Animated Shield --}}
        <div class="relative hidden md:flex w-40 h-40 items-center justify-center shrink-0">
            <div class="absolute top-1/2 left-1/2 w-20 h-20 rounded-full border border-white/10
                        -translate-x-1/2 -translate-y-1/2 animate-ring"></div>
            <div class="absolute top-1/2 left-1/2 w-[118px] h-[118px] rounded-full border border-white/10
                        -translate-x-1/2 -translate-y-1/2 animate-ring-2"></div>
            <div class="absolute top-1/2 left-1/2 w-[156px] h-[156px] rounded-full border border-white/10
                        -translate-x-1/2 -translate-y-1/2 animate-ring-3"></div>
            <i class="bi bi-shield-fill-check text-5xl text-white/30 animate-float relative z-10"></i>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-slate-100">
            <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 text-sm">
                <i class="bi bi-lightning-fill"></i>
            </div>
            <span class="text-sm font-bold text-slate-900">Aksi Cepat</span>
        </div>
        <div class="p-4 flex flex-col gap-2.5">

            {{-- Action items --}}
            <a href="{{ route('laporan') }}"
               class="flex items-center gap-3.5 p-3 rounded-xl bg-slate-50 border border-slate-100
                      hover:bg-blue-50 hover:border-blue-200 hover:translate-x-0.5
                      transition-all duration-200 group no-underline">
                <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center
                            text-blue-600 text-base shrink-0">
                    <i class="bi bi-list-ul"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 leading-none mb-0.5">Data Laporan</p>
                    <p class="text-[0.72rem] text-slate-400">Kelola semua laporan bencana</p>
                </div>
                <i class="bi bi-chevron-right text-xs text-slate-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition-all"></i>
            </a>

            <a href="{{ route('create') }}"
               class="flex items-center gap-3.5 p-3 rounded-xl bg-slate-50 border border-slate-100
                      hover:bg-emerald-50 hover:border-emerald-200 hover:translate-x-0.5
                      transition-all duration-200 group no-underline">
                <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center
                            text-emerald-600 text-base shrink-0">
                    <i class="bi bi-plus-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 leading-none mb-0.5">Buat Laporan</p>
                    <p class="text-[0.72rem] text-slate-400">Tambah laporan kejadian baru</p>
                </div>
                <i class="bi bi-chevron-right text-xs text-slate-300 group-hover:text-emerald-500 group-hover:translate-x-0.5 transition-all"></i>
            </a>

            <a href="{{ route('laporan') }}"
               class="flex items-center gap-3.5 p-3 rounded-xl bg-slate-50 border border-slate-100
                      hover:bg-amber-50 hover:border-amber-200 hover:translate-x-0.5
                      transition-all duration-200 group no-underline">
                <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center
                            text-amber-600 text-base shrink-0">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 leading-none mb-0.5">Laporan Pending</p>
                    @if(($pending ?? 0) > 0)
                        <span class="inline-block bg-red-50 text-red-700 border border-red-200
                                     text-[0.65rem] font-bold px-2 py-0.5 rounded-full">
                            {{ $pending }} perlu ditinjau
                        </span>
                    @else
                        <p class="text-[0.72rem] text-slate-400">Tidak ada laporan pending</p>
                    @endif
                </div>
                <i class="bi bi-chevron-right text-xs text-slate-300 group-hover:text-amber-500 group-hover:translate-x-0.5 transition-all"></i>
            </a>

        </div>
    </div>
</div>

@endsection