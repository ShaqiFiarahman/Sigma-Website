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
        .period-btn.active {
            background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%);
            color: white;
            box-shadow: 0 1px 4px rgba(30,58,138,0.2);
        }
    </style>
    {{-- Dashboard Top Header --}}
    <div class="flex items-center justify-between mb-6 px-1">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Admin</h1>
            <p class="text-xs text-slate-500 mt-1">Sistem Pemantauan dan Tanggap Bencana SIGMA</p>
        </div>

        {{-- Dropdown Notifikasi --}}
        <div class="relative" id="notifWrapper">
            {{-- Bell Button --}}
            <button type="button" id="notifBtn"
                    class="relative w-10 h-10 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all duration-200">
                <i class="bi bi-bell text-lg"></i>
                <span id="notifBadge" class="hidden absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center text-[9px] font-bold text-white bg-red-500 rounded-full px-1 animate-pulse">
                    0
                </span>
            </button>

            {{-- Dropdown Panel --}}
            <div id="notifDropdown" class="hidden absolute right-0 top-full mt-2 w-80 bg-white border border-slate-200/80 rounded-2xl overflow-hidden z-50 shadow-2xl"
                 style="box-shadow: 0 10px 40px rgba(10,15,30,0.15);">

                {{-- Header --}}
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between"
                     style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-bell-fill text-white/80 text-sm"></i>
                        <span class="text-xs font-bold text-white">Notifikasi</span>
                    </div>
                    <span id="notifCount" class="text-[10px] font-bold text-blue-200 bg-white/15 px-2 py-0.5 rounded-full">0 baru</span>
                </div>

                {{-- List --}}
                <div id="notifList" class="max-h-72 overflow-y-auto">
                    {{-- Diisi secara dinamis lewat JS --}}
                </div>
            </div>
        </div>
    </div>

    <x-welcome-banner />

    {{-- Period Selector --}}
    <div class="flex items-center justify-between mb-4 px-1">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Ringkasan Laporan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Statistik laporan bencana</p>
        </div>
        <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-lg p-0.5">
            <button type="button" data-period="1d" class="period-btn px-3 py-1.5 text-[11px] font-medium rounded-md transition-all text-slate-500 hover:text-slate-800">Hari ini</button>
            <button type="button" data-period="7d" class="period-btn active px-3 py-1.5 text-[11px] font-medium rounded-md transition-all">7 Hari</button>
            <button type="button" data-period="30d" class="period-btn px-3 py-1.5 text-[11px] font-medium rounded-md transition-all text-slate-500 hover:text-slate-800">30 Hari</button>
            <button type="button" data-period="all" class="period-btn px-3 py-1.5 text-[11px] font-medium rounded-md transition-all text-slate-500 hover:text-slate-800">Semua</button>
        </div>
    </div>

    {{-- Hero Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
        <div class="sm:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition-all duration-200" style="box-shadow: 0 1px 3px rgba(10,15,30,0.05);">
            <i class="bi bi-file-earmark-text absolute top-4 right-4 text-slate-200 text-2xl"></i>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Total Laporan</p>
            <div class="flex items-end gap-3">
                <p class="text-4xl font-extrabold text-slate-900" id="stat-total">{{ $total }}</p>
                <span class="text-xs font-semibold text-emerald-600 mb-1.5" id="stat-total-trend">
                    @php $todayCount = \App\Models\Disaster::whereDate('created_at', today())->count(); @endphp
                    @if($todayCount > 0) ↑ +{{ $todayCount }} hari ini @endif
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Seluruh laporan yang masuk ke sistem</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition-all duration-200" style="box-shadow: 0 1px 3px rgba(10,15,30,0.05);">
            <i class="bi bi-hourglass-split absolute top-4 right-4 text-amber-200 text-xl"></i>
            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Pending</p>
            <p class="text-3xl font-extrabold text-amber-600" id="stat-pending">{{ $pending }}</p>
            <p class="text-[11px] text-slate-400 mt-1.5">Menunggu verifikasi</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition-all duration-200" style="box-shadow: 0 1px 3px rgba(10,15,30,0.05);">
            <i class="bi bi-check-circle absolute top-4 right-4 text-emerald-200 text-xl"></i>
            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-1">Verified</p>
            <p class="text-3xl font-extrabold text-emerald-600" id="stat-selesai">{{ $selesai }}</p>
            <p class="text-[11px] text-slate-400 mt-1.5">Sudah diverifikasi</p>
        </div>
    </div>

    {{-- Secondary Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-8">
        <div class="bg-white rounded-xl border border-slate-200/80 px-4 py-3.5 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200" style="box-shadow: 0 1px 2px rgba(10,15,30,0.04);">
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Awas</p>
            <p class="text-xl font-extrabold text-red-600" id="stat-awas">{{ $awas }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">laporan aktif</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/80 px-4 py-3.5 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200" style="box-shadow: 0 1px 2px rgba(10,15,30,0.04);">
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Siaga 1</p>
            <p class="text-xl font-extrabold text-orange-600" id="stat-siaga1">{{ $siaga1 }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">laporan aktif</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/80 px-4 py-3.5 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200" style="box-shadow: 0 1px 2px rgba(10,15,30,0.04);">
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Siaga 2</p>
            <p class="text-xl font-extrabold text-violet-600" id="stat-siaga2">{{ $siaga2 }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">laporan aktif</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/80 px-4 py-3.5 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200" style="box-shadow: 0 1px 2px rgba(10,15,30,0.04);">
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Ditolak</p>
            <p class="text-xl font-extrabold text-slate-600" id="stat-decline">{{ $decline }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">laporan ditolak</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/80 px-4 py-3.5 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200" style="box-shadow: 0 1px 2px rgba(10,15,30,0.04);">
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Relawan</p>
            <p class="text-xl font-extrabold text-purple-600">{{ $approvedVolunteers }} <span class="text-[11px] font-medium text-slate-400">aktif</span></p>
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
                    <p class="text-[11px] text-slate-400 mt-0.5" id="chart-peak-info">—</p>
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
                    <p class="text-[11px] text-slate-400 mt-0.5" id="pending-subtitle">{{ $pending }} laporan pending</p>
                </div>
                <a href="{{ route('laporan.index') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors">
                    Lihat Semua
                </a>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3 max-h-[320px]" id="pending-list">
                {{-- Diisi secara dinamis lewat JS --}}
                <div class="flex items-center justify-center py-8">
                    <div class="w-5 h-5 border-2 border-blue-200 border-t-blue-500 rounded-full animate-spin"></div>
                </div>
            </div>
            {{-- Footer info --}}
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                        <i class="bi bi-check-circle-fill text-emerald-500 text-[10px]"></i>
                        <span id="footer-verified-total">{{ \App\Models\Disaster::whereNotIn('status', ['PENDING', 'DECLINE'])->count() }} diverifikasi total</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                        <i class="bi bi-graph-up text-blue-500 text-[10px]"></i>
                        <span id="footer-week-verified">{{ \App\Models\Disaster::where('created_at', '>=', now()->subWeek())->whereNotIn('status', ['PENDING', 'DECLINE'])->count() }} diverifikasi minggu ini</span>
                    </div>
                </div>
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
    <x-disaster-map />

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ═══════════════════════════════════════════════════
        //  CSRF TOKEN for AJAX requests
        // ═══════════════════════════════════════════════════
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const UPDATE_STATUS_BASE = '{{ url('admin/laporan/update-status') }}';

        // ═══════════════════════════════════════════════════
        //  Live data — will be refreshed from API
        // ═══════════════════════════════════════════════════
        let allDisasters = {!! json_encode($allDisasters) !!};
        let reportChart;
        let currentPeriod = '7d';

        const ctx = document.getElementById('reportChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(59,111,232,0.12)');
        gradient.addColorStop(1, 'rgba(59,111,232,0)');

        function createChart(labels, dataTotal, dataVerified, dataPending) {
            if (reportChart) reportChart.destroy();
            reportChart = new Chart(ctx, {
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
        }

        function computeStats(period) {
            const now = new Date();
            let cutoff = null;
            let chartDays = 7;

            if (period === '1d') { cutoff = new Date(now - 86400000); chartDays = 12; } // 12 slots = tiap 2 jam
            else if (period === '7d') { cutoff = new Date(now - 7 * 86400000); chartDays = 7; }
            else if (period === '30d') { cutoff = new Date(now - 30 * 86400000); chartDays = 15; }
            else { cutoff = null; chartDays = 14; }

            const filtered = cutoff ? allDisasters.filter(d => new Date(d.date) >= cutoff) : allDisasters;

            const stats = {
                total: filtered.length,
                pending: filtered.filter(d => d.status === 'PENDING').length,
                selesai: filtered.filter(d => !['PENDING', 'DECLINE'].includes(d.status)).length,
                decline: filtered.filter(d => d.status === 'DECLINE').length,
                awas: filtered.filter(d => d.status === 'AWAS').length,
                siaga1: filtered.filter(d => d.status === 'SIAGA_1').length,
                siaga2: filtered.filter(d => d.status === 'SIAGA_2').length,
            };

            // Update DOM
            document.getElementById('stat-total').textContent = stats.total;
            document.getElementById('stat-pending').textContent = stats.pending;
            document.getElementById('stat-selesai').textContent = stats.selesai;
            document.getElementById('stat-decline').textContent = stats.decline;
            document.getElementById('stat-awas').textContent = stats.awas;
            document.getElementById('stat-siaga1').textContent = stats.siaga1;
            document.getElementById('stat-siaga2').textContent = stats.siaga2;

            // Trend for total card
            const todayItems = allDisasters.filter(d => d.date.startsWith(now.toISOString().split('T')[0]));
            const trendEl = document.getElementById('stat-total-trend');
            if (todayItems.length > 0) {
                trendEl.innerHTML = `<span class="text-emerald-600">↑ +${todayItems.length} hari ini</span>`;
            } else {
                trendEl.textContent = '';
            }

            // Chart
            const labels = [], dataTotal = [], dataVerified = [], dataPending = [];

            if (period === '1d') {
                // Per 2 jam
                for (let i = 11; i >= 0; i--) {
                    const slotEnd = new Date(now - i * 2 * 3600000);
                    const slotStart = new Date(slotEnd - 2 * 3600000);
                    labels.push(slotEnd.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
                    const slotItems = allDisasters.filter(d => { const t = new Date(d.date); return t >= slotStart && t < slotEnd; });
                    dataTotal.push(slotItems.length);
                    dataVerified.push(slotItems.filter(d => !['PENDING', 'DECLINE'].includes(d.status)).length);
                    dataPending.push(slotItems.filter(d => d.status === 'PENDING').length);
                }
            } else {
                const days = period === '30d' ? 30 : (period === '7d' ? 7 : 14);
                const step = period === '30d' ? 2 : 1;
                for (let i = days - 1; i >= 0; i -= step) {
                    const day = new Date(now - i * 86400000);
                    // Label: show range if step > 1
                    if (step > 1 && (i - 1) >= 0) {
                        const day2 = new Date(now - (i - 1) * 86400000);
                        labels.push(day.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }) + ' - ' + day2.toLocaleDateString('id-ID', { day: '2-digit' }));
                    } else {
                        labels.push(day.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }));
                    }
                    // Accumulate data for `step` days into one point
                    let t = 0, v = 0, p = 0;
                    for (let s = 0; s < step && (i - s) >= 0; s++) {
                        const d2 = new Date(now - (i - s) * 86400000);
                        const ds = d2.toISOString().split('T')[0];
                        const items = allDisasters.filter(d => d.date.startsWith(ds));
                        t += items.length;
                        v += items.filter(d => !['PENDING', 'DECLINE'].includes(d.status)).length;
                        p += items.filter(d => d.status === 'PENDING').length;
                    }
                    dataTotal.push(t);
                    dataVerified.push(v);
                    dataPending.push(p);
                }
            }

            createChart(labels, dataTotal, dataVerified, dataPending);

            // Peak info
            const peakEl = document.getElementById('chart-peak-info');
            const maxVal = Math.max(...dataTotal);
            if (maxVal > 0) {
                const peakIdx = dataTotal.indexOf(maxVal);
                peakEl.textContent = `Puncak: ${labels[peakIdx]} (${maxVal} laporan)`;
            } else {
                peakEl.textContent = 'Belum ada data pada periode ini';
            }
        }

        // Initial render
        createChart(
            {!! json_encode($chartLabels) !!},
            {!! json_encode($chartData) !!},
            {!! json_encode($chartVerified) !!},
            {!! json_encode($chartPending) !!}
        );

        // Period switching — instant, uses live allDisasters
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.period-btn').forEach(b => {
                    b.classList.remove('active');
                    b.classList.add('text-slate-500');
                });
                btn.classList.add('active');
                btn.classList.remove('text-slate-500');
                currentPeriod = btn.dataset.period;
                computeStats(currentPeriod);
            });
        });

        // ═══════════════════════════════════════════════════
        //  PENDING LIST RENDERER
        // ═══════════════════════════════════════════════════
        function renderPendingList(items) {
            const container = document.getElementById('pending-list');
            if (!container) return;

            if (items.length === 0) {
                container.innerHTML = `
                    <div class="flex-1 flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3 bg-emerald-50">
                            <i class="bi bi-check-all text-xl text-emerald-600"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Semua terverifikasi</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tidak ada laporan pending saat ini.</p>
                    </div>`;
                return;
            }

            container.innerHTML = items.map(item => {
                const lokasi = item.lokasi.length > 40 ? item.lokasi.substring(0, 40) + '...' : item.lokasi;
                return `
                    <div class="pending-item" id="pending-item-${item.id}">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h4 class="text-[13px] font-bold text-slate-900 leading-tight line-clamp-1">${item.judul}</h4>
                            <span class="shrink-0 text-[10px] text-slate-400 font-medium">${item.tanggal}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3 flex items-center gap-1.5">
                            <i class="bi bi-geo-alt text-slate-300 text-[10px]"></i>
                            <span class="line-clamp-1">${lokasi}</span>
                        </p>
                        <div class="flex items-center gap-2">
                            <button onclick="adminUpdateStatus(${item.id}, 'SIAGA_2', this)"
                                    class="px-3 py-1.5 text-[10px] font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                <i class="bi bi-check-lg mr-0.5"></i> Verifikasi
                            </button>
                            <button onclick="adminUpdateStatus(${item.id}, 'DECLINE', this)"
                                    class="px-3 py-1.5 text-[10px] font-bold rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors">
                                <i class="bi bi-x-lg mr-0.5"></i> Tolak
                            </button>
                            <a href="/laporan/detail/${item.id}" class="ml-auto text-[10px] font-bold text-slate-500 hover:text-blue-600 transition-colors">
                                Detail →
                            </a>
                        </div>
                    </div>`;
            }).join('');
        }

        // ═══════════════════════════════════════════════════
        //  AJAX: Verify / Reject without page reload
        // ═══════════════════════════════════════════════════
        window.adminUpdateStatus = function(id, status, btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="inline-block w-3 h-3 border border-current border-t-transparent rounded-full animate-spin"></span>';

            fetch(`${UPDATE_STATUS_BASE}/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status }),
            })
            .then(res => {
                if (!res.ok) throw new Error('Server error');
                // Remove card with animation
                const card = document.getElementById(`pending-item-${id}`);
                if (card) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        card.remove();
                        // If no cards left, show empty state
                        const container = document.getElementById('pending-list');
                        if (container && container.children.length === 0) {
                            renderPendingList([]);
                        }
                    }, 300);
                }
                // Immediately refresh stats
                fetchAdminStats();
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = status === 'SIAGA_2'
                    ? '<i class="bi bi-check-lg mr-0.5"></i> Verifikasi'
                    : '<i class="bi bi-x-lg mr-0.5"></i> Tolak';
                alert('Gagal mengubah status. Silakan coba lagi.');
            });
        };

        // ═══════════════════════════════════════════════════
        //  LIVE POLLING: Admin Stats every 15 seconds
        // ═══════════════════════════════════════════════════
        function fetchAdminStats() {
            fetch('/api/admin-stats')
                .then(res => res.json())
                .then(data => {
                    // Update live allDisasters and recompute chart
                    allDisasters = data.all_disasters.map(d => ({
                        id: d.id, status: d.status, date: d.date
                    }));
                    computeStats(currentPeriod);

                    // Update stat cards
                    document.getElementById('stat-total').textContent   = data.total;
                    document.getElementById('stat-pending').textContent  = data.pending;
                    document.getElementById('stat-selesai').textContent  = data.selesai;
                    document.getElementById('stat-decline').textContent  = data.decline;
                    document.getElementById('stat-awas').textContent     = data.awas;
                    document.getElementById('stat-siaga1').textContent   = data.siaga1;
                    document.getElementById('stat-siaga2').textContent   = data.siaga2;

                    // Update footer
                    const footerVerified = document.getElementById('footer-verified-total');
                    const footerWeek     = document.getElementById('footer-week-verified');
                    if (footerVerified) footerVerified.textContent = `${data.verified_total} diverifikasi total`;
                    if (footerWeek)     footerWeek.textContent     = `${data.week_verified} diverifikasi minggu ini`;

                    // Update pending subtitle
                    const sub = document.getElementById('pending-subtitle');
                    if (sub) sub.textContent = `${data.pending} laporan pending`;

                    // Update today trend
                    const trendEl = document.getElementById('stat-total-trend');
                    if (trendEl) {
                        trendEl.innerHTML = data.today_count > 0
                            ? `<span class="text-emerald-600">↑ +${data.today_count} hari ini</span>`
                            : '';
                    }

                    // Update pending list (only re-render if count changed to avoid flicker)
                    renderPendingList(data.pending_items);
                })
                .catch(err => console.warn('Admin stats fetch failed:', err));
        }

        // ═══════════════════════════════════════════════════
        //  NOTIFICATION POLLING SYSTEM
        // ═══════════════════════════════════════════════════
        (function() {
            const notifBtn      = document.getElementById('notifBtn');
            const notifDropdown = document.getElementById('notifDropdown');
            const notifBadge    = document.getElementById('notifBadge');
            const notifCount    = document.getElementById('notifCount');
            const notifList     = document.getElementById('notifList');

            if (!notifBtn || !notifDropdown) return;

            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
                if (!notifDropdown.classList.contains('hidden')) {
                    localStorage.setItem('admin_last_seen_notif', new Date().toISOString());
                    if (notifBadge) notifBadge.classList.add('hidden');
                }
            });

            document.addEventListener('click', (e) => {
                if (!notifDropdown.classList.contains('hidden') && !e.target.closest('#notifWrapper')) {
                    notifDropdown.classList.add('hidden');
                }
            });

            function fetchNotifications() {
                fetch('/api/pending-reports')
                    .then(res => res.json())
                    .then(data => {
                        const lastSeenStr = localStorage.getItem('admin_last_seen_notif');
                        const lastSeen    = lastSeenStr ? new Date(lastSeenStr) : new Date(0);

                        const unreadCount = data.filter(item => new Date(item.created_at) > lastSeen).length;

                        if (notifBadge) {
                            notifBadge.textContent = unreadCount;
                            notifBadge.classList.toggle('hidden', unreadCount === 0);
                        }
                        if (notifCount) notifCount.textContent = `${unreadCount} baru`;

                        if (data.length === 0) {
                            notifList.innerHTML = `
                                <div class="px-5 py-8 text-center">
                                    <i class="bi bi-bell-slash text-slate-300 text-lg block mb-1"></i>
                                    <p class="text-xs text-slate-400">Tidak ada laporan baru</p>
                                </div>`;
                            return;
                        }

                        notifList.innerHTML = data.map(item => {
                            const isNew = new Date(item.created_at) > lastSeen;
                            return `
                                <div class="px-5 py-3.5 flex items-start gap-3 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-50 last:border-0"
                                     onclick="window.location.href='/laporan/detail/${item.id}'">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5 ${isNew ? 'bg-blue-100' : 'bg-slate-100'}">
                                        <i class="bi bi-megaphone-fill text-xs ${isNew ? 'text-blue-600' : 'text-slate-400'}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 truncate">${item.title}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                            <span>${item.reporter}</span> &middot; <span>${item.date}</span>
                                        </p>
                                    </div>
                                    ${isNew ? '<span class="w-2 h-2 rounded-full bg-blue-500 shrink-0 mt-2"></span>' : ''}
                                </div>`;
                        }).join('');
                    })
                    .catch(() => {
                        notifList.innerHTML = `
                            <div class="px-5 py-8 text-center">
                                <i class="bi bi-wifi-off text-slate-300 text-lg block mb-1"></i>
                                <p class="text-xs text-slate-400">Gagal memuat notifikasi</p>
                            </div>`;
                    });
            }

            fetchNotifications();
            setInterval(fetchNotifications, 30000);
        })();

        // ═══════════════════════════════════════════════════
        //  INITIAL LOAD + START POLLING
        // ═══════════════════════════════════════════════════
        // Initial chart render from server-side data
        createChart(
            {!! json_encode($chartLabels) !!},
            {!! json_encode($chartData) !!},
            {!! json_encode($chartVerified) !!},
            {!! json_encode($chartPending) !!}
        );

        // Load pending list and start live updates
        fetchAdminStats();
        setInterval(fetchAdminStats, 15000);
    </script>
@endsection

