@extends('layout')
@section('title', 'Data Laporan')
@section('subtitle', 'Daftar keseluruhan laporan bencana yang masuk ke sistem.')


@section('content')

    @if(session('msg'))
        <div class="mb-6 p-4 rounded-lg flex items-center gap-3 text-sm font-medium
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
            <button onclick="this.parentElement.remove()" class="ml-auto opacity-70 hover:opacity-100"><i class="bi bi-x-lg"></i></button>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Toolbar --}}
        <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="relative w-full sm:w-64">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="tableSearch" placeholder="Cari laporan..." 
                       class="w-full pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all">
            </div>
            <div class="flex items-center gap-2">
                <button class="px-3 py-2 text-sm font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors">
                    <i class="bi bi-filter mr-1"></i> Filter
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap" id="laporanTable">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">Judul Laporan</th>
                        <th class="px-5 py-3 font-medium">Lokasi</th>
                        <th class="px-5 py-3 font-medium">Tingkat</th>
                        <th class="px-5 py-3 font-medium">Tanggal</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($laporans as $laporan)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-medium text-slate-900">{{ $laporan['judul'] }}</td>
                            <td class="px-5 py-3.5 text-slate-500"><i class="bi bi-geo-alt mr-1 text-slate-400"></i>{{ $laporan['lokasi'] }}</td>
                            <td class="px-5 py-3.5">
                                @if(!empty($laporan['tingkat_bencana']))
                                    @if($laporan['tingkat_bencana'] == 'Awas')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ $laporan['tingkat_bencana'] }}</span>
                                    @elseif(str_contains($laporan['tingkat_bencana'], 'Siaga'))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">{{ $laporan['tingkat_bencana'] }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ $laporan['tingkat_bencana'] }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $laporan['tanggal'] }}</td>
                            <td class="px-5 py-3.5">
                                @if(strtolower($laporan['status']) == 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Pending
                                    </span>
                                @elseif(strtolower($laporan['status']) == 'verified')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Decline
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('detail', ['id' => $laporan['id']]) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition-colors">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-slate-500">
                                <i class="bi bi-inbox text-3xl mb-3 block text-slate-300"></i>
                                <p class="text-sm font-medium text-slate-900">Tidak ada laporan</p>
                                <p class="text-xs mt-1">Data laporan belum tersedia di sistem.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Footer --}}
        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-between text-xs text-slate-500">
            <span>Menampilkan {{ count($laporans) }} laporan</span>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.getElementById('tableSearch')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#laporanTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endsection