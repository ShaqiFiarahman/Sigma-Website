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

    $typeIcon = $d->type_icon;
    $typeColor = $d->type_color;
@endphp

<div class="bg-white border border-slate-200/80 border-l-4 {{ $borderColor }} rounded-xl p-4 sm:p-5"
     style="box-shadow: 0 1px 3px rgba(10,15,30,0.04);">

    <div class="flex items-center gap-2 mb-2">
        @if($d->disaster_type === 'earthquake')
            <svg class="{{ $typeColor }} w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="currentColor">
                <path d="M3 15 L3 8 L8 3 L7.5 6 L9 9 L7 12 L7.5 15 Z" />
                <path d="M8.5 16 L8 13 L10 10 L8.5 7 L9 4.5 L14 9.5 L14 16 Z" />
            </svg>
        @else
            <i class="bi {{ $typeIcon }} {{ $typeColor }} text-base shrink-0"></i>
        @endif
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
