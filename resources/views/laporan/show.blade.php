@extends('layouts.app')
@section('title', 'Detail Laporan')
@section('subtitle', 'Pantau detail laporan dan lokasi kejadian.')

@section('page-actions')
    <x-ui.back-button :useHistory="true" />
@endsection

@section('content')

@php
    $s = $laporan['status_raw'];
    $borderColor = match($s) {
        'AWAS'     => 'border-l-red-500',
        'SIAGA_1'  => 'border-l-orange-500',
        'SIAGA_2'  => 'border-l-violet-500',
        'RESOLVED' => 'border-l-emerald-500',
        'DECLINE'  => 'border-l-slate-400',
        default    => 'border-l-amber-400',
    };

    $typeIcon = $laporan['type_icon'];
    $typeColor = $laporan['type_color'];
    $typeName = $laporan['type_name'];
@endphp

{{-- Flash message --}}
@if(session('msg'))
    <div class="mb-5 p-4 rounded-xl flex items-center gap-3 text-sm font-medium
        {{ session('msg') === 'approved' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' }}">
        @if(session('msg') === 'approved')
            <i class="bi bi-check-circle-fill text-emerald-500"></i> Status laporan berhasil diperbarui.
        @else
            <i class="bi bi-x-circle-fill text-red-500"></i> Laporan berhasil ditolak.
        @endif
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100"><i class="bi bi-x-lg"></i></button>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kiri: Detail Utama --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Card Utama --}}
        <div class="bg-white border border-slate-200/80 border-l-4 {{ $borderColor }} rounded-2xl overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

            {{-- Header --}}
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <h2 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight">{{ $laporan['judul'] }}</h2>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                            <span class="flex items-center gap-1"><i class="bi bi-calendar2-event text-slate-400"></i> {{ $laporan['tanggal'] }}</span>
                            <span class="flex items-center gap-1"><i class="bi bi-person text-slate-400"></i> {{ $laporan['reporter_name'] }}</span>
                        </div>
                    </div>
                    {{-- Status Badge --}}
                    @if($s === 'PENDING')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                        </span>
                    @elseif($s === 'DECLINE')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Ditolak
                        </span>
                    @elseif($s === 'AWAS')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-200 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Awas
                        </span>
                    @elseif($s === 'SIAGA_1')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Siaga 1
                        </span>
                    @elseif($s === 'SIAGA_2')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold bg-violet-50 text-violet-700 border border-violet-200 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span> Siaga 2
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Terverifikasi
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">

                {{-- Foto --}}
                @php
                    $photos = [];
                    if (!empty($laporan['photo_url'])) {
                        $decoded = json_decode($laporan['photo_url'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $photos = $decoded;
                        } else {
                            $photos = [$laporan['photo_url']];
                        }
                    }
                @endphp

                @if(count($photos) > 0)
                    <div class="rounded-xl overflow-hidden border border-slate-100" style="box-shadow: 0 2px 8px rgba(10,15,30,0.05);">
                        {{-- Main photo --}}
                        <img src="{{ $photos[0] }}"
                             alt="Dokumentasi Bencana"
                             class="w-full object-cover cursor-pointer"
                             style="max-height: 340px;"
                             loading="lazy"
                             onclick="window.open(this.src, '_blank')">
                        {{-- Thumbnails if multiple --}}
                        @if(count($photos) > 1)
                            <div class="flex gap-2 p-2 bg-slate-50 border-t border-slate-100">
                                @foreach($photos as $i => $photo)
                                    <img src="{{ $photo }}"
                                         alt="Foto {{ $i + 1 }}"
                                         class="w-16 h-16 object-cover rounded-lg border border-slate-200 cursor-pointer {{ $i === 0 ? 'ring-2 ring-blue-400' : '' }}"
                                         loading="lazy"
                                         onclick="this.closest('.rounded-xl').querySelector('img:first-child').src = this.src">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="w-full h-40 flex flex-col items-center justify-center bg-slate-50 text-slate-400 rounded-xl border border-slate-100">
                        <i class="bi bi-image text-3xl mb-2"></i>
                        <p class="text-xs">Tidak ada foto dokumentasi</p>
                    </div>
                @endif

                {{-- Deskripsi --}}
                <div>
                    <h3 class="text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-text-paragraph text-slate-400"></i> Deskripsi
                    </h3>
                    <div class="text-sm text-slate-600 leading-relaxed p-4 bg-slate-50 rounded-xl border border-slate-100">
                        @if($laporan['deskripsi'])
                            <p>{{ $laporan['deskripsi'] }}</p>
                        @else
                            <p class="text-slate-400 italic">Belum ada deskripsi lengkap.</p>
                        @endif
                    </div>
                </div>

                {{-- Lokasi --}}
                <div>
                    <h3 class="text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-geo-alt text-slate-400"></i> Lokasi Kejadian
                    </h3>
                    <div class="p-4 rounded-xl border border-slate-100 bg-slate-50">
                        <p class="text-sm font-semibold text-slate-800">{{ $laporan['location'] ?? $laporan['lokasi'] ?? 'Lokasi tidak diketahui' }}</p>
                        @if($laporan['latitude'] && $laporan['longitude'])
                            <p class="text-[11px] text-slate-400 mt-1 font-mono">{{ round($laporan['latitude'], 6) }}, {{ round($laporan['longitude'], 6) }}</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Kanan: Panel --}}
    <div class="lg:col-span-1 space-y-5">

        {{-- Info Pelapor --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-person-circle" style="color: #3B6FE8;"></i> Informasi Pelapor
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Pelapor</span>
                    <span class="font-semibold text-slate-800">{{ $laporan['reporter_name'] }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Dilaporkan</span>
                    <span class="font-semibold text-slate-800">{{ $laporan['tanggal'] }}</span>
                </div>
                @if(!empty($laporan['tingkat_bencana']))
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Tingkat</span>
                        <span class="font-semibold text-slate-800">{{ $laporan['tingkat_bencana'] }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Aksi Cepat --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-lightning-fill text-amber-500"></i> Aksi Cepat
                </h3>
            </div>
            <div class="p-5 space-y-2">
                <button type="button"
                        onclick="window.open('https://www.google.com/maps/dir/?api=1&destination={{ $laporan['latitude'] }},{{ $laporan['longitude'] }}', '_blank')"
                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-all cursor-pointer">
                    <i class="bi bi-signpost-2 text-sm"></i> Petunjuk Arah
                </button>
                <button type="button"
                        onclick="navigator.share ? navigator.share({title: '{{ addslashes($laporan['judul']) }}', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Link disalin!'))"
                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition-all cursor-pointer">
                    <i class="bi bi-share text-sm"></i> Bagikan Laporan
                </button>
            </div>
        </div>

        {{-- Panel Tindakan (Admin only) --}}
        @if(strtolower(auth()->user()->role) === 'admin')
        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <h3 class="text-sm font-semibold text-slate-800">Panel Tindakan</h3>
            </div>
            <div class="p-5">
                    @php 
                        $currentStatus = $laporan['status_raw'] ?? 'PENDING'; 
                        $currentType = $laporan['disaster_type'] ?? 'unknown';
                    @endphp

                    <p class="text-xs text-slate-500 mb-4">
                        {{ $currentStatus === 'PENDING' ? 'Tinjau laporan ini, tentukan jenis bencana dan statusnya.' : 'Update status sesuai kondisi terkini.' }}
                    </p>

                    <form id="updateStatusForm" action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1.5">Jenis Bencana</label>
                            <select name="disaster_type" required
                                    class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white text-slate-700">
                                <option value="unknown"    {{ $currentType === 'unknown'    ? 'selected' : '' }}>Belum Diketahui</option>
                                <option value="flood"      {{ $currentType === 'flood'      ? 'selected' : '' }}>Banjir</option>
                                <option value="fire"       {{ $currentType === 'fire'       ? 'selected' : '' }}>Kebakaran</option>
                                <option value="earthquake" {{ $currentType === 'earthquake' ? 'selected' : '' }}>Gempa Bumi</option>
                                <option value="landslide"  {{ $currentType === 'landslide'  ? 'selected' : '' }}>Tanah Longsor</option>
                                <option value="tsunami"    {{ $currentType === 'tsunami'    ? 'selected' : '' }}>Tsunami</option>
                                <option value="storm"      {{ $currentType === 'storm'      ? 'selected' : '' }}>Badai</option>
                                <option value="volcano"    {{ $currentType === 'volcano'    ? 'selected' : '' }}>Gunung Meletus</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1.5">Status</label>
                            <select name="status" required
                                    class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white text-slate-700">
                                <option value="PENDING"  {{ $currentStatus === 'PENDING'  ? 'selected' : '' }}>Pending</option>
                                <option value="AWAS"     {{ $currentStatus === 'AWAS'     ? 'selected' : '' }}>Awas</option>
                                <option value="SIAGA_1"  {{ $currentStatus === 'SIAGA_1'  ? 'selected' : '' }}>Siaga 1</option>
                                <option value="SIAGA_2"  {{ $currentStatus === 'SIAGA_2'  ? 'selected' : '' }}>Siaga 2</option>
                                <option value="RESOLVED" {{ $currentStatus === 'RESOLVED' ? 'selected' : '' }}>Selesai</option>
                                <option value="DECLINE"  {{ $currentStatus === 'DECLINE'  ? 'selected' : '' }}>Tolak</option>
                            </select>
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 text-sm font-semibold text-white rounded-xl cursor-pointer"
                                style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                            Simpan
                        </button>
                    </form>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Custom Confirm Modal --}}
<div id="confirmModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl border border-slate-100/80 transform scale-95 transition-transform duration-200">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center mx-auto mb-5 shadow-lg shadow-emerald-500/20 transform rotate-3 hover:rotate-0 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Selesaikan Laporan?</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-6">Apakah Anda yakin ingin mengubah status laporan ini menjadi <strong>Selesai</strong>? Laporan yang diselesaikan akan ditandai sebagai teratasi.</p>
            <div class="flex items-center gap-2.5">
                <button type="button" id="confirmCancelBtn" class="flex-1 py-2.5 text-xs font-bold text-slate-600 border border-slate-200 bg-white rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">Batalkan</button>
                <button type="button" id="confirmOkBtn" class="flex-1 py-2.5 text-xs font-bold text-white rounded-xl shadow-md transition-all hover:opacity-95 cursor-pointer" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">Ya, Selesai</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showConfirmModal(callback) {
        const modal = document.getElementById('confirmModal');
        const content = modal.querySelector('.transform');
        const okBtn = document.getElementById('confirmOkBtn');
        const cancelBtn = document.getElementById('confirmCancelBtn');

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);

        const closeModal = () => {
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        };

        const newOkBtn = okBtn.cloneNode(true);
        const newCancelBtn = cancelBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(newOkBtn, okBtn);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

        newOkBtn.addEventListener('click', () => { closeModal(); callback(true); });
        newCancelBtn.addEventListener('click', () => { closeModal(); callback(false); });
        modal.onclick = (e) => { if (e.target === modal) { closeModal(); callback(false); } };
    }

    document.getElementById('updateStatusForm')?.addEventListener('submit', function(e) {
        const select = this.querySelector('select[name="status"]');
        if (select && select.value === 'RESOLVED') {
            e.preventDefault();
            showConfirmModal((confirmed) => {
                if (confirmed) {
                    this.submit();
                }
            });
        }
    });
</script>
@endsection
