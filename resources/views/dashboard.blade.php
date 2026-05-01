@extends('layout')
@section('title', 'Dashboard')
@section('subtitle', 'Ikhtisar data dan status laporan terkini.')

@section('content')

{{-- STAT CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    
    {{-- Total --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-500">Total Laporan</h3>
            <div class="w-8 h-8 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600">
                <i class="bi bi-file-earmark-text"></i>
            </div>
        </div>
        <div class="mt-auto">
            <p class="text-3xl font-bold text-slate-900">{{ $total ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Keseluruhan data masuk</p>
        </div>
    </div>

    {{-- Pending --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-500">Menunggu Tindakan</h3>
            <div class="w-8 h-8 rounded-md bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
        <div class="mt-auto">
            <p class="text-3xl font-bold text-slate-900">{{ $pending ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Laporan perlu ditinjau</p>
        </div>
    </div>

    {{-- Verified --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-500">Terverifikasi</h3>
            <div class="w-8 h-8 rounded-md bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
        <div class="mt-auto">
            <p class="text-3xl font-bold text-slate-900">{{ $selesai ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Laporan disetujui</p>
        </div>
    </div>

</div>

{{-- WELCOME & QUICK ACTIONS --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    
    {{-- Banner --}}
    <div class="lg:col-span-2 bg-slate-900 rounded-xl border border-slate-800 p-8 sm:p-10 flex flex-col justify-center relative overflow-hidden">
        <div class="relative z-10">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white/10 text-white/90 text-xs font-medium mb-4 border border-white/10">
                SIGMA v2.0
            </span>
            <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight mb-3">Selamat datang, Admin.</h2>
            <p class="text-slate-400 text-sm sm:text-base max-w-xl leading-relaxed mb-6">
                Sistem Informasi Tanggap Bencana memberikan Anda kendali penuh untuk memantau laporan masyarakat, memverifikasi kejadian, dan mengambil tindakan cepat.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('laporan') }}" class="px-4 py-2.5 bg-white text-slate-900 text-sm font-semibold rounded-lg hover:bg-slate-100 transition-colors shadow-sm">
                    Lihat Laporan
                </a>
                <a href="{{ route('create') }}" class="px-4 py-2.5 bg-white/5 text-white text-sm font-semibold rounded-lg hover:bg-white/10 transition-colors border border-white/10">
                    Buat Laporan Baru
                </a>
            </div>
        </div>
        
        {{-- Subtle background element instead of big blobs --}}
        <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-gradient-to-l from-primary-600/20 to-transparent pointer-events-none"></div>
    </div>

    {{-- Quick Actions list --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-900">Aksi Cepat</h3>
        </div>
        <div class="p-2 flex-1 flex flex-col gap-1">
            <a href="{{ route('laporan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                <div class="w-8 h-8 rounded-md bg-slate-50 text-slate-500 flex items-center justify-center group-hover:bg-white group-hover:shadow-sm border border-transparent group-hover:border-slate-200 transition-all">
                    <i class="bi bi-list-ul"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-900">Semua Laporan</p>
                    <p class="text-xs text-slate-500">Kelola data laporan</p>
                </div>
            </a>
            <a href="{{ route('create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                <div class="w-8 h-8 rounded-md bg-slate-50 text-slate-500 flex items-center justify-center group-hover:bg-white group-hover:shadow-sm border border-transparent group-hover:border-slate-200 transition-all">
                    <i class="bi bi-plus-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-900">Buat Laporan</p>
                    <p class="text-xs text-slate-500">Input kejadian baru</p>
                </div>
            </a>
            <a href="{{ route('laporan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors group">
                <div class="w-8 h-8 rounded-md bg-slate-50 text-slate-500 flex items-center justify-center group-hover:bg-white group-hover:shadow-sm border border-transparent group-hover:border-slate-200 transition-all">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-900">Laporan Pending</p>
                    <p class="text-xs text-slate-500">Tinjau laporan baru</p>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection