@extends('layouts.app')
@section('title', 'Dashboard Relawan')

@section('content')

    <x-welcome-banner />

    <div class="space-y-6 pb-6">

        {{-- Warning Banner --}}
        <x-warning-banner />

        {{-- Notifikasi Penugasan Baru --}}
        @if($volunteer->assignment && !$volunteer->assignment_notified_at)
            <div class="relative overflow-hidden rounded-2xl px-6 py-5 flex items-center justify-between gap-4"
                 style="background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);">
                <div class="absolute top-0 right-0 w-40 h-40 rounded-full opacity-[0.07]" style="background: white; transform: translate(30%, -40%);"></div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0 backdrop-blur-sm border border-white/10">
                        <i class="bi bi-bell-fill text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[13px] font-bold text-white">Penugasan baru</p>
                        <p class="text-xs text-white/70 mt-0.5">Lokasi: <span class="text-white font-semibold">{{ $volunteer->assignment }}</span></p>
                    </div>
                </div>
                <form action="{{ route('volunteer.dismiss_notification') }}" method="POST" class="shrink-0 relative z-10">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-blue-700 bg-white rounded-xl hover:bg-blue-50 transition-all cursor-pointer shadow-lg shadow-blue-900/20">
                        Mengerti
                    </button>
                </form>
            </div>
        @endif

        {{-- Hero Card: Identity + Availability --}}
        @php
            $skillColor = match($volunteer->skill) {
                'MEDIS' => ['from' => '#dc2626', 'to' => '#ef4444', 'bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => 'bi-heart-pulse-fill'],
                'SAR' => ['from' => '#1d4ed8', 'to' => '#3b82f6', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'bi-life-preserver'],
                'LOGISTIK' => ['from' => '#d97706', 'to' => '#f59e0b', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'bi-box-seam-fill'],
                'KONSUMSI' => ['from' => '#059669', 'to' => '#10b981', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'bi-cup-hot-fill'],
                'PSIKOSOSIAL' => ['from' => '#7c3aed', 'to' => '#a78bfa', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => 'bi-chat-heart-fill'],
                default => ['from' => '#334155', 'to' => '#64748b', 'bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'bi-person-badge'],
            };
        @endphp
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
            <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    {{-- Avatar with skill color ring --}}
                    <div class="relative">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-base font-bold"
                             style="background: linear-gradient(135deg, {{ $skillColor['from'] }}, {{ $skillColor['to'] }});">
                            <i class="bi {{ $skillColor['icon'] }}"></i>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-[15px] font-bold text-slate-900">{{ $volunteer->name }}</h2>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-md border {{ $skillColor['bg'] }} {{ $skillColor['text'] }} {{ $skillColor['border'] }}">
                                {{ $volunteer->skill }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">REL-{{ str_pad($volunteer->id, 5, '0', STR_PAD_LEFT) }} · Bergabung {{ $volunteer->created_at->format('M Y') }}</p>
                    </div>
                </div>
                {{-- Availability Toggle --}}
                <form action="{{ route('volunteer.toggle_availability') }}" method="POST" class="flex gap-1.5">
                    @csrf
                    <button type="submit" name="availability" value="available"
                        class="px-4 py-2 text-[11px] font-semibold rounded-xl border transition-all cursor-pointer {{ ($volunteer->availability ?? 'available') === 'available' ? 'border-emerald-300 bg-emerald-50 text-emerald-700 shadow-sm shadow-emerald-100' : 'border-slate-200 text-slate-400 hover:border-emerald-200 hover:text-emerald-600' }}">
                        ● Tersedia
                    </button>
                    <button type="submit" name="availability" value="unavailable"
                        class="px-4 py-2 text-[11px] font-semibold rounded-xl border transition-all cursor-pointer {{ ($volunteer->availability ?? 'available') === 'unavailable' ? 'border-slate-400 bg-slate-100 text-slate-700 shadow-sm' : 'border-slate-200 text-slate-400 hover:border-slate-300' }}">
                        ● Tidak tersedia
                    </button>
                </form>
            </div>
        </div>

        @if($volunteer->status === 'APPROVED')

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="group bg-white rounded-2xl border border-slate-100 p-5 hover:border-blue-200 hover:shadow-lg hover:shadow-blue-50 transition-all duration-300">
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-3xl font-extrabold text-slate-900 tabular-nums leading-none">{{ $totalReports }}</p>
                            <p class="text-[12px] text-slate-500 mt-2">Laporan terkirim</p>
                        </div>
                        @if($reportsThisMonth > 0)
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100">+{{ $reportsThisMonth }}</span>
                        @endif
                    </div>
                </div>
                <div class="group bg-white rounded-2xl border border-slate-100 p-5 hover:border-blue-200 hover:shadow-lg hover:shadow-blue-50 transition-all duration-300">
                    <div>
                        <p class="text-3xl font-extrabold text-slate-900 tabular-nums leading-none">{{ $volunteer->assignment ? '1' : '0' }}</p>
                        <p class="text-[12px] text-slate-500 mt-2">Penugasan aktif</p>
                    </div>
                </div>
                <div class="group bg-white rounded-2xl border border-slate-100 p-5 hover:border-blue-200 hover:shadow-lg hover:shadow-blue-50 transition-all duration-300">
                    <div>
                        <p class="text-3xl font-extrabold text-slate-900 tabular-nums leading-none">{{ $teamMembers->count() }}</p>
                        <p class="text-[12px] text-slate-500 mt-2">Anggota tim</p>
                    </div>
                </div>
            </div>

            {{-- Main Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Left Column (3/5) --}}
                <div class="lg:col-span-3 space-y-6">

                    {{-- Penugasan --}}
                    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
                        <div class="px-6 py-4 flex items-center justify-between">
                            <p class="text-[12px] font-semibold text-slate-500 uppercase tracking-wider">Penugasan</p>
                            @if($volunteer->assignment)
                                <span class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                </span>
                            @endif
                        </div>
                        <div class="px-6 pb-6">
                            @if($volunteer->assignment)
                                <div class="flex items-start gap-4 p-4 rounded-xl" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9);">
                                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
                                         style="background: linear-gradient(135deg, {{ $skillColor['from'] }}, {{ $skillColor['to'] }});">
                                        <i class="bi bi-geo-alt-fill text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[15px] font-bold text-slate-900">{{ $volunteer->assignment }}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ $volunteer->skill }} · Ditugaskan oleh Admin</p>
                                        <button type="button"
                                            onclick="window.open('https://api.whatsapp.com/send?phone=6285934415914&text={{ urlencode('Halo Admin, saya relawan ' . $volunteer->name . ' (REL-' . str_pad($volunteer->id, 5, '0', STR_PAD_LEFT) . ') ingin bertanya mengenai penugasan di ' . $volunteer->assignment) }}', '_blank')"
                                            class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white rounded-xl cursor-pointer transition-all hover:shadow-md"
                                            style="background: #25D366;">
                                            <i class="bi bi-whatsapp text-[10px]"></i> Hubungi Admin
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50">
                                    <i class="bi bi-hourglass text-slate-400"></i>
                                    <p class="text-sm text-slate-500">Menunggu penugasan dari Admin.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Riwayat --}}
                    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
                        <div class="px-6 py-4 flex items-center justify-between">
                            <p class="text-[12px] font-semibold text-slate-500 uppercase tracking-wider">Riwayat Laporan</p>
                            <a href="{{ route('volunteer.reports') }}" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                                Semua →
                            </a>
                        </div>
                        @if($recentReports->count() > 0)
                            <div class="divide-y divide-slate-100/80">
                                @foreach($recentReports as $report)
                                    <div class="px-6 py-3.5 flex items-center gap-3 hover:bg-slate-50/60 transition-colors">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $loop->first ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-400' }}">
                                            <i class="bi bi-file-earmark-text text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[13px] font-medium text-slate-800 truncate">
                                                {{ ucfirst(strtolower($report->skill_type)) }}@if($report->disaster) — {{ \Illuminate\Support\Str::limit($report->disaster->title, 25) }}@endif
                                            </p>
                                        </div>
                                        <p class="text-[11px] text-slate-400 shrink-0">{{ $report->created_at->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 pb-6 pt-2 text-center">
                                <p class="text-sm text-slate-400 mb-3">Belum ada laporan</p>
                                <a href="{{ route('volunteer.report.create') }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white rounded-xl transition-all hover:shadow-md"
                                    style="background: linear-gradient(135deg, {{ $skillColor['from'] }}, {{ $skillColor['to'] }});">
                                    <i class="bi bi-plus-lg text-[10px]"></i> Buat Laporan
                                </a>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Right Column (2/5): Tim --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden h-full">
                        <div class="px-5 py-4 flex items-center justify-between">
                            <p class="text-[12px] font-semibold text-slate-500 uppercase tracking-wider">Tim</p>
                            @if($teamMembers->count() > 0)
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ $teamMembers->count() }}</span>
                            @endif
                        </div>
                        @if($volunteer->assignment)
                            <div class="px-5 pb-2">
                                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                                    <i class="bi bi-geo-alt text-[10px]"></i> {{ $volunteer->assignment }}
                                </p>
                            </div>
                        @endif
                        <div class="px-3 pb-4">
                            @if($teamMembers->count() > 0)
                                <div class="space-y-0.5">
                                    @foreach($teamMembers as $member)
                                        @php
                                            $memberColor = match($member->skill) {
                                                'MEDIS' => '#dc2626',
                                                'SAR' => '#1d4ed8',
                                                'LOGISTIK' => '#d97706',
                                                'KONSUMSI' => '#059669',
                                                'PSIKOSOSIAL' => '#7c3aed',
                                                default => '#475569',
                                            };
                                        @endphp
                                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-bold text-white shrink-0"
                                                style="background: {{ $memberColor }};">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[13px] font-medium text-slate-800 truncate">{{ $member->name }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $member->skill }}</p>
                                            </div>
                                            <span class="w-2 h-2 rounded-full {{ ($member->availability ?? 'available') === 'available' ? 'bg-emerald-400' : 'bg-slate-300' }} shrink-0"></span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10 px-4">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                        <i class="bi bi-people text-slate-400"></i>
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        @if($volunteer->assignment)
                                            Belum ada relawan lain di lokasi ini.
                                        @else
                                            Muncul setelah ada penugasan.
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        @endif

        {{-- Menu Layanan --}}
        <x-menu-section :menu="$menu ?? []" />

        {{-- Berita --}}
        <x-news-section :news="$news" />

        {{-- Peta Bencana --}}
        <x-disaster-map />

    </div>

@section('footer')
    <x-footer />
@endsection

@endsection
