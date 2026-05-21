@props(['disaster'])

@php
    $d = $disaster;
    $borderColor = match($d->status) {
        'AWAS'     => 'border-l-red-500',
        'SIAGA_1'  => 'border-l-orange-500',
        'SIAGA_2'  => 'border-l-violet-400',
        'RESOLVED' => 'border-l-emerald-500',
        'PENDING'  => 'border-l-amber-400',
        default    => 'border-l-slate-300',
    };

    $titleLower = strtolower($d->title);
    if (str_contains($titleLower, 'banjir')) { $typeIcon = 'bi-water'; $typeColor = 'text-blue-500'; }
    elseif (str_contains($titleLower, 'kebakaran') || str_contains($titleLower, 'api')) { $typeIcon = 'bi-fire'; $typeColor = 'text-red-500'; }
    elseif (str_contains($titleLower, 'gempa')) { $typeIcon = 'bi-globe-americas'; $typeColor = 'text-emerald-500'; }
    elseif (str_contains($titleLower, 'longsor')) { $typeIcon = 'bi-layers'; $typeColor = 'text-amber-600'; }
    elseif (str_contains($titleLower, 'tsunami')) { $typeIcon = 'bi-tsunami'; $typeColor = 'text-cyan-500'; }
    else { $typeIcon = 'bi-exclamation-triangle'; $typeColor = 'text-slate-500'; }
@endphp

<div class="bg-white border border-slate-200/80 border-l-4 {{ $borderColor }} rounded-xl p-4 sm:p-5"
     style="box-shadow: 0 1px 3px rgba(10,15,30,0.04);">

    <div class="flex items-center gap-2 mb-2">
        <i class="bi {{ $typeIcon }} {{ $typeColor }} text-base shrink-0"></i>
        <h3 class="text-sm font-bold text-slate-900 truncate">{{ $d->title }}</h3>
    </div>

    <p class="text-xs text-slate-500 line-clamp-2 mb-2.5">{{ \Illuminate\Support\Str::limit($d->description, 100) }}</p>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-xs text-slate-500 mb-3">
        @if($d->location)
            <span class="flex items-center gap-1">
                <i class="bi bi-geo-alt text-slate-400"></i> {{ \Illuminate\Support\Str::limit($d->location, 40) }}
            </span>
        @else
            <span class="flex items-center gap-1">
                <i class="bi bi-geo-alt text-slate-400"></i> {{ number_format($d->latitude, 4) }}, {{ number_format($d->longitude, 4) }}
            </span>
        @endif
        <span class="flex items-center gap-1">
            <i class="bi bi-clock text-slate-400"></i> {{ $d->created_at->format('d M Y, H:i') }}
        </span>
        <span class="flex items-center gap-1">
            <i class="bi bi-person text-slate-400"></i> {{ $d->reporter_name }}
        </span>
    </div>

    <div class="flex items-center gap-2">
        <button type="button"
                onclick="window.open('https://www.google.com/maps?q={{ $d->latitude }},{{ $d->longitude }}', '_blank')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-all cursor-pointer">
            <i class="bi bi-map text-[10px]"></i> Lihat Peta
        </button>
        <button type="button"
                onclick="window.location.href='{{ route('laporan.show', $d->id) }}'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold text-white rounded-lg transition-all cursor-pointer"
                style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 6px rgba(30,58,138,0.2);">
            <i class="bi bi-file-text text-[10px]"></i> Detail
        </button>
    </div>
</div>
