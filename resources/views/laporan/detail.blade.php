@extends('layouts.app')
@section('title', 'Detail Laporan')

@section('page-actions')
    <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 shadow-sm">
        <i class="bi bi-arrow-left text-xs"></i> Kembali
    </a>
@endsection

@section('content')
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
                                <i class="bi bi-person mr-1"></i>Huda Febri
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-white leading-tight">{{ $laporan['judul'] }}</h2>
                    </div>
                    
                    <div class="shrink-0">
                        @if(strtolower($laporan['status']) == 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pending
                            </span>
                        @elseif(strtolower($laporan['status']) == 'verified')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-400/20 text-red-300 border border-red-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Decline
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">

                {{-- Foto --}}
                <div class="mb-8 rounded-xl overflow-hidden border border-slate-100"
                     style="box-shadow: 0 2px 10px rgba(10,15,30,0.06);">
                    <img src="https://akcdn.detik.net.id/community/media/visual/2026/04/15/banjir-sukoharjo-1776224414251_169.jpeg?w=700&q=90" 
                         alt="Dokumentasi Bencana" class="w-full h-auto object-cover max-h-80">
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
                        @if($laporan['tingkat_bencana'] == 'Darurat')
                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold bg-red-50 text-red-700 border border-red-100">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>{{ $laporan['tingkat_bencana'] }}
                            </div>
                        @elseif($laporan['tingkat_bencana'] == 'Bahaya')
                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>{{ $laporan['tingkat_bencana'] }}
                            </div>
                        @else
                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>{{ $laporan['tingkat_bencana'] }}
                            </div>
                        @endif
                    @else
                        <p class="text-sm text-slate-400 italic">Belum ditentukan</p>
                    @endif
                </div>

                <div class="border-t border-slate-100 my-5"></div>

                @if(in_array(auth()->user()->role, ['admin', 'relawan']))
                    @if(strtolower($laporan['status']) == 'pending')
                        
                        <p class="text-sm text-slate-500 mb-4 leading-relaxed">Tinjau laporan ini dan tentukan tingkat keparahan sebelum disetujui.</p>

                        <form action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="status" value="Verified">
                            
                            <div class="mb-4">
                                <label for="tingkat_bencana" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Pilih Tingkat <span class="text-red-500">*</span>
                                </label>
                                <select name="tingkat_bencana" id="tingkat_bencana" required
                                        class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white text-slate-700">
                                    <option value="">Pilih tingkat darurat...</option>
                                    <option value="Darurat">🔴 Darurat (Awas)</option>
                                    <option value="Bahaya">🟡 Bahaya (Siaga 1)</option>
                                    <option value="Waspada">🔵 Waspada (Siaga 2)</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"
                                    style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); box-shadow: 0 2px 8px rgba(16,185,129,0.25);">
                                <i class="bi bi-check-circle"></i> Setujui Laporan
                            </button>
                        </form>

                        <form action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="Decline">
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-50 hover:border-red-300 transition-all duration-200">
                                <i class="bi bi-x-circle"></i> Tolak Laporan
                            </button>
                        </form>
                    @else
                        <div class="text-center p-5 rounded-xl border {{ strtolower($laporan['status']) == 'verified' ? 'border-emerald-100' : 'border-red-100' }}"
                             style="{{ strtolower($laporan['status']) == 'verified' ? 'background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);' : 'background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);' }}">
                            @if(strtolower($laporan['status']) == 'verified')
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3"
                                     style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                                    <i class="bi bi-check-lg text-2xl text-white"></i>
                                </div>
                                <p class="text-sm font-bold text-emerald-800">Laporan Telah Disetujui</p>
                                <p class="text-xs text-emerald-600 mt-1">Data ini sudah terverifikasi</p>
                            @else
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3"
                                     style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                                    <i class="bi bi-x-lg text-2xl text-white"></i>
                                </div>
                                <p class="text-sm font-bold text-red-800">Laporan Ditolak</p>
                                <p class="text-xs text-red-500 mt-1">Laporan ini tidak dapat diproses</p>
                            @endif
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
            const geoMapping = {
                'Bantul':       { lat: -7.8897, lng: 110.3289 },
                'Sleman':       { lat: -7.7233, lng: 110.3650 },
                'Kulon Progo':  { lat: -7.8333, lng: 110.1583 },
                'Gunung Kidul': { lat: -7.9999, lng: 110.6000 },
                'Yogyakarta':   { lat: -7.7956, lng: 110.3695 }
            };

            const lokasi = "{{ $laporan['lokasi'] }}";
            const center = geoMapping[lokasi] || { lat: -7.7956, lng: 110.3695 };
            
            const map = new google.maps.Map(document.getElementById("detailMap"), {
                zoom: 13,
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
                title: "{{ $laporan['judul'] }}"
            });
        }
    </script>
@endsection