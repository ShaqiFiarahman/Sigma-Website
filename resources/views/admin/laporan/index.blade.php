@extends('layouts.app')
@section('title', 'Kelola Laporan')
@section('subtitle', 'Verifikasi, tinjau, dan kelola seluruh laporan bencana.')

@section('page-actions')
    <x-ui.back-button :route="route('admin.dashboard')" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- stats singkat --}}
    <div class="flex items-center gap-6 mb-5 text-sm">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-500"></span>
            <span class="text-slate-500">Laporan Aktif</span>
            <span class="font-bold text-slate-900">{{ $stats['active'] }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            <span class="text-slate-500">Butuh Verifikasi</span>
            <span class="font-bold text-slate-900">{{ $stats['need_verify'] }}</span>
        </div>
    </div>

    <div class="flex gap-4">
        @include('admin.laporan._list-panel')
        @include('admin.laporan._detail-panel')
    </div>
</div>

@include('admin.laporan._confirm-modal')
@endsection

@section('scripts')
    @include('admin.laporan._scripts')
@endsection
