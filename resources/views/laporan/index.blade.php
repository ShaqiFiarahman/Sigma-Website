@extends('layouts.app')
@section('title', 'Data Laporan')
@section('subtitle', 'Daftar keseluruhan laporan bencana yang masuk ke sistem.')


@section('content')

    @if(session('msg'))
        <div class="mb-6 p-4 rounded-xl flex items-center gap-3 text-sm font-medium
            {{ session('msg') == 'approved' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : '' }}
            {{ session('msg') == 'rejected' ? 'bg-red-50 text-red-800 border border-red-200' : '' }}
            {{ session('msg') == 'created' ? 'bg-blue-50 text-blue-800 border border-blue-200' : '' }}
        ">
            @if(session('msg') == 'approved')
                <i class="bi bi-check-circle-fill text-emerald-500"></i> Laporan berhasil diverifikasi.
            @elseif(session('msg') == 'rejected')
                <i class="bi bi-x-circle-fill text-red-500"></i> Laporan berhasil ditolak.
            @elseif(session('msg') == 'created')
                <i class="bi bi-info-circle-fill text-blue-500"></i> Laporan baru berhasil dibuat.
            @endif
            <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100 transition-opacity"><i class="bi bi-x-lg"></i></button>
        </div>
    @endif

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
         style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">
        
        {{-- Table Header Gradient --}}
        <div class="px-6 py-5 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
             style="background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 100%);">
            <div>
                <h3 class="text-sm font-semibold text-white">Semua Laporan</h3>
                <p class="text-xs mt-0.5" style="color: rgba(228,240,246,0.5);">Filter, cari, dan kelola laporan masuk</p>
            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="tableSearch" placeholder="Cari laporan..."
                           class="pl-8 pr-4 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all bg-white w-44"
                           style="color: #0f172a;">
                </div>
                <div class="relative">
                    <select id="tingkatFilter"
                            class="appearance-none pl-3 pr-7 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer"
                            style="color: #475569;">
                        <option value="">Semua Tingkat</option>
                        <option value="darurat">Darurat</option>
                        <option value="bahaya">Bahaya</option>
                        <option value="waspada">Waspada</option>
                    </select>
                    <i class="bi bi-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                </div>
                <div class="relative">
                    <select id="statusFilter"
                            class="appearance-none pl-3 pr-7 py-2 text-xs font-medium bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer"
                            style="color: #475569;">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="decline">Decline</option>
                    </select>
                    <i class="bi bi-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap" id="laporanTable">
                <thead class="border-b border-slate-100 text-slate-500" style="background: #F8FAFC;">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400">Judul Laporan</th>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400">Lokasi</th>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400">Tingkat</th>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400 cursor-pointer select-none group" id="sortTanggal">
                            <div class="flex items-center gap-1">
                                Tanggal
                                <i class="bi bi-arrow-down-short text-slate-300 group-hover:text-blue-500 transition-colors text-base leading-none" id="sortIcon"></i>
                            </div>
                        </th>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400">Status</th>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($laporans as $laporan)
                        <tr class="hover:bg-slate-50/70 transition-colors duration-150 group">
                            <td class="px-6 py-4 font-semibold text-slate-900 text-sm">{{ $laporan['judul'] }}</td>
                            <td class="px-6 py-4 text-slate-500 text-sm">
                                <span class="flex items-center gap-1.5">
                                    <i class="bi bi-geo-alt text-slate-300"></i>{{ $laporan['lokasi'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if(!empty($laporan['tingkat_bencana']))
                                    @if(in_array($laporan['tingkat_bencana'], ['Darurat', 'Awas']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>{{ $laporan['tingkat_bencana'] }}
                                        </span>
                                    @elseif(in_array($laporan['tingkat_bencana'], ['Bahaya', 'Siaga 1', 'Siaga 2']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $laporan['tingkat_bencana'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>{{ $laporan['tingkat_bencana'] }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-sm">{{ $laporan['tanggal'] }}</td>
                            <td class="px-6 py-4">
                                @if(strtolower($laporan['status']) == 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pending
                                    </span>
                                @elseif(in_array(strtolower($laporan['status']), ['verified', 'resolved']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $laporan['status'] }}
                                    </span>
                                @elseif(strtolower($laporan['status']) == 'decline')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Decline
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> {{ $laporan['status'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('laporan.show', ['id' => $laporan['id']]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:border-blue-300 hover:text-blue-700 hover:bg-blue-50 transition-all duration-200 shadow-sm">
                                    Detail <i class="bi bi-arrow-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center text-slate-400">
                                <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: #E4F0F6;">
                                    <i class="bi bi-inbox text-2xl" style="color: #1e3a8a;"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-800 mb-1">Tidak ada laporan</p>
                                <p class="text-xs text-slate-400">Data laporan belum tersedia di sistem.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400" style="background: #FAFBFD;">
            <span>Menampilkan <span class="font-semibold text-slate-600">{{ count($laporans) }}</span> laporan</span>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    const searchInput   = document.getElementById('tableSearch');
    const statusFilter  = document.getElementById('statusFilter');
    const tingkatFilter = document.getElementById('tingkatFilter');

    function filterTable() {
        const q       = searchInput?.value.toLowerCase() || '';
        const status  = statusFilter?.value.toLowerCase() || '';
        const tingkat = tingkatFilter?.value.toLowerCase() || '';

        document.querySelectorAll('#laporanTable tbody tr').forEach(row => {
            if (row.cells.length === 1) return;

            const tingkatText = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase().trim() || '';
            const statusText  = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase().trim() || '';
            const matchSearch = row.textContent.toLowerCase().includes(q);
            const matchStatus = status === '' || statusText.includes(status);

            let matchTingkat = true;
            if (tingkat !== '') {
                if (tingkat === 'darurat')  matchTingkat = tingkatText.includes('darurat') || tingkatText.includes('awas');
                else if (tingkat === 'bahaya')  matchTingkat = tingkatText.includes('bahaya') || tingkatText.includes('siaga 1');
                else if (tingkat === 'waspada') matchTingkat = tingkatText.includes('waspada') || tingkatText.includes('siaga 2');
            }

            row.style.display = (matchSearch && matchStatus && matchTingkat) ? '' : 'none';
        });
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