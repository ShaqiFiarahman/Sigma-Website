@props(['shelter'])

@php
    $capParts = explode('/', $shelter['capacity']);
    $capCurrent = (int) $capParts[0];
    $capMax = (int) ($capParts[1] ?? 1);
    $capPercent = $capMax > 0 ? round(($capCurrent / $capMax) * 100) : 0;

    if ($capPercent > 85) {
        $barColor = 'bg-red-500'; $barBg = 'bg-red-100'; $capTextColor = 'text-red-700';
    } elseif ($capPercent > 60) {
        $barColor = 'bg-amber-500'; $barBg = 'bg-amber-100'; $capTextColor = 'text-amber-700';
    } else {
        $barColor = 'bg-emerald-500'; $barBg = 'bg-emerald-100'; $capTextColor = 'text-emerald-700';
    }

    if ($shelter['status'] === 'Penuh') {
        $statusBg = 'bg-red-50 border-red-100'; $statusText = 'text-red-700'; $statusDot = 'bg-red-500'; $statusLabel = 'Penuh';
    } elseif ($capPercent > 85) {
        $statusBg = 'bg-amber-50 border-amber-100'; $statusText = 'text-amber-700'; $statusDot = 'bg-amber-500'; $statusLabel = 'Hampir Penuh';
    } else {
        $statusBg = 'bg-teal-50 border-teal-100'; $statusText = 'text-teal-700'; $statusDot = 'bg-teal-500'; $statusLabel = 'Tersedia';
    }
@endphp

<div class="shelter-card bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6"
     style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);"
     data-name="{{ strtolower($shelter['name']) }}"
     data-status="{{ strtolower($shelter['status']) }}"
     data-lat="{{ $shelter['lat'] }}"
     data-lng="{{ $shelter['lng'] }}">

    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
        <div class="flex-1 min-w-0">

            {{-- Name + Address + Distance --}}
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-slate-900 leading-tight truncate">{{ $shelter['name'] }}</h3>
                    @if(!empty($shelter['address']))
                        <p class="text-xs text-slate-500 mt-0.5 truncate flex items-center gap-1">
                            <i class="bi bi-geo-alt text-slate-400 text-[10px]"></i> {{ $shelter['address'] }}
                        </p>
                    @endif
                    <div class="flex items-center gap-3 mt-1.5 text-xs text-slate-500 shelter-distance-info">
                        <span class="flex items-center gap-1"><i class="bi bi-car-front text-slate-400"></i> <span class="drive-time">—</span></span>
                        <span class="flex items-center gap-1"><i class="bi bi-person-walking text-slate-400"></i> <span class="walk-time">—</span></span>
                        <span class="flex items-center gap-1"><i class="bi bi-geo-alt text-slate-400"></i> <span class="distance-km">—</span></span>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border shrink-0 {{ $statusBg }} {{ $statusText }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span> {{ $statusLabel }}
                </span>
            </div>

            {{-- Capacity --}}
            <div class="mt-3 mb-3">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-medium text-slate-600">Kapasitas</span>
                    <span class="text-xs font-bold {{ $capTextColor }}">{{ $shelter['capacity'] }} orang ({{ $capPercent }}%)</span>
                </div>
                <div class="w-full h-2.5 rounded-full {{ $barBg }} overflow-hidden">
                    <div class="h-full rounded-full {{ $barColor }} transition-all duration-500" style="width: {{ $capPercent }}%"></div>
                </div>
            </div>

            {{-- Logistics --}}
            @if(!empty($shelter['logistics']))
                <div class="mt-3">
                    <span class="text-[11px] font-semibold text-slate-500 block mb-1.5">Dibutuhkan:</span>
                    <div class="flex items-center gap-2 flex-wrap">
                        @foreach($shelter['logistics'] as $item)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-medium border border-teal-100" style="background:#E6FFFA; color:#0d9488;">
                                <i class="bi bi-box-seam text-[10px]"></i> {{ $item }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Divider --}}
        <div class="hidden sm:block w-px bg-slate-200 self-stretch"></div>

        {{-- Actions --}}
        <div class="flex sm:flex-col items-center justify-center gap-2 sm:shrink-0 sm:self-center sm:pl-2">
            <button type="button"
                    onclick="window.open('https://www.google.com/maps/dir/?api=1&destination={{ $shelter['lat'] }},{{ $shelter['lng'] }}', '_blank')"
                    class="inline-flex items-center justify-center gap-1.5 w-full sm:w-40 px-4 py-2.5 text-xs font-semibold text-white rounded-xl transition-all duration-200 cursor-pointer"
                    style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(30,58,138,0.2);">
                <i class="bi bi-signpost-2-fill text-[11px]"></i> Petunjuk Arah
            </button>
            <button type="button"
                    onclick="window.open('https://api.whatsapp.com/send?phone={{ $shelter['contact_phone'] ?? '6285934415914' }}&text={{ urlencode('Halo, saya ingin mengirimkan bantuan logistik ke ' . $shelter['name'] . ' berupa: ' . implode(', ', $shelter['logistics'] ?? [])) }}', '_blank')"
                    class="inline-flex items-center justify-center gap-1.5 w-full sm:w-40 px-4 py-2.5 text-xs font-semibold text-white rounded-xl transition-all duration-200 cursor-pointer"
                    style="background: #25D366; box-shadow: 0 2px 8px rgba(37,211,102,0.2);">
                <i class="bi bi-whatsapp text-[11px]"></i> Hubungi WA
            </button>
        </div>
    </div>
</div>
