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
        @if($volunteer->skill === 'SAR')
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
        @elseif($volunteer->skill === 'KONSUMSI')
            <svg class="w-5.5 h-5.5 shrink-0 shadow-sm rounded-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #059669;">
                <!-- Background Circle -->
                <circle cx="12" cy="12" r="11" fill="currentColor"/>
                <!-- Fork (White) -->
                <path d="M8.5 12v7c0 .6-.4 1-1 1s-1-.4-1-1v-7c-1.2-.3-2-1.3-2-2.5V5c0-.6.4-1 1-1s1 .4 1 1v4h.8V5c0-.6.4-1 1-1s1 .4 1 1v4h.8V5c0-.6.4-1 1-1s1 .4 1 1v4.5c0 1.2-.8 2.2-2 2.5z" fill="#ffffff"/>
                <!-- Knife (White) -->
                <path d="M16.5 12v7c0 .6-.4 1-1 1s-1-.4-1-1v-7.2c-.8-.2-1.5-1-1.5-1.8V7.5c0-2 1.5-3.5 3-3.5.3 0 .5.2.5.5v11.7c0 .8-.5 1.5-1 1.8z" fill="#ffffff"/>
            </svg>
        @elseif($volunteer->skill === 'PSIKOSOSIAL')
            <svg class="w-5.5 h-5.5 shrink-0 shadow-sm rounded-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #7c3aed;">
                <!-- Background Purple Circle -->
                <circle cx="12" cy="12" r="11" fill="currentColor"/>
                <!-- Big White Hand (Adult Hand) -->
                <path d="M21.5 14.5c-2.5-1-5-2.8-7.2-5-1.5-1.5-3.5-5-5.2-7.2-0.5-0.6-1.3-0.7-1.8-0.2c-0.5 0.5-0.4 1.3 0.1 1.8c1.5 1.8 3.5 5 4.8 7 -2.2-1.8-4.5-4-6.2-5.5-0.5-0.5-1.3-0.5-1.8 0c-0.5 0.5-0.5 1.3 0 1.8c1.6 1.6 3.8 4 5.8 5.5 -2.8-1.8-5.8-3.2-7.5-4-0.6-0.3-1.4 0-1.7 0.6c-0.3 0.6 0 1.4 0.6 1.7c2 1 4.8 2.5 7.5 4 -3.2-1-6.2-1.5-8-1.5 -0.7 0-1.3 0.6-1.3 1.3c0 0.7 0.6 1.3 1.3 1.3c2.2 0 5.2 0.6 8 1.8 -1.8 0.3-3.5 1.4-4.5 2.5 -0.5 0.6-0.4 1.4 0.2 1.9c0.6 0.5 1.4 0.4 1.9-0.2c1-1.2 2.5-2.2 4.5-2.5 1.4 1.2 2.8 2.8 4.2 4.2c1.4 1.4 3.2 2.2 4.8 2.8h3L21.5 14.5z" fill="#ffffff" />
                <!-- Small Purple Hand (Child Hand) -->
                <path d="M16 16.5c-1.5-0.6-3-1.8-4.3-3.2-0.9-0.9-2.1-3-3.1-4.3-0.3-0.4-0.8-0.4-1.1-0.1c-0.3 0.3-0.2 0.8 0.1 1.1c0.9 1.1 2.1 3 2.9 4.2 -1.3-1.1-2.7-2.4-3.7-3.3-0.3-0.3-0.8-0.3-1.1 0c-0.3 0.3-0.3 0.8 0 1.1c1 1 2.3 2.4 3.5 3.3 -1.7-1.1-3.5-1.9-4.5-2.4-0.4-0.2-0.8 0-1 0.4s0 0.8 0.4 1c1.2 0.6 2.9 1.5 4.5 2.4 -1.9-0.6-3.7-0.9-4.8-0.9-0.4 0-0.8 0.4-0.8 0.8s0.4 0.8 0.8 0.8c1.3 0 3.1 0.4 4.8 1.1 -1.1 0.2-2.1 0.8-2.7 1.5 -0.3 0.4-0.2 0.8 0.1 1.1s0.8 0.1 1.1-0.2c0.6-0.7 1.5-1.3 2.7-1.5 0.8 0.7 1.7 1.7 2.5 2.5 0.8 0.8 1.9 1.3 2.9 1.7h1.8L16 16.5z" fill="#7c3aed" />
            </svg>
        @else
            @php
                $skillIcon = match($volunteer->skill) {
                    'MEDIS' => 'bi-heart-pulse',
                    'LOGISTIK' => 'bi-box-seam',
                    default => 'bi-clipboard-data',
                };
            @endphp
            <i class="bi {{ $skillIcon }} text-lg" style="color: #1e3a8a;"></i>
        @endif
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

            @if($report->photo_urls && count($report->photo_urls) > 0)
                <div class="mt-3 flex gap-2 flex-wrap">
                    @foreach($report->photo_urls as $photo)
                        <a href="{{ $photo }}" target="_blank" class="block w-16 h-16 rounded-lg overflow-hidden border border-slate-200 hover:border-blue-300 transition-colors">
                            <img src="{{ $photo }}" alt="Foto laporan" class="w-full h-full object-cover">
                        </a>
                    @endforeach
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
