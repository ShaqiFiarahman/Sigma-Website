@props(['chartLabels', 'chartData', 'chartVerified', 'chartPending', 'allDisasters', 'pending'])

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
    {{-- Chart --}}
    <div class="lg:col-span-3 bg-white border border-slate-200/60 rounded-2xl p-6" style="box-shadow: 0 2px 8px rgba(10,15,30,0.04);">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-bold text-slate-900">Tren Laporan</h3>
                <p class="text-[11px] text-slate-400 mt-0.5" id="chart-peak-info">—</p>
            </div>
            <div class="flex items-center gap-4 text-[11px] font-semibold text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-6 h-[3px] rounded-full" style="background:#3B6FE8;"></span> Total</span>
                <span class="flex items-center gap-1.5"><span class="w-6 h-[3px] rounded-full bg-emerald-500"></span> Verified</span>
                <span class="flex items-center gap-1.5"><span class="w-6 h-[3px] rounded-full" style="border:1px dashed #f59e0b;"></span> Pending</span>
            </div>
        </div>
        <div class="w-full relative" style="height: 260px;">
            <canvas id="reportChart"></canvas>
        </div>
    </div>

    {{-- Recent Pending --}}
    <div class="lg:col-span-2 bg-white border border-slate-200/60 rounded-2xl overflow-hidden flex flex-col" style="box-shadow: 0 2px 8px rgba(10,15,30,0.04);">
        <div class="px-5 py-4 flex items-center justify-between border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Menunggu Verifikasi
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5" id="pending-subtitle">{{ $pending }} laporan pending</p>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-3 max-h-[320px]" id="pending-list">
            <div class="flex items-center justify-center py-8">
                <div class="w-5 h-5 border-2 border-blue-200 border-t-blue-500 rounded-full animate-spin"></div>
            </div>
        </div>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const UPDATE_STATUS_BASE = '{{ url("admin/laporan/update-status") }}';
    let allDisasters = @json($allDisasters);
    let reportChart, currentPeriod = '7d';

    const ctx = document.getElementById('reportChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(59,111,232,0.12)');
    gradient.addColorStop(1, 'rgba(59,111,232,0)');

    function createChart(labels, dataTotal, dataVerified, dataPending) {
        if (reportChart) reportChart.destroy();
        reportChart = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets: [
                { label: 'Total', data: dataTotal, borderColor: '#3B6FE8', backgroundColor: gradient, borderWidth: 2.5, pointBackgroundColor: '#fff', pointBorderColor: '#3B6FE8', pointBorderWidth: 2, pointRadius: 4, fill: true, tension: 0.4 },
                { label: 'Verified', data: dataVerified, borderColor: '#10b981', borderWidth: 2, pointBackgroundColor: '#fff', pointBorderColor: '#10b981', pointBorderWidth: 2, pointRadius: 3, fill: false, tension: 0.4 },
                { label: 'Pending', data: dataPending, borderColor: '#f59e0b', borderWidth: 2, borderDash: [6,4], pointBackgroundColor: '#fff', pointBorderColor: '#f59e0b', pointBorderWidth: 2, pointRadius: 3, fill: false, tension: 0.4 }
            ]},
            options: { responsive: true, maintainAspectRatio: false, interaction: { intersect: false, mode: 'index' },
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0A0F1E', titleColor: '#E4F0F6', bodyColor: 'rgba(228,240,246,0.8)', padding: 12, cornerRadius: 12 } },
                scales: { y: { beginAtZero: true, grid: { color: 'rgba(10,15,30,0.04)' }, border: { display: false }, ticks: { precision: 0, color: '#94a3b8', font: { size: 11 } } }, x: { grid: { display: false }, border: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } } }
            }
        });
    }

    function computeStats(period) {
        const now = new Date();
        let cutoff = null;
        if (period === '1d') cutoff = new Date(now - 86400000);
        else if (period === '7d') cutoff = new Date(now - 7*86400000);
        else if (period === '30d') cutoff = new Date(now - 30*86400000);

        const filtered = cutoff ? allDisasters.filter(d => new Date(d.date) >= cutoff) : allDisasters;
        const s = { total: filtered.length, pending: filtered.filter(d => d.status==='PENDING').length, selesai: filtered.filter(d => !['PENDING','DECLINE'].includes(d.status)).length, decline: filtered.filter(d => d.status==='DECLINE').length, awas: filtered.filter(d => d.status==='AWAS').length, siaga1: filtered.filter(d => d.status==='SIAGA_1').length, siaga2: filtered.filter(d => d.status==='SIAGA_2').length };

        ['total','pending','selesai','decline','awas','siaga1','siaga2'].forEach(k => { const el = document.getElementById('stat-'+k); if(el) el.textContent = s[k]; });

        const todayStr = now.toISOString().split('T')[0];
        const todayItems = allDisasters.filter(d => d.date.startsWith(todayStr));
        const trendEl = document.getElementById('stat-total-trend');
        if (trendEl) trendEl.innerHTML = todayItems.length > 0 ? `<span class="text-emerald-600">↑ +${todayItems.length} hari ini</span>` : '';

        const labels = [], dataTotal = [], dataVerified = [], dataPending = [];
        if (period === '1d') {
            for (let i = 11; i >= 0; i--) { const end = new Date(now - i*2*3600000), start = new Date(end - 2*3600000); labels.push(end.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})); const items = allDisasters.filter(d => { const t = new Date(d.date); return t >= start && t < end; }); dataTotal.push(items.length); dataVerified.push(items.filter(d => !['PENDING','DECLINE'].includes(d.status)).length); dataPending.push(items.filter(d => d.status==='PENDING').length); }
        } else {
            const days = period==='30d'?30:(period==='7d'?7:14), step = period==='30d'?2:1;
            for (let i = days-1; i >= 0; i -= step) { const day = new Date(now - i*86400000); if(step>1&&(i-1)>=0){const d2=new Date(now-(i-1)*86400000);labels.push(day.toLocaleDateString('id-ID',{day:'2-digit',month:'short'})+' - '+d2.toLocaleDateString('id-ID',{day:'2-digit'}));}else{labels.push(day.toLocaleDateString('id-ID',{day:'2-digit',month:'short'}));} let t=0,v=0,p=0; for(let s2=0;s2<step&&(i-s2)>=0;s2++){const d2=new Date(now-(i-s2)*86400000),ds=d2.toISOString().split('T')[0],items=allDisasters.filter(d=>d.date.startsWith(ds));t+=items.length;v+=items.filter(d=>!['PENDING','DECLINE'].includes(d.status)).length;p+=items.filter(d=>d.status==='PENDING').length;} dataTotal.push(t);dataVerified.push(v);dataPending.push(p); }
        }
        createChart(labels, dataTotal, dataVerified, dataPending);
        const maxVal = Math.max(...dataTotal), peakEl = document.getElementById('chart-peak-info');
        if (peakEl) peakEl.textContent = maxVal > 0 ? `Puncak: ${labels[dataTotal.indexOf(maxVal)]} (${maxVal} laporan)` : 'Belum ada data';
    }

    createChart(@json($chartLabels), @json($chartData), @json($chartVerified), @json($chartPending));

    document.querySelectorAll('.period-btn').forEach(btn => { btn.addEventListener('click', () => { document.querySelectorAll('.period-btn').forEach(b => { b.classList.remove('active'); b.classList.add('text-slate-500'); }); btn.classList.add('active'); btn.classList.remove('text-slate-500'); currentPeriod = btn.dataset.period; computeStats(currentPeriod); }); });

    function renderPendingList(items) {
        const c = document.getElementById('pending-list'); if(!c) return;
        if (!items||items.length===0) { c.innerHTML = '<div class="flex flex-col items-center justify-center py-8 text-center"><div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3 bg-emerald-50"><i class="bi bi-check-all text-xl text-emerald-600"></i></div><p class="text-sm font-bold text-slate-700">Semua terverifikasi</p></div>'; return; }
        c.innerHTML = items.map(item => `<div class="pending-item" id="pending-item-${item.id}"><div class="flex items-start justify-between gap-2 mb-2"><h4 class="text-[13px] font-bold text-slate-900 leading-tight line-clamp-1">${item.judul}</h4><span class="shrink-0 text-[10px] text-slate-400">${item.tanggal}</span></div><p class="text-[11px] text-slate-500 mb-3"><i class="bi bi-geo-alt text-slate-300 text-[10px]"></i> ${(item.lokasi||'').substring(0,40)}</p><div class="flex items-center"><a href="/laporan/detail/${item.id}" class="px-4 py-1.5 text-[10px] font-bold text-white rounded-lg transition-all duration-200 hover:opacity-90 shadow-sm cursor-pointer" style="background: linear-gradient(135deg, #1e3a8a 0%, #3B6FE8 100%); box-shadow: 0 2px 6px rgba(59, 111, 232, 0.15);">Detail</a></div></div>`).join('');
    }

    window.adminUpdateStatus = function(id, status, btn) {
        btn.disabled = true; btn.innerHTML = '<span class="inline-block w-3 h-3 border border-current border-t-transparent rounded-full animate-spin"></span>';
        fetch(`${UPDATE_STATUS_BASE}/${id}`, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}, body:JSON.stringify({status, disaster_type:'unknown'}) })
        .then(r => { if(!r.ok) throw new Error(); const card = document.getElementById(`pending-item-${id}`); if(card){card.style.transition='all 0.3s';card.style.opacity='0';setTimeout(()=>{card.remove();if(!document.getElementById('pending-list').children.length)renderPendingList([]);},300);} fetchAdminStats(); })
        .catch(() => { btn.disabled=false; btn.textContent = status==='SIAGA_2'?'Verifikasi':'Tolak'; });
    };

    function fetchAdminStats() {
        fetch('/api/pending-reports').then(r=>r.json()).then(data => {
            renderPendingList(data.map(d=>({id:d.id,judul:d.title,tanggal:d.date,lokasi:d.reporter})));
            const sub = document.getElementById('pending-subtitle'); if(sub) sub.textContent = data.length+' laporan pending';
        }).catch(()=>{});
    }

    fetchAdminStats();
    setInterval(fetchAdminStats, 15000);
})();
</script>
