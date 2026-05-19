@extends('layouts.app')
@section('title', 'Data Laporan')
@section('subtitle', 'Daftar keseluruhan laporan bencana yang masuk ke sistem.')

@section('content')

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

    {{-- ═══════════════════════════════════════════════════
         STATS SUMMARY BAR
    ═══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-white/70 backdrop-blur-sm border border-slate-200/60 rounded-xl px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #E4F0F6, #C8DFF0);">
                <i class="bi bi-file-earmark-text text-sm" style="color: #1e3a8a;"></i>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total</p>
                <p class="text-lg font-extrabold text-slate-900">{{ count($laporans) }}</p>
            </div>
        </div>
        <div class="bg-white/70 backdrop-blur-sm border border-amber-200/60 rounded-xl px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                <i class="bi bi-hourglass-split text-sm text-amber-700"></i>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Pending</p>
                <p class="text-lg font-extrabold text-amber-600">{{ collect($laporans)->filter(fn($l) => strtolower($l['status']) === 'pending')->count() }}</p>
            </div>
        </div>
        <div class="bg-white/70 backdrop-blur-sm border border-emerald-200/60 rounded-xl px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
                <i class="bi bi-check-circle text-sm text-emerald-700"></i>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Verified</p>
                <p class="text-lg font-extrabold text-emerald-600">{{ collect($laporans)->filter(fn($l) => !in_array(strtolower($l['status']), ['pending', 'decline']))->count() }}</p>
            </div>
        </div>
        <div class="bg-white/70 backdrop-blur-sm border border-red-200/60 rounded-xl px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #fee2e2, #fecaca);">
                <i class="bi bi-x-circle text-sm text-red-700"></i>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Ditolak</p>
                <p class="text-lg font-extrabold text-red-600">{{ collect($laporans)->filter(fn($l) => strtolower($l['status']) === 'decline')->count() }}</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         MAIN TABLE CARD
    ═══════════════════════════════════════════════════ --}}
    <div class="bg-white/80 backdrop-blur-sm border border-slate-200/60 rounded-2xl shadow-sm overflow-hidden">
        
        {{-- Header with Gradient --}}
        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
             style="background: linear-gradient(135deg, #0A0F1E 0%, #162550 100%); border-bottom: 1px solid rgba(59,111,232,0.15);">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="bi bi-list-ul text-blue-300 text-xs"></i>
                    Semua Laporan
                </h3>
                <p class="text-[11px] mt-0.5" style="color: rgba(228,240,246,0.45);">Filter, cari, dan kelola laporan masuk</p>
            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="tableSearch" placeholder="Cari laporan..."
                           class="pl-8 pr-4 py-2 text-xs border-0 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all bg-white/90 backdrop-blur-sm w-44 shadow-sm"
                           style="color: #0f172a;">
                </div>
                <div class="relative">
                    <select id="tingkatFilter"
                            class="appearance-none pl-3 pr-7 py-2 text-xs font-medium bg-white/90 backdrop-blur-sm border-0 rounded-lg hover:bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/40 cursor-pointer shadow-sm"
                            style="color: #475569;">
                        <option value="">Semua Tingkat</option>
                        <option value="awas">Awas</option>
                        <option value="siaga 1">Siaga 1</option>
                        <option value="siaga 2">Siaga 2</option>
                    </select>
                    <i class="bi bi-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                </div>
                <div class="relative">
                    <select id="statusFilter"
                            class="appearance-none pl-3 pr-7 py-2 text-xs font-medium bg-white/90 backdrop-blur-sm border-0 rounded-lg hover:bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/40 cursor-pointer shadow-sm"
                            style="color: #475569;">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="awas">Awas</option>
                        <option value="siaga">Siaga</option>
                        <option value="resolved">Resolved</option>
                        <option value="decline">Decline</option>
                    </select>
                    <i class="bi bi-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" id="laporanTable">
                <thead class="border-b border-slate-100/80" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
                    <tr>
                        <th class="px-6 py-3.5 font-bold text-[11px] uppercase tracking-wider text-slate-500">Laporan</th>
                        <th class="px-6 py-3.5 font-bold text-[11px] uppercase tracking-wider text-slate-500">Lokasi</th>
                        <th class="px-6 py-3.5 font-bold text-[11px] uppercase tracking-wider text-slate-500">Tingkat</th>
                        <th class="px-6 py-3.5 font-bold text-[11px] uppercase tracking-wider text-slate-500 cursor-pointer select-none group" id="sortTanggal">
                            <div class="flex items-center gap-1">
                                Tanggal
                                <i class="bi bi-arrow-down-short text-slate-300 group-hover:text-blue-500 transition-colors text-base leading-none" id="sortIcon"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3.5 font-bold text-[11px] uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-6 py-3.5 font-bold text-[11px] uppercase tracking-wider text-slate-500 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/80">
                    @forelse($laporans as $laporan)
                        <tr class="hover:bg-blue-50/30 transition-all duration-200 group">
                            {{-- Judul + Reporter --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-110"
                                        style="background: linear-gradient(135deg, #E4F0F6, #C8DFF0);">
                                        <i class="bi bi-exclamation-triangle text-xs" style="color: #1e3a8a;"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-sm truncate max-w-[200px] group-hover:text-blue-800 transition-colors">{{ $laporan['judul'] }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $laporan['reporter_name'] ?? 'Unknown' }}</p>
                                    </div>
                                </div>
                            </td>
                            {{-- Lokasi --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 max-w-[220px]">
                                    <i class="bi bi-geo-alt text-slate-300 text-xs shrink-0"></i>
                                    <span class="text-slate-600 text-[12px] truncate">{{ \Illuminate\Support\Str::limit($laporan['lokasi'], 50) }}</span>
                                </div>
                            </td>
                            {{-- Tingkat --}}
                            <td class="px-6 py-4">
                                @if(!empty($laporan['tingkat_bencana']))
                                    @if($laporan['tingkat_bencana'] === 'Awas')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-red-500/10 text-red-700 border border-red-200/60">
                                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse shadow-sm shadow-red-500/50"></span>Awas
                                        </span>
                                    @elseif($laporan['tingkat_bencana'] === 'Siaga 1')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-500/10 text-amber-700 border border-amber-200/60">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shadow-sm shadow-amber-500/50"></span>Siaga 1
                                        </span>
                                    @elseif($laporan['tingkat_bencana'] === 'Siaga 2')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-500/10 text-blue-700 border border-blue-200/60">
                                            <span class="w-2 h-2 rounded-full bg-blue-500 shadow-sm shadow-blue-500/50"></span>Siaga 2
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/10 text-emerald-700 border border-emerald-200/60">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>{{ $laporan['tingkat_bencana'] }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-300 text-xs italic">Belum ditentukan</span>
                                @endif
                            </td>
                            {{-- Tanggal --}}
                            <td class="px-6 py-4">
                                <span class="text-slate-500 text-[12px] font-medium">{{ $laporan['tanggal'] }}</span>
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @php $statusLower = strtolower($laporan['status']); @endphp
                                @if($statusLower === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-amber-500/10 text-amber-700 border border-amber-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pending
                                    </span>
                                @elseif(in_array($statusLower, ['awas']))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-red-500/10 text-red-700 border border-red-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> {{ $laporan['status'] }}
                                    </span>
                                @elseif(in_array($statusLower, ['siaga 1', 'siaga 2']))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-blue-500/10 text-blue-700 border border-blue-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> {{ $laporan['status'] }}
                                    </span>
                                @elseif($statusLower === 'resolved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-emerald-500/10 text-emerald-700 border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Resolved
                                    </span>
                                @elseif($statusLower === 'decline')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-red-500/10 text-red-600 border border-red-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-slate-500/10 text-slate-600 border border-slate-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> {{ $laporan['status'] }}
                                    </span>
                                @endif
                            </td>
                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('laporan.show', ['id' => $laporan['id']]) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-[11px] font-bold text-slate-600 bg-white border border-slate-200/80 rounded-lg hover:border-blue-300 hover:text-blue-700 hover:bg-blue-50 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
                                    <i class="bi bi-eye text-xs"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-24 text-center">
                                <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #E4F0F6, #C8DFF0);">
                                    <i class="bi bi-inbox text-2xl" style="color: #1e3a8a;"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-800 mb-1">Tidak ada laporan</p>
                                <p class="text-xs text-slate-400 mb-4">Data laporan belum tersedia di sistem.</p>
                                <a href="{{ route('laporan.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white rounded-lg transition-all hover:-translate-y-0.5"
                                   style="background: linear-gradient(135deg, #1e3a8a, #3B6FE8); box-shadow: 0 2px 8px rgba(59,111,232,0.3);">
                                    <i class="bi bi-plus-lg"></i> Buat Laporan Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Footer --}}
        <div class="px-6 py-3.5 border-t border-slate-100/80 flex items-center justify-between text-xs" style="background: linear-gradient(180deg, #fafbfd 0%, #f5f7fa 100%);">
            <span class="text-slate-400">Menampilkan <span class="font-bold text-slate-700">{{ count($laporans) }}</span> laporan</span>
            <span class="text-slate-300 text-[10px]" id="filterInfo"></span>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    const searchInput   = document.getElementById('tableSearch');
    const statusFilter  = document.getElementById('statusFilter');
    const tingkatFilter = document.getElementById('tingkatFilter');
    const filterInfo    = document.getElementById('filterInfo');

    function filterTable() {
        const q       = searchInput?.value.toLowerCase() || '';
        const status  = statusFilter?.value.toLowerCase() || '';
        const tingkat = tingkatFilter?.value.toLowerCase() || '';
        let visible = 0;
        const total = document.querySelectorAll('#laporanTable tbody tr').length;

        document.querySelectorAll('#laporanTable tbody tr').forEach(row => {
            if (row.cells.length === 1) return; // empty state row

            const tingkatText = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase().trim() || '';
            const statusText  = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase().trim() || '';
            const matchSearch = row.textContent.toLowerCase().includes(q);
            const matchStatus = status === '' || statusText.includes(status);
            const matchTingkat = tingkat === '' || tingkatText.includes(tingkat);

            const show = matchSearch && matchStatus && matchTingkat;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Update filter info
        if (q || status || tingkat) {
            filterInfo.textContent = `${visible} dari ${total} ditampilkan`;
        } else {
            filterInfo.textContent = '';
        }
    }

    searchInput?.addEventListener('input', filterTable);
    statusFilter?.addEventListener('change', filterTable);
    tingkatFilter?.addEventListener('change', filterTable);

    // Sorting Logic
    let sortAsc = false;
    const sortBtn  = document.getElementById('sortTanggal');
    const sortIcon = document.getElementById('sortIcon');
    const tbody    = document.querySelector('#laporanTable tbody');

    sortBtn?.addEventListener('click', () => {
        sortAsc = !sortAsc;
        sortIcon.className = sortAsc
            ? 'bi bi-arrow-up-short text-blue-500 transition-colors text-base leading-none'
            : 'bi bi-arrow-down-short text-blue-500 transition-colors text-base leading-none';

        const rows     = Array.from(tbody.querySelectorAll('tr'));
        const dataRows = rows.filter(r => r.cells.length > 1);
        const emptyRow = rows.find(r => r.cells.length === 1);

        if (!dataRows.length) return;

        dataRows.sort((a, b) => {
            const dA = new Date(a.querySelector('td:nth-child(4)').textContent.trim());
            const dB = new Date(b.querySelector('td:nth-child(4)').textContent.trim());
            return sortAsc ? dA - dB : dB - dA;
        });

        dataRows.forEach(r => tbody.appendChild(r));
        if (emptyRow) tbody.appendChild(emptyRow);
    });
</script>
@endsection
