@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

    <style>
        .menu-card {
            background: #FFFFFF;
            border: 1px solid rgba(10, 15, 30, 0.06);
            border-radius: 20px;
            padding: 1.5rem 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 160px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(10, 15, 30, 0.04);
        }
        .menu-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(59, 111, 232, 0) 0%, rgba(59, 111, 232, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .menu-card:hover {
            transform: translateY(-4px);
            border-color: rgba(59, 111, 232, 0.4);
            box-shadow: 0 12px 24px rgba(59, 111, 232, 0.12);
        }
        .menu-card:hover::before { opacity: 1; }
        .menu-card > * { position: relative; z-index: 1; }

        .menu-icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, #E4F0F6 0%, #C8DFF0 100%);
            color: #0A0F1E;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }
        .menu-card:hover .menu-icon-wrap {
            background: linear-gradient(135deg, #1e3a8a 0%, #3B6FE8 100%);
            color: #FFFFFF;
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(59, 111, 232, 0.25);
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0A0F1E;
            letter-spacing: -0.01em;
        }

        .stat-card {
            background: #FFFFFF;
            border: 1px solid rgba(10, 15, 30, 0.06);
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 8px rgba(10, 15, 30, 0.04);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            box-shadow: 0 8px 20px rgba(10, 15, 30, 0.08);
            transform: translateY(-2px);
        }

        .pending-item {
            padding: 1rem 1.25rem;
            border-radius: 16px;
            border: 1px solid rgba(10, 15, 30, 0.06);
            background: #FFFFFF;
            transition: all 0.2s ease;
        }
        .pending-item:hover {
            border-color: rgba(59, 111, 232, 0.3);
            box-shadow: 0 4px 12px rgba(59, 111, 232, 0.08);
        }
    </style>

    {{-- WELCOME BANNER (sama persis dengan user dashboard) --}}
    <div class="relative rounded-2xl overflow-hidden mb-8"
        style="background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 50%, #1a3068 100%);">

        <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full opacity-20 pointer-events-none"
            style="background: radial-gradient(circle, #E4F0F6 0%, transparent 70%);"></div>
        <div class="absolute bottom-0 left-1/3 w-96 h-48 opacity-10 pointer-events-none"
            style="background: radial-gradient(ellipse, #3B6FE8 0%, transparent 70%);"></div>

        <div class="absolute inset-0 opacity-15 pointer-events-none">
            <img src="{{ asset('images/indonesia_map.png') }}" class="w-full h-full object-cover object-center"
                alt="" loading="lazy" decoding="async">
        </div>

        <div class="relative z-10 px-12 sm:px-16 py-14 flex flex-col sm:flex-row sm:items-center justify-between gap-12">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full mb-4"
                    style="background: rgba(228,240,246,0.15); border: 1px solid rgba(228,240,246,0.2); backdrop-filter: blur(8px);">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-[11px] font-bold text-white/70 tracking-wide">Panel Administrator</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight mb-3">Selamat datang, Admin.</h2>
                <p class="text-sm sm:text-base leading-relaxed" style="color: rgba(228,240,246,0.7);">
                    Pantau laporan bencana, verifikasi kejadian, dan koordinasi tim dari panel kendali terpusat.
                </p>
            </div>
            <div class="shrink-0 hidden sm:flex flex-col items-end gap-1">
                <p class="text-2xl font-bold text-white" id="liveClock">--:--</p>
                <p class="text-xs" style="color: rgba(228,240,246,0.45);" id="liveDate">—</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         STATISTICS
    ═══════════════════════════════════════════════════ --}}
    <div class="mb-4 px-1">
        <h2 class="section-title">Ringkasan Laporan</h2>
        <p class="text-xs text-slate-500 mt-0.5">Statistik laporan bencana saat ini</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="stat-card flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0"
                style="background: linear-gradient(135deg, #E4F0F6, #C8DFF0);">
                <i class="bi bi-file-earmark-text text-lg" style="color: #1e3a8a;"></i>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Total</p>
                <p class="text-2xl font-extrabold text-slate-900">{{ $total }}</p>
            </div>
        </div>

        <div class="stat-card flex items-center gap-4" style="border-color: rgba(245,158,11,0.2);">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0"
                style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                <i class="bi bi-hourglass-split text-lg text-amber-700"></i>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Pending</p>
                <p class="text-2xl font-extrabold text-amber-600">{{ $pending }}</p>
            </div>
        </div>

        <div class="stat-card flex items-center gap-4" style="border-color: rgba(16,185,129,0.2);">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0"
                style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
                <i class="bi bi-check-circle text-lg text-emerald-700"></i>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Verified</p>
                <p class="text-2xl font-extrabold text-emerald-600">{{ $selesai }}</p>
            </div>
        </div>

        <div class="stat-card flex items-center gap-4" style="border-color: rgba(239,68,68,0.2);">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0"
                style="background: linear-gradient(135deg, #fee2e2, #fecaca);">
                <i class="bi bi-x-circle text-lg text-red-700"></i>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Ditolak</p>
                <p class="text-2xl font-extrabold text-red-600">{{ $decline }}</p>
            </div>
        </div>
    </div>

    {{-- Tingkat Bencana + Relawan --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
        <div class="bg-white rounded-2xl border border-red-100 px-4 py-3 flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-red-500 animate-pulse shadow-lg shadow-red-500/30"></span>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Awas</p>
                <p class="text-lg font-extrabold text-red-700">{{ $awas }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-amber-100 px-4 py-3 flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-amber-500 shadow-lg shadow-amber-500/30"></span>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Siaga 1</p>
                <p class="text-lg font-extrabold text-amber-700">{{ $siaga1 }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-blue-100 px-4 py-3 flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-blue-500 shadow-lg shadow-blue-500/30"></span>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Siaga 2</p>
                <p class="text-lg font-extrabold text-blue-700">{{ $siaga2 }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-indigo-100 px-4 py-3 flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/30"></span>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Relawan</p>
                <p class="text-lg font-extrabold text-indigo-700">{{ $approvedVolunteers }}<span class="text-xs font-medium text-slate-400">/{{ $totalVolunteers }}</span></p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         CHART + RECENT PENDING
    ═══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">

        {{-- Chart --}}
        <div class="lg:col-span-3 bg-white border border-slate-200/60 rounded-2xl p-6"
            style="box-shadow: 0 2px 8px rgba(10,15,30,0.04);">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Tren Laporan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">7 hari terakhir</p>
                </div>
                <div class="flex items-center gap-4 text-[11px] font-semibold text-slate-500">
                    <span class="flex items-center gap-1.5"><span class="w-6 h-[3px] rounded-full" style="background:#3B6FE8;"></span> Total</span>
                    <span class="flex items-center gap-1.5"><span class="w-6 h-[3px] rounded-full bg-emerald-500"></span> Verified</span>
                    <span class="flex items-center gap-1.5"><span class="w-6 h-[3px] rounded-full bg-amber-400" style="border: 1px dashed #f59e0b; background: transparent;"></span> Pending</span>
                </div>
            </div>
            <div class="w-full relative" style="height: 260px;">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

        {{-- Recent Pending --}}
        <div class="lg:col-span-2 bg-white border border-slate-200/60 rounded-2xl overflow-hidden flex flex-col"
            style="box-shadow: 0 2px 8px rgba(10,15,30,0.04);">
            <div class="px-5 py-4 flex items-center justify-between border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        Menunggu Verifikasi
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $pending }} laporan pending</p>
                </div>
                <a href="{{ route('laporan.index') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors">
                    Lihat Semua →
                </a>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3 max-h-[320px]">
                @forelse($recentPending as $item)
                    <div class="pending-item">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h4 class="text-[13px] font-bold text-slate-900 leading-tight line-clamp-1">{{ $item['judul'] }}</h4>
                            <span class="shrink-0 text-[10px] text-slate-400 font-medium">{{ $item['tanggal'] }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3 flex items-center gap-1.5">
                            <i class="bi bi-geo-alt text-slate-300 text-[10px]"></i>
                            <span class="line-clamp-1">{{ \Illuminate\Support\Str::limit($item['lokasi'], 40) }}</span>
                        </p>
                        <div class="flex items-center gap-2">
                            <form action="{{ route('laporan.update_status', $item['id']) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="SIAGA_2">
                                <button type="submit" class="px-3 py-1.5 text-[10px] font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                    <i class="bi bi-check-lg mr-0.5"></i> Verifikasi
                                </button>
                            </form>
                            <form action="{{ route('laporan.update_status', $item['id']) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="DECLINE">
                                <button type="submit" class="px-3 py-1.5 text-[10px] font-bold rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors">
                                    <i class="bi bi-x-lg mr-0.5"></i> Tolak
                                </button>
                            </form>
                            <a href="{{ route('laporan.show', $item['id']) }}" class="ml-auto text-[10px] font-bold text-slate-500 hover:text-blue-600 transition-colors">
                                Detail →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="flex-1 flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-3"
                            style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
                            <i class="bi bi-check-all text-2xl text-emerald-700"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Semua terverifikasi!</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tidak ada laporan pending.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         QUICK ACCESS (sama style dengan user dashboard menu)
    ═══════════════════════════════════════════════════ --}}
    <div class="mb-4 px-1">
        <h2 class="section-title">Akses Cepat Administrator</h2>
        <p class="text-xs text-slate-500 mt-0.5">Menu kelola fitur dan layanan utama SIGMA</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('laporan.index') }}" class="menu-card group">
            <div class="menu-icon-wrap"><i class="bi bi-shield-check"></i></div>
            <p class="font-bold text-sm mb-1 text-slate-900">Kelola Laporan</p>
            <p class="text-xs text-slate-500 leading-relaxed">Verifikasi & validasi laporan</p>
        </a>
        <a href="{{ route('volunteer.index') }}" class="menu-card group">
            <div class="menu-icon-wrap"><i class="bi bi-people-fill"></i></div>
            <p class="font-bold text-sm mb-1 text-slate-900">Data Relawan</p>
            <p class="text-xs text-slate-500 leading-relaxed">{{ $totalVolunteers }} terdaftar</p>
        </a>
        <a href="{{ route('shelter') }}" class="menu-card group">
            <div class="menu-icon-wrap"><i class="bi bi-house-heart-fill"></i></div>
            <p class="font-bold text-sm mb-1 text-slate-900">Data Posko</p>
            <p class="text-xs text-slate-500 leading-relaxed">Titik pengungsian & shelter</p>
        </a>
        <a href="{{ route('search') }}" class="menu-card group">
            <div class="menu-icon-wrap"><i class="bi bi-search"></i></div>
            <p class="font-bold text-sm mb-1 text-slate-900">Cari Bencana</p>
            <p class="text-xs text-slate-500 leading-relaxed">Pencarian & filter data</p>
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════
         MAP
    ═══════════════════════════════════════════════════ --}}
    <div class="mb-4 px-1">
        <h2 class="section-title">Peta Pantauan Bencana</h2>
        <p class="text-xs text-slate-500 mt-0.5">Semua laporan bencana dengan koordinat lokasi</p>
    </div>

    <div class="bg-white border border-slate-200/60 rounded-2xl overflow-hidden mb-8"
        style="box-shadow: 0 2px 8px rgba(10,15,30,0.04);">
        <div class="relative">
            <div id="map" class="w-full h-[480px] z-0"></div>

            {{-- Legend --}}
            <div class="absolute bottom-4 right-4 z-[1000] p-3 rounded-2xl pointer-events-none"
                style="background: rgba(255,255,255,0.92); backdrop-filter: blur(12px); border: 1px solid rgba(10,15,30,0.08); box-shadow: 0 4px 16px rgba(10,15,30,0.1);">
                <p class="text-[10px] font-bold uppercase tracking-wider mb-2 text-slate-400">Tingkat Bencana</p>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span><span class="text-xs font-medium text-slate-700">Awas</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-amber-400"></span><span class="text-xs font-medium text-slate-700">Siaga</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-slate-400"></span><span class="text-xs font-medium text-slate-700">Pending</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span><span class="text-xs font-medium text-slate-700">Selesai</span></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .custom-marker {
            width: 14px; height: 14px; border-radius: 50%;
            border: 2.5px solid white; position: absolute;
            cursor: pointer; transform: translate(-50%, -50%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: transform 0.2s ease;
        }
        .custom-marker:hover { transform: translate(-50%, -50%) scale(1.3); }
        .marker-awas { background-color: #ef4444; animation: pulse-marker 2s infinite; }
        .marker-siaga { background-color: #f59e0b; }
        .marker-pending { background-color: #94a3b8; }
        .marker-resolved { background-color: #10b981; }

        @keyframes pulse-marker {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4), 0 2px 8px rgba(0,0,0,0.2); }
            50% { box-shadow: 0 0 0 6px rgba(239,68,68,0), 0 2px 8px rgba(0,0,0,0.2); }
        }

        .gm-style-iw { padding: 0 !important; max-width: 280px !important; border-radius: 16px !important; }
        .gm-style-iw-c { padding: 0 !important; border-radius: 16px !important; background-color: transparent !important; box-shadow: 0 12px 40px rgba(0,0,0,0.2) !important; }
        .gm-style-iw-d { overflow: hidden !important; max-height: none !important; padding: 0 !important; }
        .gm-style-iw-tc { display: none !important; }
        .gm-ui-hover-effect { display: none !important; }
    </style>

    <script>
        // Live Clock
        function updateClock() {
            const now = new Date();
            const clock = document.getElementById('liveClock');
            const dateEl = document.getElementById('liveDate');
            if (clock) clock.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            if (dateEl) dateEl.textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' });
        }
        updateClock();
        setInterval(updateClock, 1000);

        let map, infoWindow;
        window.closeMapPopup = function() { if (infoWindow) infoWindow.close(); };

        function initMap() {
            // ─── Chart.js ────────────────────────────────
            const ctx = document.getElementById('reportChart').getContext('2d');
            const labels = {!! json_encode($chartLabels) !!};
            const dataTotal = {!! json_encode($chartData) !!};
            const dataVerified = {!! json_encode($chartVerified) !!};
            const dataPending = {!! json_encode($chartPending) !!};

            const gradient = ctx.createLinearGradient(0, 0, 0, 260);
            gradient.addColorStop(0, 'rgba(59,111,232,0.12)');
            gradient.addColorStop(1, 'rgba(59,111,232,0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        { label: 'Total', data: dataTotal, borderColor: '#3B6FE8', backgroundColor: gradient, borderWidth: 2.5, pointBackgroundColor: '#fff', pointBorderColor: '#3B6FE8', pointBorderWidth: 2, pointRadius: 4, fill: true, tension: 0.4 },
                        { label: 'Verified', data: dataVerified, borderColor: '#10b981', borderWidth: 2, pointBackgroundColor: '#fff', pointBorderColor: '#10b981', pointBorderWidth: 2, pointRadius: 3, fill: false, tension: 0.4 },
                        { label: 'Pending', data: dataPending, borderColor: '#f59e0b', borderWidth: 2, borderDash: [6, 4], pointBackgroundColor: '#fff', pointBorderColor: '#f59e0b', pointBorderWidth: 2, pointRadius: 3, fill: false, tension: 0.4 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0A0F1E', titleColor: '#E4F0F6', bodyColor: 'rgba(228,240,246,0.8)', padding: 12, cornerRadius: 12, displayColors: true, boxPadding: 4 } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(10,15,30,0.04)' }, border: { display: false }, ticks: { precision: 0, color: '#94a3b8', font: { size: 11 } } },
                        x: { grid: { display: false }, border: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } }
                    }
                }
            });

            // ─── Google Maps ─────────────────────────────
            class CustomMarker extends google.maps.OverlayView {
                constructor(position, element, map, onClick) {
                    super(); this.position = position; this.element = element;
                    this.element.addEventListener('click', (e) => { e.stopPropagation(); if (onClick) onClick(); });
                    this.setMap(map);
                }
                onAdd() { this.getPanes().overlayMouseTarget.appendChild(this.element); }
                draw() { const pos = this.getProjection().fromLatLngToDivPixel(this.position); if (pos) { this.element.style.left = pos.x + 'px'; this.element.style.top = pos.y + 'px'; } }
                onRemove() { if (this.element.parentNode) this.element.parentNode.removeChild(this.element); }
            }

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 11, center: { lat: -7.7956, lng: 110.3695 },
                styles: [
                    { featureType: "poi", stylers: [{ visibility: "off" }] },
                    { featureType: "transit", stylers: [{ visibility: "off" }] },
                    { featureType: "water", elementType: "geometry", stylers: [{ color: "#c8dff0" }] },
                    { featureType: "landscape", elementType: "geometry", stylers: [{ color: "#f0f4f8" }] },
                ],
                disableDefaultUI: false, mapTypeControl: false, streetViewControl: false, fullscreenControl: true, gestureHandling: 'greedy'
            });

            map.addListener('click', () => { if (infoWindow) infoWindow.close(); });
            infoWindow = new google.maps.InfoWindow();

            const disasters = {!! json_encode($mapDisasters) !!};
            disasters.forEach(item => {
                if (!item.latitude || !item.longitude) return;
                const coords = { lat: parseFloat(item.latitude), lng: parseFloat(item.longitude) };

                let cls = 'marker-pending';
                if (item.status === 'AWAS') cls = 'marker-awas';
                else if (item.status === 'SIAGA_1' || item.status === 'SIAGA_2') cls = 'marker-siaga';
                else if (item.status === 'RESOLVED') cls = 'marker-resolved';

                const el = document.createElement('div');
                el.className = `custom-marker ${cls}`;
                el.title = item.judul;

                const colors = { 'AWAS': '#ef4444', 'SIAGA_1': '#f59e0b', 'SIAGA_2': '#3b82f6', 'PENDING': '#94a3b8', 'RESOLVED': '#10b981', 'DECLINE': '#6b7280' };
                const bg = colors[item.status] || '#94a3b8';

                const popup = `
                    <div style="width:270px;font-family:system-ui,sans-serif;border-radius:16px;overflow:hidden;">
                        <div style="padding:14px 16px 10px;background:linear-gradient(135deg,#0A0F1E,#0f1f4a);">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                <span style="font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:rgba(228,240,246,0.5);">${item.tanggal}</span>
                                <span style="padding:2px 8px;border-radius:6px;font-size:9px;font-weight:800;color:white;background:${bg};">${item.status_label}</span>
                            </div>
                            <h4 style="font-size:13px;font-weight:700;color:white;margin:0;">${item.judul}</h4>
                        </div>
                        <div style="padding:12px 16px 14px;background:white;">
                            <p style="font-size:11px;color:#64748b;margin:0 0 10px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${item.deskripsi}</p>
                            <a href="/laporan/detail/${item.id}" style="display:block;text-align:center;padding:8px;background:linear-gradient(135deg,#0A0F1E,#1e3a8a);color:white;text-decoration:none;font-size:11px;font-weight:700;border-radius:8px;">Detail Laporan →</a>
                        </div>
                    </div>`;

                new CustomMarker(coords, el, map, () => { infoWindow.setContent(popup); infoWindow.setPosition(coords); infoWindow.open(map); });
            });
        }
    </script>
@endsection
