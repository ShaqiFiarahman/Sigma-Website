@extends('layouts.app')
@section('title', 'Detail Laporan')

@section('page-actions')
    <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 shadow-sm">
        <i class="bi bi-arrow-left text-xs"></i> Kembali
    </a>
@endsection

@section('content')

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
    <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

            {{-- Card gradient header --}}
            <div class="px-8 py-6 border-b border-slate-100"
                 style="background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 100%);">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="text-[10px] font-bold uppercase tracking-widest" style="color: rgba(228,240,246,0.5);">
                                <i class="bi bi-calendar2-event mr-1"></i>{{ $laporan['tanggal'] }}
                            </span>
                            <span class="text-slate-600">•</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest" style="color: rgba(228,240,246,0.5);">
                                <i class="bi bi-person mr-1"></i>{{ $laporan['reporter_name'] }}
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-white leading-tight">{{ $laporan['judul'] }}</h2>
                    </div>
                    
                    <div class="shrink-0">
                        @php
                            $s = $laporan['status']; // e.g. "Pending", "Awas", "Siaga 1", "Decline", "Resolved"
                        @endphp
                        @if($s === 'Pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pending
                            </span>
                        @elseif($s === 'Decline')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-400/20 text-red-300 border border-red-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Decline
                            </span>
                        @elseif($s === 'Awas')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-400/20 text-red-300 border border-red-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span> Awas
                            </span>
                        @elseif(in_array($s, ['Siaga 1', 'Siaga 2']))
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> {{ $s }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> {{ $s }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">

                {{-- Foto --}}
                <div class="mb-8 rounded-xl overflow-hidden border border-slate-100"
                     style="box-shadow: 0 2px 10px rgba(10,15,30,0.06);">
                    @if(!empty($laporan['photo_url']))
                        <img src="{{ $laporan['photo_url'] }}"
                             alt="Dokumentasi Bencana" class="w-full h-auto object-cover max-h-80">
                    @else
                        <div class="w-full h-48 flex flex-col items-center justify-center bg-slate-50 text-slate-400">
                            <i class="bi bi-image text-4xl mb-2"></i>
                            <p class="text-sm">Tidak ada foto dokumentasi</p>
                        </div>
                    @endif
                </div>

                {{-- Deskripsi & Lokasi --}}
                <div class="space-y-6">
                    <div class="p-4 rounded-xl border border-slate-100 flex items-start gap-3" style="background: #F8FAFC;">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5" style="background: #E4F0F6;">
                            <i class="bi bi-geo-alt text-sm" style="color: #1e3a8a;"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Lokasi Kejadian</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $laporan['lokasi'] }}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                            <i class="bi bi-text-paragraph text-slate-400"></i> Deskripsi Laporan
                        </h3>
                        <div class="prose prose-sm prose-slate max-w-none text-slate-600 leading-relaxed p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p>{{ $laporan['deskripsi'] }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Kanan: Panel Aksi --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">
            
            {{-- Panel header white --}}
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #E4F0F6;">
                    <i class="bi bi-shield-check text-xs" style="color: #1e3a8a;"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-900">Panel Tindakan</h3>
            </div>
            
            <div class="p-5">
                
                {{-- Tingkat Bencana badge --}}
                <div class="mb-5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2.5">Tingkat Bencana</p>
                    @if(!empty($laporan['tingkat_bencana']))
                        @if($laporan['tingkat_bencana'] === 'Awas')
                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold bg-red-50 text-red-700 border border-red-100">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>Awas
                            </div>
                        @elseif($laporan['tingkat_bencana'] === 'Siaga 1')
                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>Siaga 1
                            </div>
                        @elseif($laporan['tingkat_bencana'] === 'Siaga 2')
                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>Siaga 2
                            </div>
                        @else
                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>{{ $laporan['tingkat_bencana'] }}
                            </div>
                        @endif
                    @else
                        <p class="text-sm text-slate-400 italic">Belum ditentukan</p>
                    @endif
                </div>

                <div class="border-t border-slate-100 my-5"></div>

                @if(in_array(auth()->user()->role, ['admin', 'BNPB']))

                    @php
                        $currentStatus = $laporan['status']; // "Pending", "Awas", "Siaga 1", etc.
                    @endphp

                    {{-- Selalu tampilkan form update status untuk admin/BNPB --}}
                    <p class="text-xs text-slate-500 mb-3 leading-relaxed">
                        @if($currentStatus === 'Pending')
                            Tinjau laporan ini dan tentukan tingkat keparahan.
                        @else
                            Update status bencana sesuai kondisi terkini di lapangan.
                        @endif
                    </p>

                    <form action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                Ubah Status
                            </label>
                            <select name="status" required
                                    class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white text-slate-700">
                                <option value="">-- Pilih status --</option>
                                <option value="AWAS"     {{ $currentStatus === 'Awas'     ? 'selected' : '' }}>🔴 Awas (Darurat)</option>
                                <option value="SIAGA_1"  {{ $currentStatus === 'Siaga 1'  ? 'selected' : '' }}>🟡 Siaga 1 (Bahaya)</option>
                                <option value="SIAGA_2"  {{ $currentStatus === 'Siaga 2'  ? 'selected' : '' }}>🔵 Siaga 2 (Waspada)</option>
                                <option value="RESOLVED" {{ $currentStatus === 'Resolved' ? 'selected' : '' }}>✅ Resolved (Selesai)</option>
                                <option value="DECLINE"  {{ $currentStatus === 'Decline'  ? 'selected' : '' }}>❌ Decline (Tolak)</option>
                            </select>
                        </div>
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5"
                                style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); box-shadow: 0 2px 8px rgba(16,185,129,0.25);">
                            <i class="bi bi-arrow-repeat"></i> Update Status
                        </button>
                    </form>

                    {{-- Status saat ini --}}
                    @if($currentStatus !== 'Pending')
                        <div class="mt-2 p-3 rounded-xl text-xs font-medium text-center
                            {{ $currentStatus === 'Decline'  ? 'bg-red-50 text-red-700 border border-red-100' : '' }}
                            {{ $currentStatus === 'Awas'     ? 'bg-red-50 text-red-700 border border-red-100' : '' }}
                            {{ $currentStatus === 'Siaga 1'  ? 'bg-amber-50 text-amber-700 border border-amber-100' : '' }}
                            {{ $currentStatus === 'Siaga 2'  ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}
                            {{ $currentStatus === 'Resolved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : '' }}
                        ">
                            Status saat ini: <span class="font-bold">{{ $currentStatus }}</span>
                        </div>
                    @endif
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <i class="bi bi-info-circle text-slate-400 mb-2 block text-xl"></i>
                        <p class="text-xs text-slate-500 leading-relaxed">Anda masuk sebagai <b>{{ auth()->user()->role }}</b>. Hanya Admin dan Relawan yang dapat memverifikasi laporan ini.</p>
                    </div>
                @endif

            </div>
        </div>

        {{-- Map Lokasi --}}
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #E4F0F6;">
                    <i class="bi bi-map text-xs" style="color: #1e3a8a;"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-900">Lokasi pada Peta</h3>
            </div>
            <div class="p-4">
                <div id="detailMap" class="w-full h-56 rounded-xl border border-slate-100 overflow-hidden"></div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initDetailMap" async defer></script>
    <script>
        function initDetailMap() {
            const lat = {{ $laporan['latitude'] ?? -7.5505 }};
            const lng = {{ $laporan['longitude'] ?? 110.8063 }};
            const center = { lat, lng };
            
            const map = new google.maps.Map(document.getElementById("detailMap"), {
                zoom: 14,
                center: center,
                disableDefaultUI: true,
                zoomControl: true,
                styles: [
                    { featureType: "water",     elementType: "geometry", stylers: [{ color: "#c8dff0" }] },
                    { featureType: "landscape",  elementType: "geometry", stylers: [{ color: "#f0f4f8" }] },
                    { featureType: "poi",        stylers: [{ visibility: "off" }] },
                ]
            });

            new google.maps.Marker({
                position: center,
                map: map,
                title: "{{ addslashes($laporan['judul']) }}",
                animation: google.maps.Animation.DROP,
            });
        }
    </script>
@endsection