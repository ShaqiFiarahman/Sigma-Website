@props([
    'total', 'pending', 'selesai', 'decline',
    'awas', 'siaga1', 'siaga2',
    'approvedVolunteers', 'totalVolunteers'
])

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

<style>
    .period-btn.active {
        background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%);
        color: white;
        box-shadow: 0 1px 4px rgba(30,58,138,0.2);
    }
</style>
