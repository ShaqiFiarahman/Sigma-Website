@extends('layouts.app')
@section('title', 'Riwayat Laporan Tugas')

@section('page-actions')
    <a href="{{ route('volunteer.report.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-white rounded-lg transition-all hover:-translate-y-0.5"
        style="background: linear-gradient(135deg, #1e3a8a, #3B6FE8); box-shadow: 0 2px 8px rgba(59,111,232,0.3);">
        <i class="bi bi-plus-lg text-xs"></i> Buat Laporan
    </a>
@endsection

@section('content')

@if(session('msg'))
    <div class="mb-5 p-4 rounded-xl flex items-center gap-3 text-sm font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
        <i class="bi bi-check-circle-fill text-emerald-500"></i> {{ session('msg') }}
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100"><i class="bi bi-x-lg"></i></button>
    </div>
@endif

{{-- Info Card --}}
<div class="bg-white border border-slate-200/60 rounded-2xl p-5 mb-6 flex items-center gap-4" style="box-shadow: 0 2px 8px rgba(10,15,30,0.04);">
    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
        style="background: linear-gradient(135deg, #E4F0F6, #C8DFF0);">
        @php
            $skillIcon = match($volunteer->skill) {
                'MEDIS' => 'bi-heart-pulse',
                'SAR' => 'bi-life-preserver',
                'LOGISTIK' => 'bi-box-seam',
                'KONSUMSI' => 'bi-cup-hot',
                'PSIKOSOSIAL' => 'bi-chat-heart',
                default => 'bi-clipboard-data',
            };
        @endphp
        <i class="bi {{ $skillIcon }} text-lg" style="color: #1e3a8a;"></i>
    </div>
    <div>
        <p class="text-sm font-bold text-slate-900">{{ $volunteer->name }} — Tim {{ ucfirst(strtolower($volunteer->skill)) }}</p>
        <p class="text-xs text-slate-500">{{ $volunteer->assignment ?? 'Belum ada penugasan' }} · {{ $reports->total() }} laporan dikirim</p>
    </div>
</div>

{{-- Reports List --}}
<div class="space-y-4">
    @forelse($reports as $report)
        <div class="bg-white border border-slate-200/60 rounded-2xl p-5 hover:shadow-md transition-all" style="box-shadow: 0 2px 8px rgba(10,15,30,0.04);">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <p class="text-sm font-bold text-slate-900">
                        Laporan {{ ucfirst(strtolower($report->skill_type)) }}
                        @if($report->disaster)
                            — {{ $report->disaster->title }}
                        @endif
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $report->created_at->format('d M Y, H:i') }}</p>
                </div>
                <span class="shrink-0 text-[10px] font-bold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Terkirim
                </span>
            </div>

            {{-- Report Data --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($report->report_data as $key => $value)
                    <div class="bg-slate-50 rounded-xl px-3 py-2.5 border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">
                            {{ str_replace('_', ' ', ucfirst($key)) }}
                        </p>
                        <p class="text-sm font-bold text-slate-800 line-clamp-2">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            @if($report->notes)
                <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Catatan</p>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $report->notes }}</p>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white border border-slate-200/60 rounded-2xl p-12 text-center" style="box-shadow: 0 2px 8px rgba(10,15,30,0.04);">
            <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center"
                style="background: linear-gradient(135deg, #E4F0F6, #C8DFF0);">
                <i class="bi bi-clipboard-data text-xl" style="color: #1e3a8a;"></i>
            </div>
            <p class="text-sm font-bold text-slate-800 mb-1">Belum ada laporan</p>
            <p class="text-xs text-slate-400 mb-4">Kirim laporan tugas pertama kamu.</p>
            <a href="{{ route('volunteer.report.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white rounded-lg"
                style="background: linear-gradient(135deg, #1e3a8a, #3B6FE8);">
                <i class="bi bi-plus-lg"></i> Buat Laporan
            </a>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($reports->hasPages())
    <div class="mt-6">
        {{ $reports->links() }}
    </div>
@endif

@endsection
