@extends('layout')
@section('title', 'Data Laporan')

@section('page-actions')
    <a href="{{ route('create') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 text-white
              text-sm font-semibold shadow-[0_2px_8px_rgba(29,78,216,0.25)]
              hover:bg-blue-700 hover:-translate-y-px hover:shadow-[0_4px_14px_rgba(29,78,216,0.32)]
              transition-all duration-200">
        <i class="bi bi-plus-lg"></i> Buat Laporan
    </a>
@endsection

@section('content')

    {{-- ── FLASH MESSAGES ────────────────────────────────────── --}}
    @if(session('msg') == 'approved')
        <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl bg-emerald-50 border border-emerald-200
                    text-emerald-800 text-sm font-medium" role="alert">
            <i class="bi bi-check-circle-fill text-emerald-500 shrink-0"></i>
            <span>Laporan berhasil di-<strong>approve</strong> dan sudah terverifikasi.</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">
                <i class="bi bi-x text-base"></i>
            </button>
        </div>
    @elseif(session('msg') == 'rejected')
        <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl bg-red-50 border border-red-200
                    text-red-800 text-sm font-medium" role="alert">
            <i class="bi bi-x-circle-fill text-red-500 shrink-0"></i>
            <span>Laporan berhasil di-<strong>reject</strong>.</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600">
                <i class="bi bi-x text-base"></i>
            </button>
        </div>
    @elseif(session('msg') == 'created')
        <div class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl bg-blue-50 border border-blue-200
                    text-blue-800 text-sm font-medium" role="alert">
            <i class="bi bi-info-circle-fill text-blue-500 shrink-0"></i>
            <span>Laporan baru berhasil <strong>dibuat</strong> dan menunggu verifikasi.</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-blue-400 hover:text-blue-600">
                <i class="bi bi-x text-base"></i>
            </button>
        </div>
    @endif

    {{-- ── TABLE CARD ────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100 flex-wrap">
            <div>
                <p class="text-sm font-bold text-slate-900">Daftar Laporan Bencana</p>
                <p class="text-xs text-slate-400 mt-0.5">Semua laporan yang masuk ke sistem SIGMA</p>
            </div>
            {{-- Search --}}
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="tableSearch" placeholder="Cari laporan..."
                       class="pl-8 pr-4 py-2 rounded-lg border border-slate-200 bg-slate-50
                              text-sm font-medium text-slate-900 w-52
                              focus:outline-none focus:border-blue-400 focus:bg-white focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]
                              transition-all duration-200 placeholder:text-slate-400">
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="laporanTable">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-slate-400 uppercase tracking-wider w-12">#</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-slate-400 uppercase tracking-wider">Judul Laporan</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-slate-400 uppercase tracking-wider">Lokasi</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-slate-400 uppercase tracking-wider">Tingkat</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-[0.7rem] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-[0.7rem] font-bold text-slate-400 uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($laporans as $index => $laporan)
                        <tr class="hover:bg-blue-50/50 transition-colors duration-150 group">
                            <td class="px-4 py-3.5 text-slate-400 font-medium">{{ $index + 1 }}</td>
                            <td class="px-4 py-3.5">
                                <span class="font-semibold text-slate-800">{{ $laporan['judul'] }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="flex items-center gap-1.5 text-slate-500 text-[0.83rem]">
                                    <i class="bi bi-geo-alt-fill text-red-400 text-xs"></i>
                                    {{ $laporan['lokasi'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if(!empty($laporan['tingkat_bencana']))
                                    @if($laporan['tingkat_bencana'] == 'Awas')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.68rem] font-bold
                                                     bg-red-50 text-red-700 border border-red-200">
                                            <i class="bi bi-exclamation-triangle-fill text-[0.6rem]"></i> Awas
                                        </span>
                                    @elseif(str_contains($laporan['tingkat_bencana'], 'Siaga'))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.68rem] font-bold
                                                     bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="bi bi-exclamation-circle-fill text-[0.6rem]"></i> {{ $laporan['tingkat_bencana'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.68rem] font-bold
                                                     bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="bi bi-info-circle-fill text-[0.6rem]"></i> {{ $laporan['tingkat_bencana'] }}
                                        </span>
                                    @endif
                                @else
                                    @if(strtolower($laporan['status']) == 'decline' || strtolower($laporan['status']) == 'danger')
                                        <span class="px-2.5 py-1 rounded-full text-[0.68rem] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                            Tidak Ada
                                        </span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-[0.8rem] text-slate-400 font-medium">{{ $laporan['tanggal'] }}</td>
                            <td class="px-4 py-3.5">
                                @if(strtolower($laporan['status']) == 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.68rem] font-bold
                                                 bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="bi bi-hourglass-split text-[0.6rem]"></i> Pending
                                    </span>
                                @elseif(strtolower($laporan['status']) == 'verified')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.68rem] font-bold
                                                 bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="bi bi-check-circle-fill text-[0.6rem]"></i> Verified
                                    </span>
                                @elseif(strtolower($laporan['status']) == 'decline' || strtolower($laporan['status']) == 'danger')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.68rem] font-bold
                                                 bg-red-50 text-red-700 border border-red-200">
                                        <i class="bi bi-x-circle-fill text-[0.6rem]"></i> Decline
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[0.68rem] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        {{ $laporan['status'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <a href="{{ route('detail', ['id' => $laporan['id']]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                          bg-blue-50 text-blue-600 border border-blue-200 text-[0.72rem] font-bold
                                          hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:-translate-y-px
                                          hover:shadow-[0_4px_10px_rgba(29,78,216,0.25)]
                                          transition-all duration-200">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center
                                                text-3xl text-slate-300">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">Belum ada laporan</p>
                                    <p class="text-xs text-slate-400">Laporan yang masuk akan muncul di sini.</p>
                                    <a href="{{ route('create') }}"
                                       class="mt-1 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                              bg-blue-600 text-white text-xs font-semibold
                                              hover:bg-blue-700 transition-colors">
                                        <i class="bi bi-plus-lg"></i> Buat Laporan Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between px-6 py-3.5 border-t border-slate-100">
            <span class="text-xs text-slate-400">
                Menampilkan <strong class="text-slate-600">{{ count($laporans) }}</strong> laporan
            </span>
            <nav class="flex items-center gap-1" aria-label="Paginasi">
                <button class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 text-xs
                               flex items-center justify-center opacity-50 cursor-not-allowed">‹</button>
                <button class="w-7 h-7 rounded-lg bg-blue-600 text-white text-xs font-bold
                               flex items-center justify-center">1</button>
                <button class="w-7 h-7 rounded-lg border border-slate-200 text-slate-400 text-xs
                               flex items-center justify-center opacity-50 cursor-not-allowed">›</button>
            </nav>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    // Live search
    document.getElementById('tableSearch')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#laporanTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endsection