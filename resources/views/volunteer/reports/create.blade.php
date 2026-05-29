@extends('layouts.app')
@section('title', 'Kirim Laporan Tugas')

@section('subtitle')
    Isi detail aktivitas lapangan Anda untuk posko <span
        class="text-blue-600 font-bold">{{ $volunteer->assignment ?? 'Relawan SIGMA' }}</span>.
@endsection

@section('page-actions')
    <x-ui.back-button :route="route('dashboard')" label="Kembali" />
@endsection

@section('content')

    @php
        $skillColor = match ($volunteer->skill) {
            'MEDIS' => ['from' => '#dc2626', 'to' => '#ef4444', 'bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => 'bi-heart-pulse-fill'],
            'SAR' => ['from' => '#f97316', 'to' => '#ea580c', 'bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'icon' => 'bi-life-preserver'],
            'LOGISTIK' => ['from' => '#d97706', 'to' => '#f59e0b', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'bi-box-seam-fill'],
            'KONSUMSI' => ['from' => '#059669', 'to' => '#10b981', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'bi-cup-hot-fill'],
            'PSIKOSOSIAL' => ['from' => '#7c3aed', 'to' => '#a78bfa', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => 'bi-chat-heart-fill'],
            default => ['from' => '#334155', 'to' => '#64748b', 'bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'bi-person-badge'],
        };
    @endphp

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 animate-fade-up">
            <p class="font-bold mb-1 flex items-center gap-2"><i class="bi bi-exclamation-circle-fill"></i> Terjadi kesalahan
                pengisian:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs text-red-650 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-6xl mx-auto relative z-10 mb-10">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- Left: Form (3/5) --}}
            <div class="lg:col-span-3">
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden animate-fade-up"
                    style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

                    {{-- Form header --}}
                    <div class="pl-6 pr-8 py-5 border-l-4 border-l-[#3B6FE8] border-b border-slate-100 bg-white">
                        <div class="flex items-center gap-2.5">
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
                                <svg class="w-5.5 h-5.5 shrink-0 shadow-sm rounded-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: {{ $skillColor['from'] }};">
                                    <!-- Background Circle -->
                                    <circle cx="12" cy="12" r="11" fill="currentColor"/>
                                    <!-- Fork (White) -->
                                    <path d="M8.5 12v7c0 .6-.4 1-1 1s-1-.4-1-1v-7c-1.2-.3-2-1.3-2-2.5V5c0-.6.4-1 1-1s1 .4 1 1v4h.8V5c0-.6.4-1 1-1s1 .4 1 1v4h.8V5c0-.6.4-1 1-1s1 .4 1 1v4.5c0 1.2-.8 2.2-2 2.5z" fill="#ffffff"/>
                                    <!-- Knife (White) -->
                                    <path d="M16.5 12v7c0 .6-.4 1-1 1s-1-.4-1-1v-7.2c-.8-.2-1.5-1-1.5-1.8V7.5c0-2 1.5-3.5 3-3.5.3 0 .5.2.5.5v11.7c0 .8-.5 1.5-1 1.8z" fill="#ffffff"/>
                                </svg>
                            @elseif($volunteer->skill === 'PSIKOSOSIAL')
                                <svg class="w-5.5 h-5.5 shrink-0 shadow-sm rounded-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: {{ $skillColor['from'] }};">
                                    <!-- Background Purple Circle -->
                                    <circle cx="12" cy="12" r="11" fill="currentColor"/>
                                    <!-- Big White Hand (Adult Hand) -->
                                    <path d="M21.5 14.5c-2.5-1-5-2.8-7.2-5-1.5-1.5-3.5-5-5.2-7.2-0.5-0.6-1.3-0.7-1.8-0.2c-0.5 0.5-0.4 1.3 0.1 1.8c1.5 1.8 3.5 5 4.8 7 -2.2-1.8-4.5-4-6.2-5.5-0.5-0.5-1.3-0.5-1.8 0c-0.5 0.5-0.5 1.3 0 1.8c1.6 1.6 3.8 4 5.8 5.5 -2.8-1.8-5.8-3.2-7.5-4-0.6-0.3-1.4 0-1.7 0.6c-0.3 0.6 0 1.4 0.6 1.7c2 1 4.8 2.5 7.5 4 -3.2-1-6.2-1.5-8-1.5 -0.7 0-1.3 0.6-1.3 1.3c0 0.7 0.6 1.3 1.3 1.3c2.2 0 5.2 0.6 8 1.8 -1.8 0.3-3.5 1.4-4.5 2.5 -0.5 0.6-0.4 1.4 0.2 1.9c0.6 0.5 1.4 0.4 1.9-0.2c1-1.2 2.5-2.2 4.5-2.5 1.4 1.2 2.8 2.8 4.2 4.2c1.4 1.4 3.2 2.2 4.8 2.8h3L21.5 14.5z" fill="#ffffff" />
                                    <!-- Small Purple Hand (Child Hand) -->
                                    <path d="M16 16.5c-1.5-0.6-3-1.8-4.3-3.2-0.9-0.9-2.1-3-3.1-4.3-0.3-0.4-0.8-0.4-1.1-0.1c-0.3 0.3-0.2 0.8 0.1 1.1c0.9 1.1 2.1 3 2.9 4.2 -1.3-1.1-2.7-2.4-3.7-3.3-0.3-0.3-0.8-0.3-1.1 0c-0.3 0.3-0.3 0.8 0 1.1c1 1 2.3 2.4 3.5 3.3 -1.7-1.1-3.5-1.9-4.5-2.4-0.4-0.2-0.8 0-1 0.4s0 0.8 0.4 1c1.2 0.6 2.9 1.5 4.5 2.4 -1.9-0.6-3.7-0.9-4.8-0.9-0.4 0-0.8 0.4-0.8 0.8s0.4 0.8 0.8 0.8c1.3 0 3.1 0.4 4.8 1.1 -1.1 0.2-2.1 0.8-2.7 1.5 -0.3 0.4-0.2 0.8 0.1 1.1s0.8 0.1 1.1-0.2c0.6-0.7 1.5-1.3 2.7-1.5 0.8 0.7 1.7 1.7 2.5 2.5 0.8 0.8 1.9 1.3 2.9 1.7h1.8L16 16.5z" fill="#7c3aed" />
                                </svg>
                            @else
                                <i class="bi {{ $skillColor['icon'] }} text-lg shrink-0" style="color: {{ $skillColor['from'] }};"></i>
                            @endif
                            <div>
                                <h2 class="text-base font-bold text-slate-800 leading-tight">Laporan Aktivitas
                                    {{ ucfirst(strtolower($volunteer->skill)) }}</h2>
                                <p class="text-xs text-slate-500 mt-0.5 font-semibold">{{ $volunteer->name }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('volunteer.report.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="p-6 space-y-5">
                            {{-- Pilih Bencana --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Terkait Bencana <span class="text-red-500 font-bold">*</span>
                                </label>
                                <select name="disaster_id" required
                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all text-slate-800 font-semibold bg-slate-50 focus:bg-white">
                                    <option value="">— Pilih Bencana —</option>
                                    @foreach($disasters as $disaster)
                                        <option value="{{ $disaster->id }}">{{ $disaster->title }} —
                                            {{ \Illuminate\Support\Str::limit($disaster->location ?? 'Lokasi tidak diketahui', 45) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Dynamic Fields per Skill --}}
                            <div class="border-t border-slate-100/60 pt-5">
                                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-4">Data Laporan</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($fields as $field)
                                        @php
                                            $isOptional = isset($field['optional']) && $field['optional'];
                                            $reqAttr = $isOptional ? '' : 'required';
                                        @endphp
                                        @if($field['type'] === 'textarea')
                                            </div>
                                            <div class="mt-4">
                                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                                    {{ $field['label'] }}
                                                    @if($isOptional)
                                                        <span class="text-slate-400 font-normal text-[10px] normal-case">(opsional)</span>
                                                    @endif
                                                </label>
                                                <textarea name="data[{{ $field['name'] }}]" rows="3" {{ $reqAttr }}
                                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all resize-y text-slate-800 font-semibold bg-slate-50 focus:bg-white placeholder:text-slate-300"
                                                    placeholder="Tulis {{ strtolower($field['label']) }}...">{{ old('data.' . $field['name']) }}</textarea>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                        @elseif($field['type'] === 'select')
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                                    {{ $field['label'] }}
                                                    @if($isOptional)
                                                        <span
                                                            class="text-slate-400 font-normal text-[10px] normal-case">(opsional)</span>
                                                    @endif
                                                </label>
                                                <select name="data[{{ $field['name'] }}]" {{ $reqAttr }}
                                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all font-semibold text-slate-800 bg-slate-50 focus:bg-white">
                                                    <option value="">— Pilih —</option>
                                                    @foreach($field['options'] as $opt)
                                                        <option value="{{ $opt }}" {{ old('data.' . $field['name']) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @else
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                                    {{ $field['label'] }}
                                                    @if($isOptional)
                                                        <span
                                                            class="text-slate-400 font-normal text-[10px] normal-case">(opsional)</span>
                                                    @endif
                                                </label>
                                                <input type="{{ $field['type'] }}" name="data[{{ $field['name'] }}]" {{ $reqAttr }}
                                                    value="{{ old('data.' . $field['name']) }}"
                                                    min="{{ $field['type'] === 'number' ? '0' : '' }}"
                                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all text-slate-800 font-semibold bg-slate-50 focus:bg-white"
                                                    placeholder="{{ $field['type'] === 'number' ? '0' : 'Masukkan ' . strtolower($field['label']) . '...' }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            {{-- Catatan Tambahan --}}
                            <div class="border-t border-slate-100/60 pt-5">
                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Catatan Tambahan <span
                                        class="text-slate-400 font-normal text-[10px] normal-case">(opsional)</span>
                                </label>
                                <textarea name="notes" rows="3"
                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all resize-y text-slate-800 font-semibold bg-slate-50 focus:bg-white placeholder:text-slate-300"
                                    placeholder="Catatan atau informasi tambahan...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        {{-- Actions/Footer --}}
                        <div class="px-6 py-4.5 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl"
                            style="background: #FAFBFD;">
                            <a href="{{ route('dashboard') }}"
                                class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-blue-50 hover:text-blue-600 hover:border-blue-300 transition-all duration-200">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-2"
                                style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(30,58,138,0.3);">
                                <i class="bi bi-send-fill text-[11px]"></i> Kirim Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right: Sidebar (2/5) --}}
            <div class="lg:col-span-2">
                <div class="space-y-4 lg:sticky lg:top-24">

                    {{-- 1. Statistik Laporan --}}
                    @php
                        $totalReportsCount = \App\Models\VolunteerReport::where('volunteer_id', $volunteer->id)->count();
                        $monthReportsCount = \App\Models\VolunteerReport::where('volunteer_id', $volunteer->id)->whereMonth('created_at', now()->month)->count();
                    @endphp
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
                        style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                                <i class="bi bi-bar-chart-fill" style="color: #3B6FE8;"></i> Statistik Laporan Anda
                            </h3>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-650 font-medium">Total Laporan</span>
                                <span class="font-bold text-blue-600 text-sm">{{ $totalReportsCount }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-650 font-medium">Laporan Bulan Ini</span>
                                <span class="font-bold text-blue-600 text-sm">{{ $monthReportsCount }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Riwayat Laporan Tugas --}}
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
                        style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                                <i class="bi bi-clock-history" style="color: #3B6FE8;"></i> Riwayat Laporan Tugas
                            </h3>
                        </div>

                        <div class="p-4 max-h-[260px] overflow-y-auto space-y-3">
                            @if($recentReports->count() > 0)
                                <div class="space-y-3">
                                    @foreach($recentReports as $report)
                                        <div
                                            class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 hover:bg-blue-50/20 hover:border-blue-200/30 transition-all duration-200">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-slate-700">
                                                        {{ ucfirst(strtolower($report->skill_type)) }}@if($report->disaster) · <span
                                                        class="text-slate-500 font-medium">{{ \Illuminate\Support\Str::limit($report->disaster->title, 22) }}</span>@endif
                                                    </p>
                                                    <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                                        <i class="bi bi-calendar3"></i>
                                                        {{ $report->created_at->format('d M Y, H:i') }}
                                                    </p>
                                                </div>
                                                <span
                                                    class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 shrink-0 border border-blue-100/30">
                                                    {{ $report->created_at->diffForHumans() }}
                                                </span>
                                            </div>

                                            @if($report->notes)
                                                <p
                                                    class="text-[11px] text-slate-600 bg-white/80 p-2 rounded-lg mt-2 font-medium border border-slate-100/50 line-clamp-2">
                                                    {{ $report->notes }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 px-4 flex flex-col items-center justify-center">
                                    <i class="bi bi-journal-x text-3xl text-slate-300 mb-2"></i>
                                    <p class="text-xs text-slate-400 font-bold">Belum ada riwayat laporan tugas.</p>
                                    <p class="text-[10px] text-slate-400 max-w-xs mt-1 text-center leading-relaxed">Laporan
                                        tugas yang Anda kirimkan melalui formulir di samping akan tercatat di sini.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Panduan Cepat --}}
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
                        style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                                <i class="bi bi-lightbulb-fill text-amber-500"></i> Panduan Cepat
                            </h3>
                        </div>
                        <div class="p-4 space-y-3.5">
                            <div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Pilih Bencana Sesuai</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Pastikan Anda memilih bencana terkait
                                    agar koordinasi posko lapangan berjalan tepat sasaran.</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Catatan Tambahan</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Gunakan kolom catatan untuk
                                    menyampaikan kendala tak terduga atau bantuan ekstra yang mendesak di lapangan.</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Verifikasi Sistem</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Laporan lapangan yang Anda kirimkan
                                    akan direkam dan disinkronisasikan langsung ke sistem utama SIGMA.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection