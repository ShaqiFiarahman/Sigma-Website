@extends('layouts.app')
@section('title', 'Informasi Posko & Pengungsian')
@section('subtitle', 'Temukan lokasi posko evakuasi terdekat dan informasi kapasitas terkini.')

@section('page-actions')
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 shadow-sm">
        <i class="bi bi-arrow-left text-xs"></i> Kembali
    </a>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Search & Filter --}}
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden mb-6"
         style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">
        <div class="p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" id="searchPosko" placeholder="Cari nama posko..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all bg-slate-50 focus:bg-white text-slate-800 placeholder:text-slate-400">
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" data-filter="all" class="filter-chip active px-3.5 py-2 text-xs font-semibold rounded-lg border transition-all duration-200">Semua</button>
                    <button type="button" data-filter="terdekat" class="filter-chip px-3.5 py-2 text-xs font-semibold rounded-lg border transition-all duration-200"><i class="bi bi-geo-alt text-[10px]"></i> Terdekat</button>
                    <button type="button" data-filter="tersedia" class="filter-chip px-3.5 py-2 text-xs font-semibold rounded-lg border transition-all duration-200"><i class="bi bi-check-circle text-[10px]"></i> Tersedia</button>
                    <button type="button" data-filter="penuh" class="filter-chip px-3.5 py-2 text-xs font-semibold rounded-lg border transition-all duration-200"><i class="bi bi-x-circle text-[10px]"></i> Penuh</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Summary --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        @php
            $totalPosko = count($shelters);
            $tersedia = collect($shelters)->where('status', 'Tersedia')->count();
            $penuh = collect($shelters)->where('status', 'Penuh')->count();
        @endphp
        <div class="bg-white border border-slate-200/80 rounded-xl p-4 text-center" style="box-shadow: 0 1px 3px rgba(10,15,30,0.04);">
            <p class="text-2xl font-bold text-slate-900">{{ $totalPosko }}</p>
            <p class="text-[11px] text-slate-500 font-medium">Total Posko</p>
        </div>
        <div class="bg-white border border-emerald-100 rounded-xl p-4 text-center" style="box-shadow: 0 1px 3px rgba(10,15,30,0.04);">
            <p class="text-2xl font-bold text-emerald-600">{{ $tersedia }}</p>
            <p class="text-[11px] text-slate-500 font-medium">Tersedia</p>
        </div>
        <div class="bg-white border border-red-100 rounded-xl p-4 text-center" style="box-shadow: 0 1px 3px rgba(10,15,30,0.04);">
            <p class="text-2xl font-bold text-red-600">{{ $penuh }}</p>
            <p class="text-[11px] text-slate-500 font-medium">Penuh</p>
        </div>
    </div>

    {{-- Shelter Cards --}}
    <div class="space-y-4" id="shelterList">
        @foreach($shelters as $shelter)
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
                    $statusBg = 'bg-emerald-50 border-emerald-100'; $statusText = 'text-emerald-700'; $statusDot = 'bg-emerald-500'; $statusLabel = 'Tersedia';
                }
            @endphp

            <div class="shelter-card bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-default"
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

                        {{-- Capacity Progress --}}
                        <div class="mt-3 mb-3">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-medium text-slate-600">Kapasitas</span>
                                <span class="text-xs font-bold {{ $capTextColor }}">{{ $shelter['capacity'] }} orang ({{ $capPercent }}%)</span>
                            </div>
                            <div class="w-full h-2.5 rounded-full {{ $barBg }} overflow-hidden">
                                <div class="h-full rounded-full {{ $barColor }} transition-all duration-500" style="width: {{ $capPercent }}%"></div>
                            </div>
                        </div>

                        {{-- Logistik yang dibutuhkan --}}
                        @if(!empty($shelter['logistics']))
                            <div class="mt-3">
                                <span class="text-[11px] font-semibold text-slate-500 block mb-1.5">Dibutuhkan:</span>
                                <div class="flex items-center gap-2 flex-wrap">
                                    @foreach($shelter['logistics'] as $item)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-medium border border-blue-100" style="background:#E4F0F6; color:#1e3a8a;">
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
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $shelter['lat'] }},{{ $shelter['lng'] }}"
                           target="_blank"
                           class="inline-flex items-center justify-center gap-1.5 w-full sm:w-40 px-4 py-2.5 text-xs font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 shadow-sm hover:shadow-md"
                           style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(30,58,138,0.2);">
                            <i class="bi bi-signpost-2-fill text-[11px]"></i> Petunjuk Arah
                        </a>
                        <a href="https://api.whatsapp.com/send?phone={{ $shelter['contact_phone'] ?? '6285934415914' }}&text={{ urlencode('Halo, saya ingin mengirimkan bantuan logistik ke ' . $shelter['name'] . ' berupa: ' . implode(', ', $shelter['logistics'] ?? [])) }}"
                           target="_blank"
                           class="inline-flex items-center justify-center gap-1.5 w-full sm:w-40 px-4 py-2.5 text-xs font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 shadow-sm hover:shadow-md"
                           style="background: #25D366; box-shadow: 0 2px 8px rgba(37,211,102,0.2);">
                            <i class="bi bi-whatsapp text-[11px]"></i> Hubungi WA
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Empty State --}}
    <div id="emptyState" class="hidden bg-white border border-slate-200/80 rounded-2xl p-12 text-center mt-4" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
        <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: #E4F0F6;">
            <i class="bi bi-building text-2xl" style="color: #1e3a8a;"></i>
        </div>
        <p class="text-sm font-semibold text-slate-800 mb-1">Tidak ada posko ditemukan</p>
        <p class="text-xs text-slate-400">Coba ubah kata kunci atau filter pencarian.</p>
    </div>
</div>

{{-- Logistics Modal --}}
<div id="logisticsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(10,15,30,0.5);">
    <div class="bg-white rounded-2xl max-w-sm w-full overflow-hidden shadow-2xl" style="box-shadow: 0 25px 50px rgba(10,15,30,0.25);">
        <div class="px-6 py-5 border-b border-slate-100" style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
            <h3 class="text-base font-bold text-white">Kebutuhan Logistik</h3>
            <p id="modalShelterName" class="text-sm font-medium mt-0.5" style="color: rgba(228,240,246,0.7);"></p>
        </div>
        <div class="p-6">
            <p class="text-sm text-slate-600 mb-4">Masyarakat dapat mengirimkan bantuan mendesak berikut:</p>
            <div id="modalLogistics" class="flex flex-wrap gap-2 mb-5"></div>
            <p class="text-xs text-slate-400 mb-5 italic">*Bantuan Anda sangat berarti bagi warga di pengungsian.</p>
            <a id="waButton" href="#" target="_blank"
               class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold text-white rounded-xl transition-all hover:opacity-90 mb-3"
               style="background: #25D366; box-shadow: 0 2px 8px rgba(37,211,102,0.3);">
                <i class="bi bi-whatsapp"></i> Hubungi via WhatsApp
            </a>
            <button type="button" onclick="closeLogistics()" class="w-full py-2.5 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors rounded-xl hover:bg-slate-50">Tutup</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showLogistics(name, logistics, phone) {
        document.getElementById('modalShelterName').textContent = name;
        const container = document.getElementById('modalLogistics');
        container.innerHTML = logistics.map(item =>
            `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold" style="background:#E4F0F6; color:#1e3a8a;"><i class="bi bi-box-seam text-[10px]"></i>${item}</span>`
        ).join('');
        const waPhone = phone || '6285934415914';
        const message = `Halo, saya ingin mengirimkan bantuan logistik ke ${name} berupa: ${logistics.join(', ')}`;
        document.getElementById('waButton').href = `https://api.whatsapp.com/send?phone=${waPhone}&text=${encodeURIComponent(message)}`;
        document.getElementById('logisticsModal').classList.remove('hidden');
    }

    function closeLogistics() { document.getElementById('logisticsModal').classList.add('hidden'); }
    document.getElementById('logisticsModal')?.addEventListener('click', (e) => { if (e.target === document.getElementById('logisticsModal')) closeLogistics(); });

    // Distance calculation
    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371, dLat = (lat2-lat1)*Math.PI/180, dLon = (lon2-lon1)*Math.PI/180;
        const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLon/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    function updateDistances(userLat, userLng) {
        document.querySelectorAll('.shelter-card').forEach(card => {
            const dist = getDistance(userLat, userLng, parseFloat(card.dataset.lat), parseFloat(card.dataset.lng));
            card.dataset.distance = dist.toFixed(2);
            const info = card.querySelector('.shelter-distance-info');
            if (info) {
                info.querySelector('.drive-time').textContent = Math.max(1, Math.round(dist/0.5)) + ' mnt';
                info.querySelector('.walk-time').textContent = Math.max(2, Math.round(dist/0.083)) + ' mnt';
                info.querySelector('.distance-km').textContent = dist.toFixed(1) + ' km';
            }
        });
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(p => updateDistances(p.coords.latitude, p.coords.longitude), () => updateDistances(-7.5755, 110.8243));
    } else { updateDistances(-7.5755, 110.8243); }

    // Search & Filter
    const searchInput = document.getElementById('searchPosko');
    const shelterCards = document.querySelectorAll('.shelter-card');
    const emptyState = document.getElementById('emptyState');

    function filterCards() {
        const query = searchInput.value.toLowerCase().trim();
        const activeFilter = document.querySelector('.filter-chip.active')?.dataset.filter || 'all';
        let visibleCount = 0;
        shelterCards.forEach(card => {
            const matchSearch = !query || card.dataset.name.includes(query);
            let matchFilter = true;
            if (activeFilter === 'tersedia') matchFilter = card.dataset.status === 'tersedia';
            else if (activeFilter === 'penuh') matchFilter = card.dataset.status === 'penuh';
            card.style.display = (matchSearch && matchFilter) ? '' : 'none';
            if (matchSearch && matchFilter) visibleCount++;
        });
        emptyState.classList.toggle('hidden', visibleCount > 0);
    }

    searchInput?.addEventListener('input', filterCards);

    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            if (chip.dataset.filter === 'terdekat') {
                const list = document.getElementById('shelterList');
                const cards = Array.from(list.querySelectorAll('.shelter-card'));
                cards.sort((a, b) => parseFloat(a.dataset.distance || 999) - parseFloat(b.dataset.distance || 999));
                cards.forEach(card => list.appendChild(card));
            }
            filterCards();
        });
    });
</script>
<style>
    .filter-chip { background: white; border-color: #e2e8f0; color: #64748b; }
    .filter-chip:hover { border-color: #93c5fd; color: #1e40af; background: #eff6ff; }
    .filter-chip.active { background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); border-color: transparent; color: white; box-shadow: 0 2px 8px rgba(30,58,138,0.25); }
</style>
@endsection
