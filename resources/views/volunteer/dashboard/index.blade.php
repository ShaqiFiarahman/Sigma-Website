@extends('layouts.app')
@section('title', 'Dashboard Relawan')

@section('content')

    @include('partials._welcome-banner')

    <div class="space-y-6 pb-6">

        {{-- Warning Banner --}}
        @include('partials._warning-banner')

        {{-- Notifikasi Penugasan Baru --}}
        @if($volunteer->assignment && !$volunteer->assignment_notified_at)
            <div class="relative overflow-hidden rounded-2xl px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-lg shadow-blue-500/10 animate-fade-up"
                 style="background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);">
                <div class="absolute top-0 right-0 w-40 h-40 rounded-full opacity-[0.07] pointer-events-none" style="background: white; transform: translate(30%, -40%);"></div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center shrink-0 backdrop-blur-sm border border-white/10">
                        <i class="bi bi-bell-fill text-white text-lg animate-bounce"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white tracking-wide">Penugasan Baru Diterima!</p>
                        <p class="text-xs text-white/80 mt-0.5">Lokasi Tugas: <span class="text-white font-extrabold underline decoration-white/30 decoration-2 underline-offset-2">{{ $volunteer->assignment }}</span></p>
                    </div>
                </div>
                <form action="{{ route('volunteer.dismiss_notification') }}" method="POST" class="shrink-0 relative z-10">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold text-blue-700 bg-white rounded-xl hover:bg-blue-50 transition-all cursor-pointer shadow-lg shadow-blue-900/20 hover:scale-[1.02] active:scale-[0.98]">
                        Saya Mengerti
                    </button>
                </form>
            </div>
        @endif

        {{-- Hero Card: Identity + Availability --}}
        @php
            $skillColor = match($volunteer->skill) {
                'MEDIS' => ['from' => '#dc2626', 'to' => '#ef4444', 'bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => 'bi-heart-pulse-fill'],
                'SAR' => ['from' => '#f97316', 'to' => '#ea580c', 'bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'icon' => 'bi-life-preserver'],
                'LOGISTIK' => ['from' => '#d97706', 'to' => '#f59e0b', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'bi-box-seam-fill'],
                'KONSUMSI' => ['from' => '#059669', 'to' => '#10b981', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'bi-cup-hot-fill'],
                'PSIKOSOSIAL' => ['from' => '#7c3aed', 'to' => '#a78bfa', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => 'bi-chat-heart-fill'],
                'PENDIDIKAN' => ['from' => '#0891b2', 'to' => '#06b6d4', 'bg' => 'bg-cyan-50', 'text' => 'text-cyan-700', 'border' => 'border-cyan-200', 'icon' => 'bi-mortarboard-fill'],
                default => ['from' => '#334155', 'to' => '#64748b', 'bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'bi-person-badge'],
            };
        @endphp
        <div class="relative bg-white/80 backdrop-blur-md rounded-3xl border border-white/60 shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden p-6 animate-fade-up">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-[0.03] pointer-events-none" style="background: {{ $skillColor['from'] }}; transform: translate(10%, -10%);"></div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-lg font-bold shadow-md"
                         style="background: linear-gradient(135deg, {{ $skillColor['from'] }}, {{ $skillColor['to'] }}); shadow-color: {{ $skillColor['from'] }}30;">
                        @if($volunteer->skill === 'SAR')
                            <svg class="w-6.5 h-6.5 shrink-0 shadow-sm rounded-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                            <svg class="w-6.5 h-6.5 shrink-0 shadow-sm rounded-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: {{ $skillColor['from'] }};">
                                <!-- Background Circle -->
                                <circle cx="12" cy="12" r="11" fill="currentColor"/>
                                <!-- Fork (White) -->
                                <path d="M8.5 12v7c0 .6-.4 1-1 1s-1-.4-1-1v-7c-1.2-.3-2-1.3-2-2.5V5c0-.6.4-1 1-1s1 .4 1 1v4h.8V5c0-.6.4-1 1-1s1 .4 1 1v4h.8V5c0-.6.4-1 1-1s1 .4 1 1v4.5c0 1.2-.8 2.2-2 2.5z" fill="#ffffff"/>
                                <!-- Knife (White) -->
                                <path d="M16.5 12v7c0 .6-.4 1-1 1s-1-.4-1-1v-7.2c-.8-.2-1.5-1-1.5-1.8V7.5c0-2 1.5-3.5 3-3.5.3 0 .5.2.5.5v11.7c0 .8-.5 1.5-1 1.8z" fill="#ffffff"/>
                            </svg>
                        @elseif($volunteer->skill === 'PSIKOSOSIAL')
                            <svg class="w-6.5 h-6.5 shrink-0 shadow-sm rounded-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: {{ $skillColor['from'] }};">
                                <!-- Background Purple Circle -->
                                <circle cx="12" cy="12" r="11" fill="currentColor"/>
                                <!-- Big White Hand (Adult Hand) -->
                                <path d="M21.5 14.5c-2.5-1-5-2.8-7.2-5-1.5-1.5-3.5-5-5.2-7.2-0.5-0.6-1.3-0.7-1.8-0.2c-0.5 0.5-0.4 1.3 0.1 1.8c1.5 1.8 3.5 5 4.8 7 -2.2-1.8-4.5-4-6.2-5.5-0.5-0.5-1.3-0.5-1.8 0c-0.5 0.5-0.5 1.3 0 1.8c1.6 1.6 3.8 4 5.8 5.5 -2.8-1.8-5.8-3.2-7.5-4-0.6-0.3-1.4 0-1.7 0.6c-0.3 0.6 0 1.4 0.6 1.7c2 1 4.8 2.5 7.5 4 -3.2-1-6.2-1.5-8-1.5 -0.7 0-1.3 0.6-1.3 1.3c0 0.7 0.6 1.3 1.3 1.3c2.2 0 5.2 0.6 8 1.8 -1.8 0.3-3.5 1.4-4.5 2.5 -0.5 0.6-0.4 1.4 0.2 1.9c0.6 0.5 1.4 0.4 1.9-0.2c1-1.2 2.5-2.2 4.5-2.5 1.4 1.2 2.8 2.8 4.2 4.2c1.4 1.4 3.2 2.2 4.8 2.8h3L21.5 14.5z" fill="#ffffff" />
                                <!-- Small Purple Hand (Child Hand) -->
                                <path d="M16 16.5c-1.5-0.6-3-1.8-4.3-3.2-0.9-0.9-2.1-3-3.1-4.3-0.3-0.4-0.8-0.4-1.1-0.1c-0.3 0.3-0.2 0.8 0.1 1.1c0.9 1.1 2.1 3 2.9 4.2 -1.3-1.1-2.7-2.4-3.7-3.3-0.3-0.3-0.8-0.3-1.1 0c-0.3 0.3-0.3 0.8 0 1.1c1 1 2.3 2.4 3.5 3.3 -1.7-1.1-3.5-1.9-4.5-2.4-0.4-0.2-0.8 0-1 0.4s0 0.8 0.4 1c1.2 0.6 2.9 1.5 4.5 2.4 -1.9-0.6-3.7-0.9-4.8-0.9-0.4 0-0.8 0.4-0.8 0.8s0.4 0.8 0.8 0.8c1.3 0 3.1 0.4 4.8 1.1 -1.1 0.2-2.1 0.8-2.7 1.5 -0.3 0.4-0.2 0.8 0.1 1.1s0.8 0.1 1.1-0.2c0.6-0.7 1.5-1.3 2.7-1.5 0.8 0.7 1.7 1.7 2.5 2.5 0.8 0.8 1.9 1.3 2.9 1.7h1.8L16 16.5z" fill="#7c3aed" />
                            </svg>
                        @else
                            <i class="bi {{ $skillColor['icon'] }}"></i>
                        @endif
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">{{ $volunteer->name }}</h2>
                        </div>
                        <p class="text-[11px] text-slate-400 font-semibold tracking-wide mt-1">
                            Kode: {{ $volunteer->volunteer_code ?? 'REL-' . str_pad($volunteer->id, 5, '0', STR_PAD_LEFT) }} · Terdaftar {{ $volunteer->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
                
                {{-- Availability Toggle --}}
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-500 hidden sm:inline">Status Ketersediaan:</span>
                    <form action="{{ route('volunteer.toggle_availability') }}" method="POST" class="flex bg-slate-200/40 p-1 rounded-2xl border border-slate-200/50 backdrop-blur-sm">
                        @csrf
                        <button type="submit" name="availability" value="available"
                            class="px-4 py-1.5 text-[10px] font-extrabold rounded-xl transition-all cursor-pointer uppercase tracking-wider {{ ($volunteer->availability ?? 'available') === 'available' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'text-slate-400 hover:text-slate-600' }}">
                            Tersedia
                        </button>
                        <button type="submit" name="availability" value="unavailable"
                            class="px-4 py-1.5 text-[10px] font-extrabold rounded-xl transition-all cursor-pointer uppercase tracking-wider {{ ($volunteer->availability ?? 'available') === 'unavailable' ? 'bg-slate-700 text-white shadow-md' : 'text-slate-400 hover:text-slate-600' }}">
                            Tidak Tersedia
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if($volunteer->status === 'APPROVED')

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4 animate-fade-up" style="animation-delay: 0.08s;">
                <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-white/60 p-5 shadow-sm hover:shadow-md transition-all">
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
                <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-white/60 p-5 shadow-sm hover:shadow-md transition-all">
                    <p class="text-3xl font-extrabold text-slate-900 tabular-nums leading-none">{{ $volunteer->assignment ? '1' : '0' }}</p>
                    <p class="text-[12px] text-slate-500 mt-2">Penugasan aktif</p>
                </div>
                <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-white/60 p-5 shadow-sm hover:shadow-md transition-all">
                    <p class="text-3xl font-extrabold text-slate-900 tabular-nums leading-none">{{ $teamMembers->count() }}</p>
                    <p class="text-[12px] text-slate-500 mt-2">Anggota tim</p>
                </div>
            </div>

            {{-- Main Layout Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 animate-fade-up" style="animation-delay: 0.1s;">
                {{-- Left Sektor: Penugasan & Riwayat (3/5) --}}
                <div class="lg:col-span-3 space-y-6">
                    
                    {{-- Box Penugasan --}}
                    <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-white/60 overflow-hidden shadow-sm">
                        <div class="px-4.5 py-2.5 border-b border-slate-100/50">
                            <p class="text-xs font-bold text-slate-450 uppercase tracking-wider">Status Tugas Saat Ini</p>
                        </div>
                        <div class="p-3">
                            @if($volunteer->assignment)
                                <div class="flex flex-col sm:flex-row items-start gap-3 p-3 rounded-xl border border-blue-50/70 bg-blue-50/15 backdrop-blur-sm">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/10"
                                         style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%);">
                                        <i class="bi bi-geo-alt-fill text-white text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[9px] font-bold text-blue-600 uppercase tracking-wider">Lokasi Penugasan</p>
                                        <p class="text-sm font-extrabold text-slate-900 mt-0.5 leading-snug">{{ $volunteer->assignment }}</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">Keahlian: <span class="font-bold text-slate-700">{{ $volunteer->skill }}</span> · Ditugaskan oleh {{ \App\Models\User::whereRaw('LOWER(role) = ?', ['admin'])->first()?->full_name ? 'Admin ' . \App\Models\User::whereRaw('LOWER(role) = ?', ['admin'])->first()->full_name : 'Admin SIGMA' }}</p>
                                        
                                        <div class="flex flex-wrap items-center gap-2 mt-2.5">
                                            <button type="button"
                                                onclick="window.open('https://api.whatsapp.com/send?phone=6285934415914&text={{ urlencode('Halo Admin, saya relawan ' . $volunteer->name . ' (' . ($volunteer->volunteer_code ?? 'REL-' . str_pad($volunteer->id, 5, '0', STR_PAD_LEFT)) . ') ingin bertanya mengenai penugasan di ' . $volunteer->assignment) }}', '_blank')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-white rounded-lg cursor-pointer transition-all hover:scale-[1.02] active:scale-[0.98] shadow-sm shadow-emerald-500/10"
                                                style="background: #25D366;">
                                                <i class="bi bi-whatsapp text-[12px]"></i> Hubungi Admin
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-6 px-4 text-center rounded-xl bg-slate-50/50 border border-slate-100">
                                    {{-- Pulsing Radar Wave Icon --}}
                                    <div class="relative w-12 h-12 flex items-center justify-center mb-3">
                                        <span class="animate-ping absolute inline-flex h-8 w-8 rounded-full bg-slate-200 opacity-75"></span>
                                        <div class="relative w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200">
                                            <i class="bi bi-broadcast text-slate-400 text-sm animate-pulse"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-xs font-bold text-slate-800">Menunggu Penugasan</h3>
                                    <p class="text-[10px] text-slate-400 max-w-xs mt-0.5">Status Anda saat ini aktif. Kami sedang mencari titik bencana yang membutuhkan koordinasi bantuan relawan.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Box Riwayat Laporan --}}
                    <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-white/60 overflow-hidden shadow-sm">
                        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100/50">
                            <p class="text-xs font-bold text-slate-450 uppercase tracking-wider">Aktivitas Laporan Terbaru</p>
                            <a href="{{ route('volunteer.reports') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors">Semua Laporan</a>
                        </div>
                        @if($recentReports->count() > 0)
                            <div class="divide-y divide-slate-100/40">
                                @foreach($recentReports as $report)
                                    <div class="px-6 py-4 flex items-center gap-3 hover:bg-slate-50/40 transition-colors">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-slate-800 truncate">
                                                {{ ucfirst(strtolower($report->skill_type)) }}@if($report->disaster) — <span class="font-medium text-slate-500">{{ \Illuminate\Support\Str::limit($report->disaster->title, 30) }}</span>@endif
                                            </p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">Dibuat {{ $report->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                        <p class="text-xs text-slate-400 font-medium shrink-0">{{ $report->created_at->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-8 text-center flex flex-col items-center justify-center">
                                <p class="text-sm text-slate-400 mb-3">Anda belum pernah mengirimkan laporan aktivitas lapangan.</p>
                                <a href="{{ route('volunteer.report.create') }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white rounded-xl transition-all hover:scale-[1.02] active:scale-[0.98] shadow-md"
                                    style="background: linear-gradient(135deg, {{ $skillColor['from'] }}, {{ $skillColor['to'] }});">
                                    <i class="bi bi-plus-lg text-[10px]"></i> Buat Laporan Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Sektor: Roster Tim Lapangan (2/5) --}}
                <div class="lg:col-span-2">
                    <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-white/60 overflow-hidden shadow-sm h-full flex flex-col">
                        <div class="px-5 py-4 flex items-center justify-between border-b border-slate-100/50">
                            <p class="text-xs font-bold text-slate-450 uppercase tracking-wider">Tim Lapangan</p>
                            @if($teamMembers->count() > 0)
                                <span class="text-[10px] font-extrabold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ $teamMembers->count() }} Relawan</span>
                            @endif
                        </div>
                        @if($volunteer->assignment)
                            <div class="px-5 py-3 bg-slate-50/50 border-b border-slate-100/30">
                                <p class="text-xs font-semibold text-slate-500 flex items-center gap-1.5"><i class="bi bi-geo-alt-fill text-blue-600 text-[10px]"></i> Lokasi: <span class="text-slate-800 font-bold">{{ $volunteer->assignment }}</span></p>
                            </div>
                        @endif
                        <div class="p-3 flex-1">
                            @if($teamMembers->count() > 0)
                                <div class="space-y-1">
                                    @foreach($teamMembers as $member)
                                        @php
                                            $memberColor = match($member->skill) {
                                                'MEDIS' => '#dc2626', 'SAR' => '#f97316', 'LOGISTIK' => '#d97706',
                                                'KONSUMSI' => '#059669', 'PSIKOSOSIAL' => '#7c3aed', 'PENDIDIKAN' => '#0891b2',
                                                default => '#475569',
                                            };
                                        @endphp
                                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50/80 border border-transparent hover:border-slate-100/55 transition-all">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-[11px] font-extrabold text-white shrink-0 shadow-sm" style="background: {{ $memberColor }};">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <p class="text-[13px] font-bold text-slate-800 truncate">{{ $member->name }}</p>
                                                    <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded uppercase border shrink-0" style="background: {{ $memberColor }}10; color: {{ $memberColor }}; border-color: {{ $memberColor }}20;">
                                                        {{ $member->skill }}
                                                    </span>
                                                </div>
                                                <p class="text-[10px] text-slate-400 mt-0.5">Status: <span class="font-semibold {{ ($member->availability ?? 'available') === 'available' ? 'text-emerald-600' : 'text-slate-500' }}">{{ ($member->availability ?? 'available') === 'available' ? 'Tersedia' : 'Sibuk' }}</span></p>
                                            </div>
                                            <span class="relative flex h-2 w-2 shrink-0">
                                                @if(($member->availability ?? 'available') === 'available')
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                                @else
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-slate-300"></span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12 px-4 flex flex-col items-center justify-center h-full">
                                    <i class="bi bi-people text-3xl text-slate-300 mb-3"></i>
                                    <p class="text-xs text-slate-400 font-medium">
                                        @if($volunteer->assignment) Belum ada relawan lain yang ditugaskan di posko ini.
                                        @else Tim relawan akan muncul di sini setelah Anda mendapatkan penugasan. @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Berita --}}
        @include('partials._news-section')

        {{-- Menu Layanan --}}
        @include('partials._menu-section')

        {{-- Peta Bencana --}}
        <x-disaster-map />
    </div>

@section('footer')
    <x-footer />
@endsection

@section('scripts')
<script>
    // News scroll indicators
    const newsScroll = document.querySelector('.news-scroll');
    const indicatorsContainer = document.getElementById('newsIndicators');

    if (newsScroll && indicatorsContainer) {
        for (let i = 0; i < 3; i++) {
            const dot = document.createElement('div');
            dot.className = `w-2 h-2 rounded-full transition-all duration-300 cursor-pointer ${i === 0 ? 'bg-[#2B52C3] w-8' : 'bg-slate-300'}`;
            dot.addEventListener('click', () => {
                const maxScroll = newsScroll.scrollWidth - newsScroll.clientWidth;
                newsScroll.scrollTo({ left: i === 0 ? 0 : i === 1 ? maxScroll * 0.5 : maxScroll, behavior: 'smooth' });
            });
            indicatorsContainer.appendChild(dot);
        }

        const dots = indicatorsContainer.querySelectorAll('div');

        newsScroll.addEventListener('wheel', (e) => {
            if (e.deltaY !== 0) {
                const maxScroll = newsScroll.scrollWidth - newsScroll.clientWidth;
                if (maxScroll > 0) {
                    const atStart = newsScroll.scrollLeft <= 0;
                    const atEnd = newsScroll.scrollLeft >= maxScroll - 1;
                    if ((e.deltaY < 0 && atStart) || (e.deltaY > 0 && atEnd)) {
                        return;
                    }
                    e.preventDefault();
                    newsScroll.scrollBy({ left: e.deltaY * 2.5, behavior: 'smooth' });
                }
            }
        }, { passive: false });

        newsScroll.addEventListener('scroll', () => {
            const maxScroll = newsScroll.scrollWidth - newsScroll.clientWidth;
            let activeIndex = 0;
            if (maxScroll > 0) {
                const pct = newsScroll.scrollLeft / maxScroll;
                if (pct > 0.33 && pct <= 0.66) activeIndex = 1;
                else if (pct > 0.66) activeIndex = 2;
            }
            dots.forEach((dot, i) => {
                dot.className = i === activeIndex ? 'w-8 h-2 rounded-full bg-[#2B52C3] transition-all duration-300' : 'w-2 h-2 rounded-full bg-slate-300 transition-all duration-300';
            });
        });
    }
</script>
@endsection

@endsection
