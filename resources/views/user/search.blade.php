@extends('layouts.app')
@section('title', 'Cari & Filter Bencana')
@section('subtitle', 'Temukan laporan berdasarkan lokasi atau jenis bencana.')

@section('page-actions')
    <button type="button" onclick="window.location.href='{{ route('dashboard') }}'" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 shadow-sm cursor-pointer">
        <i class="bi bi-arrow-left text-xs"></i> Kembali
    </button>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Flash Messages --}}
    @if(session('msg'))
        <div class="mb-6 p-4 rounded-2xl flex items-center gap-3 text-sm font-medium backdrop-blur-sm animate-fade-up
            {{ session('msg') == 'approved' ? 'bg-emerald-50/80 text-emerald-800 border border-emerald-200/60' : '' }}
            {{ session('msg') == 'rejected' ? 'bg-red-50/80 text-red-800 border border-red-200/60' : '' }}
            {{ session('msg') == 'created' ? 'bg-blue-50/80 text-blue-800 border border-blue-200/60' : '' }}
        ">
            @if(session('msg') == 'approved')
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                    <i class="bi bi-check-circle-fill text-emerald-500"></i>
                </div>
                <span>Laporan berhasil diverifikasi.</span>
            @elseif(session('msg') == 'rejected')
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                    <i class="bi bi-x-circle-fill text-red-500"></i>
                </div>
                <span>Laporan berhasil ditolak.</span>
            @elseif(session('msg') == 'created')
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                    <i class="bi bi-info-circle-fill text-blue-500"></i>
                </div>
                <span>Laporan baru berhasil dibuat.</span>
            @endif
            <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100 transition-opacity p-1 rounded-lg hover:bg-black/5"><i class="bi bi-x-lg text-xs"></i></button>
        </div>
    @endif

    {{-- Search & Filter --}}
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden mb-5"
         style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">
        <div class="p-4 sm:p-5 space-y-4">

            {{-- Search Bar --}}
            <div class="relative">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="searchInput"
                       placeholder="Cari lokasi atau jenis bencana..."
                       value="{{ request('q') }}"
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-slate-400 text-slate-800 bg-slate-50 focus:bg-white">
            </div>

            {{-- Filter Chips --}}
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" data-filter="all" class="filter-chip active px-3.5 py-1.5 text-xs font-semibold rounded-full border transition-all duration-200">
                    Semua
                </button>
                <button type="button" data-filter="AWAS" class="filter-chip px-3.5 py-1.5 text-xs font-semibold rounded-full border transition-all duration-200">
                    <span class="inline-block w-2 h-2 rounded-full bg-red-500 mr-1"></span>Awas
                </button>
                <button type="button" data-filter="SIAGA_1" class="filter-chip px-3.5 py-1.5 text-xs font-semibold rounded-full border transition-all duration-200">
                    <span class="inline-block w-2 h-2 rounded-full bg-orange-500 mr-1"></span>Siaga 1
                </button>
                <button type="button" data-filter="SIAGA_2" class="filter-chip px-3.5 py-1.5 text-xs font-semibold rounded-full border transition-all duration-200">
                    <span class="inline-block w-2 h-2 rounded-full bg-violet-500 mr-1"></span>Siaga 2
                </button>
                <button type="button" data-filter="RESOLVED" class="filter-chip px-3.5 py-1.5 text-xs font-semibold rounded-full border transition-all duration-200">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1"></span>Resolved
                </button>
                @if(strtolower(auth()->user()->role ?? '') === 'admin' || $disasters->contains('status', 'PENDING'))
                    <button type="button" data-filter="PENDING" class="filter-chip px-3.5 py-1.5 text-xs font-semibold rounded-full border transition-all duration-200">
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-1"></span>Pending
                    </button>
                @endif
                @if(strtolower(auth()->user()->role ?? '') === 'admin' || $disasters->contains('status', 'DECLINE'))
                    <button type="button" data-filter="DECLINE" class="filter-chip px-3.5 py-1.5 text-xs font-semibold rounded-full border transition-all duration-200">
                        <span class="inline-block w-2 h-2 rounded-full bg-slate-400 mr-1"></span>Ditolak
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Result Count --}}
    <div class="flex items-center justify-between mb-4 px-1">
        <p class="text-sm text-slate-500" id="resultCount">
            <span class="font-semibold text-slate-700">{{ $disasters->count() }}</span> laporan ditemukan
        </p>
    </div>

    {{-- List View --}}
    <div id="listView" class="space-y-3">
        @forelse($disasters as $d)
            @php
                $borderColor = match($d->status) {
                    'AWAS'     => 'border-l-red-500',
                    'SIAGA_1'  => 'border-l-blue-500',
                    'SIAGA_2'  => 'border-l-slate-400',
                    'RESOLVED' => 'border-l-emerald-500',
                    'PENDING'  => 'border-l-amber-400',
                    'DECLINE'  => 'border-l-slate-300',
                    default    => 'border-l-slate-300',
                };
                $statusLabel = match($d->status) {
                    'AWAS'     => 'Awas',
                    'SIAGA_1'  => 'Siaga 1',
                    'SIAGA_2'  => 'Siaga 2',
                    'RESOLVED' => 'Resolved',
                    'PENDING'  => 'Pending',
                    'DECLINE'  => 'Ditolak',
                    default    => $d->status,
                };
                $badgeBg = match($d->status) {
                    'AWAS'     => 'bg-red-50 text-red-700 border-red-100',
                    'SIAGA_1'  => 'bg-blue-50 text-blue-700 border-blue-100',
                    'SIAGA_2'  => 'bg-slate-100 text-slate-700 border-slate-200',
                    'RESOLVED' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                    'PENDING'  => 'bg-amber-50 text-amber-700 border-amber-200',
                    'DECLINE'  => 'bg-slate-50 text-slate-500 border-slate-200',
                    default    => 'bg-slate-50 text-slate-600 border-slate-200',
                };
                // Detect disaster type from title
                $titleLower = strtolower($d->title);
                if (str_contains($titleLower, 'banjir')) { $typeIcon = 'bi-water'; $typeColor = 'text-blue-500'; }
                elseif (str_contains($titleLower, 'kebakaran') || str_contains($titleLower, 'api')) { $typeIcon = 'bi-fire'; $typeColor = 'text-red-500'; }
                elseif (str_contains($titleLower, 'gempa')) { $typeIcon = 'bi-globe-americas'; $typeColor = 'text-emerald-500'; }
                elseif (str_contains($titleLower, 'longsor')) { $typeIcon = 'bi-layers'; $typeColor = 'text-amber-600'; }
                elseif (str_contains($titleLower, 'tsunami')) { $typeIcon = 'bi-tsunami'; $typeColor = 'text-cyan-500'; }
                else { $typeIcon = 'bi-exclamation-triangle'; $typeColor = 'text-slate-500'; }
            @endphp

            <div class="disaster-card bg-white border border-slate-200/80 border-l-4 {{ $borderColor }} rounded-xl p-4 sm:p-5"
                 style="box-shadow: 0 1px 3px rgba(10,15,30,0.04);"
                 data-status="{{ $d->status }}"
                 data-title="{{ strtolower($d->title) }}"
                 data-reporter="{{ strtolower($d->reporter_name) }}"
                 data-lat="{{ $d->latitude }}"
                 data-lng="{{ $d->longitude }}">

                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="bi {{ $typeIcon }} {{ $typeColor }} text-base shrink-0"></i>
                        <h3 class="text-sm font-bold text-slate-900 truncate">{{ $d->title }}</h3>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeBg }} shrink-0">
                        {{ $statusLabel }}
                    </span>
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
                        <i class="bi bi-map text-[10px]"></i> Lihat di Maps
                    </button>
                    <button type="button"
                            onclick="window.location.href='{{ route('laporan.show', $d->id) }}'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold text-white rounded-lg transition-all cursor-pointer"
                            style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 6px rgba(30,58,138,0.2);">
                        <i class="bi bi-file-text text-[10px]"></i> Detail
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-200/80 rounded-2xl p-14 text-center" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: #E4F0F6;">
                    <i class="bi bi-search text-2xl" style="color: #1e3a8a;"></i>
                </div>
                <p class="text-sm font-semibold text-slate-800 mb-1">Tidak ada data bencana</p>
                <p class="text-xs text-slate-400">Belum ada laporan bencana yang tersedia.</p>
            </div>
        @endforelse
    </div>

    {{-- No results (JS filtered) --}}
    <div id="noResults" class="hidden bg-white border border-slate-200/80 rounded-2xl p-14 text-center" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
        <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: #E4F0F6;">
            <i class="bi bi-emoji-frown text-2xl" style="color: #1e3a8a;"></i>
        </div>
        <p class="text-sm font-semibold text-slate-800 mb-1">Tidak ditemukan</p>
        <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter pencarian.</p>
    </div>
</div>

<style>
    .filter-chip { background: white; border-color: #e2e8f0; color: #64748b; }
    .filter-chip:hover { border-color: #93c5fd; color: #1e40af; background: #eff6ff; }
    .filter-chip.active { background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); border-color: transparent; color: white; box-shadow: 0 2px 8px rgba(30,58,138,0.25); }
</style>
@endsection

@section('scripts')
<script>
    const searchInput = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.disaster-card');
    const noResults = document.getElementById('noResults');
    const chips = document.querySelectorAll('.filter-chip');
    const resultCount = document.getElementById('resultCount');

    let activeFilter = 'all';

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeFilter = chip.dataset.filter;
            filterCards();
        });
    });

    searchInput.addEventListener('input', filterCards);

    function filterCards() {
        const query = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        cards.forEach(card => {
            const title = card.dataset.title || '';
            const reporter = card.dataset.reporter || '';
            const status = card.dataset.status || '';

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
        resultCount.innerHTML = `<span class="font-semibold text-slate-700">${visibleCount}</span> laporan ditemukan`;
    }
</script>
@endsection
