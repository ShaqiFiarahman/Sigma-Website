@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <x-welcome-banner />
    <x-admin.stats-panel
        :total="$total" :pending="$pending" :selesai="$selesai" :decline="$decline"
        :awas="$awas" :siaga1="$siaga1" :siaga2="$siaga2"
        :approvedVolunteers="$approvedVolunteers" :totalVolunteers="$totalVolunteers"
    />
    <x-admin.chart-panel
        :chartLabels="$chartLabels" :chartData="$chartData"
        :chartVerified="$chartVerified" :chartPending="$chartPending"
        :allDisasters="$allDisasters" :pending="$pending"
    />

    {{-- Quick Access --}}
    <div class="mb-4 px-1">
        <h2 class="section-title">Akses Cepat Administrator</h2>
        <p class="text-xs text-slate-500 mt-0.5">Menu kelola fitur dan layanan utama SIGMA</p>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('volunteer.index') }}" class="menu-card group">
            <div class="menu-icon-wrap"><i class="bi bi-people-fill"></i></div>
            <p class="font-bold text-sm mb-1 text-slate-900">Data Relawan</p>
            <p class="text-xs text-slate-500 leading-relaxed">{{ $totalVolunteers }} terdaftar</p>
        </a>
        <a href="{{ route('admin.volunteer.reports') }}" class="menu-card group">
            <div class="menu-icon-wrap"><i class="bi bi-file-earmark-text-fill"></i></div>
            <p class="font-bold text-sm mb-1 text-slate-900">Laporan Tugas</p>
            <p class="text-xs text-slate-500 leading-relaxed">Aktivitas relawan lapangan</p>
        </a>
        <a href="{{ route('shelter') }}" class="menu-card group">
            <div class="menu-icon-wrap"><i class="bi bi-house-heart-fill"></i></div>
            <p class="font-bold text-sm mb-1 text-slate-900">Data Posko</p>
            <p class="text-xs text-slate-500 leading-relaxed">Titik pengungsian & shelter</p>
        </a>
        <a href="{{ route('search') }}" class="menu-card group">
            <div class="menu-icon-wrap"><i class="bi bi-search"></i></div>
            <p class="font-bold text-sm mb-1 text-slate-900">Cari Bencana</p>
            <p class="text-xs text-slate-500 leading-relaxed">Pencarian & filter data</p>
        </a>
    </div>

    <x-disaster-map />
@endsection
