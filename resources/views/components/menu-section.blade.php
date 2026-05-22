@props(['menu' => []])

<section class="animate-fade-up mb-8" style="animation-delay: 0.15s;">
    <div class="mb-4 px-1">
        <h2 class="text-lg font-bold text-slate-900">Menu Layanan</h2>
        <p class="text-xs text-slate-500 mt-0.5">Akses cepat layanan SIGMA</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($menu as $item)
            @if(in_array($item['id'] ?? null, [1, 2])) @continue @endif
            @php
                $href = match ($item['id'] ?? null) {
                    1 => route('map'),
                    2 => route('laporan.create'),
                    3 => route('shelter'),
                    5 => route('volunteer.create'),
                    7 => route('search'),
                    10 => route('panduan'),
                    12 => route('volunteer.report.create'),
                    6 => route('laporan.index'),
                    default => '#',
                };
            @endphp
            <div onclick="window.location.href='{{ $href }}'" class="menu-card group cursor-pointer">
                <div class="menu-icon-wrap">
                    <i class="bi {{ $item['icon'] ?? 'bi-box' }}"></i>
                </div>
                <p class="font-bold text-sm mb-1 text-slate-900">{{ $item['title'] ?? 'Menu' }}</p>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $item['description'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
</section>
