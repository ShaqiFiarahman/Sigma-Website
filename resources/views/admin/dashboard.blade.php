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
    <x-welcome-banner />
    <x-admin-stats
        :total="$total" :pending="$pending" :selesai="$selesai" :decline="$decline"
        :awas="$awas" :siaga1="$siaga1" :siaga2="$siaga2"
        :approvedVolunteers="$approvedVolunteers" :totalVolunteers="$totalVolunteers"
    />

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
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $pending }} laporan pending</p>
                </div>
                <a href="{{ route('laporan.index') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors">
                    Lihat Semua
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
                    <div class="flex-1 flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3 bg-emerald-50">
                            <i class="bi bi-check-all text-xl text-emerald-600"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Semua terverifikasi</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tidak ada laporan pending saat ini.</p>
                    </div>
                @endforelse
            </div>
            {{-- Footer info --}}
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
                <div class="space-y-1.5">
                    @php
                        $verifiedTotal = \App\Models\Disaster::whereNotIn('status', ['PENDING', 'DECLINE'])->count();
                        $weekVerified = \App\Models\Disaster::where('created_at', '>=', now()->subWeek())
                            ->whereNotIn('status', ['PENDING', 'DECLINE'])->count();
                    @endphp
                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                        <i class="bi bi-check-circle-fill text-emerald-500 text-[10px]"></i>
                        <span>{{ $verifiedTotal }} diverifikasi total</span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                        <i class="bi bi-graph-up text-blue-500 text-[10px]"></i>
                        <span>{{ $weekVerified }} diverifikasi minggu ini</span>
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
        let reportChart;
        const allDisasters = {!! json_encode($allDisasters) !!};

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

        // Period switching - instant, no fetch
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.period-btn').forEach(b => {
                    b.classList.remove('active');
                    b.classList.add('text-slate-500');
                });
                btn.classList.add('active');
                btn.classList.remove('text-slate-500');
                computeStats(btn.dataset.period);
            });
        });
    </script>
@endsection
