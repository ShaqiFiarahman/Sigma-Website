@extends('layout')
@section('title', 'Detail Laporan')

@section('page-actions')
    <a href="{{ route('laporan') }}"
       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-200
              bg-white text-slate-500 text-xs font-semibold
              hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── MAIN DETAIL ─────────────────────────────────────── --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-7 md:p-8">

                {{-- Title + Status --}}
                <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
                    <h2 class="text-xl font-bold text-slate-900 leading-snug">{{ $laporan['judul'] }}</h2>

                    @if(strtolower($laporan['status']) == 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                                     bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                            <i class="bi bi-hourglass-split"></i> Pending
                        </span>
                    @elseif(strtolower($laporan['status']) == 'verified')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                                     bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                            <i class="bi bi-check-circle-fill"></i> Verified
                        </span>
                    @elseif(strtolower($laporan['status']) == 'decline' || strtolower($laporan['status']) == 'danger')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                                     bg-red-50 text-red-700 border border-red-200 shrink-0">
                            <i class="bi bi-x-circle-fill"></i> Decline
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                                     bg-slate-100 text-slate-500 border border-slate-200 shrink-0">
                            <i class="bi bi-info-circle"></i> {{ $laporan['status'] }}
                        </span>
                    @endif
                </div>

                {{-- Photo --}}
                <div class="relative rounded-xl overflow-hidden mb-7 group">
                    <img src="https://akcdn.detik.net.id/community/media/visual/2026/04/15/banjir-sukoharjo-1776224414251_169.jpeg?w=700&q=90"
                         alt="Dokumentasi Bencana"
                         class="w-full h-72 object-cover transition-transform duration-500 group-hover:scale-[1.02]">
                    <div class="absolute bottom-3 left-3 inline-flex items-center gap-1.5
                                bg-black/60 backdrop-blur text-white text-xs font-semibold
                                px-3 py-1.5 rounded-full">
                        <i class="bi bi-camera-fill"></i> Dokumentasi Lapangan
                    </div>
                </div>

                {{-- Info Grid --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                        <i class="bi bi-info-circle-fill text-blue-500 text-sm"></i>
                        <h5 class="text-sm font-bold text-slate-900">Informasi Laporan</h5>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <p class="text-[0.68rem] font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Pelapor</p>
                            <p class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                                <i class="bi bi-person-circle text-blue-500"></i> Huda Febri
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <p class="text-[0.68rem] font-bold text-slate-400 uppercase tracking-wider mb-2">Waktu Laporan</p>
                            <p class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                                <i class="bi bi-calendar2-event text-blue-500"></i> {{ $laporan['tanggal'] }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <p class="text-[0.68rem] font-bold text-slate-400 uppercase tracking-wider mb-2">Tingkat Bencana</p>
                            <div class="flex items-center gap-2">
                                @if(!empty($laporan['tingkat_bencana']))
                                    @if($laporan['tingkat_bencana'] == 'Awas')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                            <i class="bi bi-exclamation-triangle-fill text-[0.6rem]"></i> {{ $laporan['tingkat_bencana'] }}
                                        </span>
                                    @elseif(str_contains($laporan['tingkat_bencana'], 'Siaga'))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="bi bi-exclamation-circle-fill text-[0.6rem]"></i> {{ $laporan['tingkat_bencana'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="bi bi-info-circle-fill text-[0.6rem]"></i> {{ $laporan['tingkat_bencana'] }}
                                        </span>
                                    @endif
                                @else
                                    @if(strtolower($laporan['status']) == 'decline' || strtolower($laporan['status']) == 'danger')
                                        <span class="text-xs text-slate-400 font-medium">Tidak Ada</span>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">Belum Ditetapkan</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <p class="text-[0.68rem] font-bold text-slate-400 uppercase tracking-wider mb-2">Lokasi Kejadian</p>
                            <p class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-red-500"></i> {{ $laporan['lokasi'] }}
                            </p>
                        </div>

                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                        <i class="bi bi-align-left text-blue-500 text-sm"></i>
                        <h5 class="text-sm font-bold text-slate-900">Deskripsi Kejadian</h5>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed text-justify">{{ $laporan['deskripsi'] }}</p>
                </div>

            </div>
        </div>
    </div>

    {{-- ── SIDEBAR ACTION ───────────────────────────────────── --}}
    <div class="lg:sticky lg:top-[84px] self-start">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-base">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900 leading-none">Tindakan Admin</p>
                    <p class="text-xs text-slate-400 mt-0.5">Kelola status laporan ini</p>
                </div>
            </div>

            <div class="p-5">
                @if(strtolower($laporan['status']) == 'pending')

                    <p class="text-xs text-slate-500 leading-relaxed bg-slate-50 border border-slate-100 rounded-xl p-3 mb-5">
                        Tinjau laporan ini dengan seksama. Tentukan tingkat bencana dan berikan keputusan untuk diteruskan ke tim lapangan.
                    </p>

                    {{-- Approve Form --}}
                    <form action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="status" value="Verified">

                        <label for="tingkat_bencana" class="block text-xs font-bold text-slate-700 mb-2">
                            Tingkat Darurat <span class="text-red-500">*</span>
                        </label>
                        <select name="tingkat_bencana" id="tingkat_bencana" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50
                                       text-sm font-medium text-slate-800 mb-4
                                       focus:outline-none focus:border-blue-400 focus:bg-white
                                       focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)] transition-all">
                            <option value="">Pilih tingkat darurat...</option>
                            <option value="Awas">🔴 Awas — Tertinggi / Kritis</option>
                            <option value="Siaga 1">🟠 Siaga 1 — Sangat Bahaya</option>
                            <option value="Siaga 2">🟡 Siaga 2 — Bahaya</option>
                            <option value="Waspada">🔵 Waspada — Potensi Bahaya</option>
                        </select>

                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 py-3 rounded-xl
                                       bg-emerald-600 text-white text-sm font-bold
                                       shadow-[0_2px_8px_rgba(5,150,105,0.25)]
                                       hover:bg-emerald-700 hover:-translate-y-0.5
                                       hover:shadow-[0_4px_14px_rgba(5,150,105,0.3)]
                                       transition-all duration-200">
                            <i class="bi bi-check-circle-fill"></i> Approve Laporan
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="relative text-center my-4">
                        <div class="absolute inset-y-1/2 left-0 right-0 h-px bg-slate-200"></div>
                        <span class="relative bg-white px-3 text-xs text-slate-400 font-semibold">atau</span>
                    </div>

                    {{-- Decline Form --}}
                    <form action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="Decline">
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 py-3 rounded-xl
                                       border-[1.5px] border-red-200 text-red-600 bg-transparent text-sm font-bold
                                       hover:bg-red-50 hover:border-red-400 hover:-translate-y-0.5
                                       transition-all duration-200">
                            <i class="bi bi-x-circle-fill"></i> Tolak Laporan
                        </button>
                    </form>

                @else

                    {{-- Processed State --}}
                    <div class="flex flex-col items-center text-center p-5 bg-slate-50 border border-slate-100 rounded-xl mb-5">
                        @if(strtolower($laporan['status']) == 'verified')
                            <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center text-3xl text-emerald-500 mb-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-800 mb-1">Laporan Disetujui</p>
                            <p class="text-xs text-slate-400 leading-relaxed">Laporan ini telah diverifikasi dan diteruskan ke tim lapangan.</p>
                        @else
                            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center text-3xl text-red-500 mb-3">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-800 mb-1">Laporan Ditolak</p>
                            <p class="text-xs text-slate-400 leading-relaxed">Laporan ini telah ditolak dan tidak diproses lebih lanjut.</p>
                        @endif
                    </div>

                @endif

                {{-- Admin Notes --}}
                <div class="border-t border-slate-100 pt-4 mt-4">
                    <label for="adminNotes" class="block text-xs font-bold text-slate-700 mb-2">
                        <i class="bi bi-pencil-square text-blue-500 mr-1"></i>
                        Catatan Admin <span class="font-normal text-slate-400">(Opsional)</span>
                    </label>
                    <textarea id="adminNotes" rows="3"
                              placeholder="Tambahkan catatan alasan persetujuan atau penolakan..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50
                                     text-sm font-medium text-slate-800 placeholder:text-slate-400
                                     focus:outline-none focus:border-blue-400 focus:bg-white
                                     focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]
                                     transition-all resize-none"></textarea>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection