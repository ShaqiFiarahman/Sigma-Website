@extends('layouts.app')
@section('title', 'Cari & Filter Bencana')

@section('content')

{{-- Back link --}}
<div class="mb-4">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold transition-colors hover:opacity-70" style="color: #6650a4;">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<h1 class="text-2xl font-bold mb-5" style="color: #1D1B20;">Cari & Filter Bencana</h1>

{{-- Search Bar --}}
<div class="relative mb-4">
    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
    <input type="text" id="searchInput"
           placeholder="Cari lokasi atau jenis bencana..."
           value="{{ request('q') }}"
           class="w-full pl-11 pr-4 py-3.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all placeholder:text-slate-400 text-slate-900 bg-white">
</div>

{{-- Filter Chips --}}
<div class="flex items-center gap-2 mb-5 flex-wrap">
    <div class="flex items-center gap-1.5 text-sm font-bold text-slate-700">
        <i class="bi bi-funnel-fill" style="color: #6650a4;"></i> Filter Status:
    </div>
    <button type="button" data-filter="all"
            class="filter-chip active px-3 py-1.5 text-xs font-bold rounded-full border transition-all">
        Semua
    </button>
    <button type="button" data-filter="AWAS"
            class="filter-chip px-3 py-1.5 text-xs font-bold rounded-full border transition-all">
        🔴 Awas
    </button>
    <button type="button" data-filter="SIAGA_1"
            class="filter-chip px-3 py-1.5 text-xs font-bold rounded-full border transition-all">
        🔵 Siaga 1
    </button>
    <button type="button" data-filter="SIAGA_2"
            class="filter-chip px-3 py-1.5 text-xs font-bold rounded-full border transition-all">
        ⚫ Siaga 2
    </button>
    <button type="button" data-filter="RESOLVED"
            class="filter-chip px-3 py-1.5 text-xs font-bold rounded-full border transition-all">
        ✅ Resolved
    </button>
    <button type="button" data-filter="PENDING"
            class="filter-chip px-3 py-1.5 text-xs font-bold rounded-full border transition-all">
        🟡 Pending
    </button>
</div>

{{-- Results --}}
<div id="results" class="space-y-3">
    @forelse($disasters as $d)
        <a href="{{ route('laporan.show', $d->id) }}"
           class="disaster-card block bg-white border border-slate-200/60 rounded-2xl p-4 transition-all hover:shadow-md hover:border-purple-300"
           data-status="{{ $d->status }}"
           data-title="{{ strtolower($d->title) }}"
           data-reporter="{{ strtolower($d->reporter_name) }}"
           style="background: rgba(231, 224, 236, 0.2);">

            <div class="flex items-start justify-between gap-3 mb-2">
                <h3 class="text-base font-extrabold" style="color: #1D1B20;">{{ $d->title }}</h3>
                @php
                    $badgeStyle = match($d->status) {
                        'AWAS'     => 'background:#FFEBEE; color:#B71C1C;',
                        'SIAGA_1'  => 'background:#E3F2FD; color:#0D47A1;',
                        'SIAGA_2'  => 'background:#F5F5F5; color:#424242;',
                        'RESOLVED' => 'background:#E8F5E9; color:#1B5E20;',
                        'PENDING'  => 'background:#FFF3E0; color:#E65100;',
                        default    => 'background:#F5F5F5; color:#424242;',
                    };
                @endphp
                <span class="text-xs font-bold px-2.5 py-1 rounded-full shrink-0" style="{{ $badgeStyle }}">
                    {{ $d->status_label }}
                </span>
            </div>

            <p class="text-sm text-slate-600 mb-1">
                <i class="bi bi-geo-alt-fill" style="color: #6650a4;"></i>
                Lat: {{ number_format($d->latitude, 4) }}, Long: {{ number_format($d->longitude, 4) }}
            </p>
            <p class="text-xs text-slate-400">
                <i class="bi bi-clock"></i> {{ $d->created_at->format('d M Y, H:i') }}
                &middot; <i class="bi bi-person"></i> {{ $d->reporter_name }}
            </p>
        </a>
    @empty
        <div id="emptyState" class="text-center py-16 text-slate-400">
            <i class="bi bi-search text-4xl mb-3 block" style="color: #CAC4D0;"></i>
            <p class="text-sm font-medium text-slate-500">Tidak ada data bencana</p>
            <p class="text-xs mt-1">Belum ada laporan bencana yang tersedia.</p>
        </div>
    @endforelse
</div>

{{-- No results message (hidden by default) --}}
<div id="noResults" class="hidden text-center py-16 text-slate-400">
    <i class="bi bi-emoji-frown text-4xl mb-3 block" style="color: #CAC4D0;"></i>
    <p class="text-sm font-medium text-slate-500">Tidak ditemukan</p>
    <p class="text-xs mt-1">Coba ubah kata kunci atau filter.</p>
</div>

<style>
    .filter-chip {
        background: #FFFFFF;
        border-color: #CAC4D0;
        color: #625b71;
    }
    .filter-chip:hover {
        border-color: #6650a4;
        color: #6650a4;
    }
    .filter-chip.active {
        background: #EADDFF;
        border-color: #6650a4;
        color: #6650a4;
    }
</style>

@endsection

@section('scripts')
<script>
    const searchInput = document.getElementById('searchInput');
    const cards       = document.querySelectorAll('.disaster-card');
    const noResults   = document.getElementById('noResults');
    const chips       = document.querySelectorAll('.filter-chip');

    let activeFilter = 'all';

    // Filter chips
    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeFilter = chip.dataset.filter;
            filterCards();
        });
    });

    // Search input
    searchInput.addEventListener('input', filterCards);

    function filterCards() {
        const query = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        cards.forEach(card => {
            const title    = card.dataset.title || '';
            const reporter = card.dataset.reporter || '';
            const status   = card.dataset.status || '';

            const matchSearch = !query || title.includes(query) || reporter.includes(query);
            const matchFilter = activeFilter === 'all' || status === activeFilter;

            if (matchSearch && matchFilter) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        noResults.classList.toggle('hidden', visibleCount > 0 || cards.length === 0);
    }
</script>
@endsection
