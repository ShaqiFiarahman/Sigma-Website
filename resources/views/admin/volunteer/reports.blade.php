@extends('layouts.app')
@section('title', 'Laporan Tugas Relawan')
@section('subtitle', 'Monitoring laporan yang dikirim relawan di lapangan.')

@section('page-actions')
    <a href="{{ route('volunteer.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
        <i class="bi bi-people text-xs"></i> Daftar Relawan
    </a>
@endsection

@section('content')

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-100 p-4 mb-6">
    <form method="GET" action="{{ route('admin.volunteer.reports') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[140px]">
            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Keahlian</label>
            <select name="skill" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg bg-white">
                <option value="">Semua</option>
                @foreach($skills as $value => $label)
                    <option value="{{ $value }}" {{ request('skill') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Bencana</label>
            <select name="disaster_id" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg bg-white">
                <option value="">Semua</option>
                @foreach($disasters as $d)
                    <option value="{{ $d->id }}" {{ request('disaster_id') == $d->id ? 'selected' : '' }}>
                        {{ \Illuminate\Support\Str::limit($d->title, 30) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[150px]">
            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Relawan</label>
            <select name="volunteer_id" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg bg-white">
                <option value="">Semua</option>
                @foreach($volunteers as $v)
                    <option value="{{ $v->id }}" {{ request('volunteer_id') == $v->id ? 'selected' : '' }}>
                        {{ $v->name }} ({{ $v->skill }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white rounded-lg" style="background: #3B6FE8;">
                <i class="bi bi-funnel text-[10px]"></i> Filter
            </button>
            @if(request()->hasAny(['skill', 'disaster_id', 'volunteer_id']))
                <a href="{{ route('admin.volunteer.reports') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Reports --}}
<div class="space-y-4">
    @forelse($reports as $report)
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
            {{-- Header --}}
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-white shrink-0">
                        {{ substr($report->volunteer->name ?? '?', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $report->volunteer->name ?? 'Unknown' }}</p>
                        <p class="text-[11px] text-slate-400">{{ $report->skill_type }} · {{ $report->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @if($report->disaster)
                    <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100 hidden sm:inline-flex items-center gap-1">
                        <i class="bi bi-geo-alt text-[9px]"></i> {{ \Illuminate\Support\Str::limit($report->disaster->title, 25) }}
                    </span>
                @endif
            </div>

            {{-- Data --}}
            <div class="p-5">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($report->report_data ?? [] as $key => $value)
                        <div class="bg-slate-50 rounded-xl px-3 py-2.5 border border-slate-100">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">
                                {{ str_replace('_', ' ', ucfirst($key)) }}
                            </p>
                            <p class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>

                @if($report->notes)
                    <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-1">Catatan</p>
                        <p class="text-xs text-slate-600">{{ $report->notes }}</p>
                    </div>
                @endif

                @if($report->disaster)
                    <p class="text-[11px] text-slate-400 mt-3 sm:hidden">
                        <i class="bi bi-geo-alt"></i> {{ $report->disaster->title }}
                    </p>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center">
            <i class="bi bi-clipboard-data text-3xl text-slate-300"></i>
            <p class="text-sm font-semibold text-slate-700 mt-3">Belum ada laporan tugas</p>
            <p class="text-xs text-slate-400 mt-1">Laporan akan muncul setelah relawan mengirim dari lapangan.</p>
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
