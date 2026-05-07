@extends('layout')
@section('title', 'Detail Laporan')

@section('page-actions')
    <a href="{{ route('laporan') }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition-colors shadow-sm">
        <i class="bi bi-arrow-left mr-1.5"></i> Kembali
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kiri: Detail Utama --}}
    <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8">
                
                {{-- Header Laporan --}}
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 leading-tight mb-2">{{ $laporan['judul'] }}</h2>
                        <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                            <span class="flex items-center gap-1.5"><i class="bi bi-calendar2-event"></i> {{ $laporan['tanggal'] }}</span>
                            <span class="text-slate-300">•</span>
                            <span class="flex items-center gap-1.5"><i class="bi bi-person"></i> Huda Febri</span>
                        </div>
                    </div>
                    
                    <div>
                        @if(strtolower($laporan['status']) == 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Pending
                            </span>
                        @elseif(strtolower($laporan['status']) == 'verified')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Decline
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Foto --}}
                <div class="mb-8 rounded-lg overflow-hidden bg-slate-100 border border-slate-200">
                    <img src="https://akcdn.detik.net.id/community/media/visual/2026/04/15/banjir-sukoharjo-1776224414251_169.jpeg?w=700&q=90" 
                         alt="Dokumentasi Bencana" class="w-full h-auto object-cover max-h-96">
                </div>

                {{-- Deskripsi & Lokasi --}}
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-2">Lokasi Kejadian</h3>
                        <p class="text-sm text-slate-700 flex items-start gap-2">
                            <i class="bi bi-geo-alt text-slate-400 mt-0.5"></i> {{ $laporan['lokasi'] }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-2">Deskripsi Laporan</h3>
                        <div class="prose prose-sm prose-slate max-w-none text-slate-700 leading-relaxed">
                            <p>{{ $laporan['deskripsi'] }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Kanan: Panel Aksi --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900">Panel Tindakan</h3>
            </div>
            
            <div class="p-5">
                
                {{-- Status Info --}}
                <div class="mb-5">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Tingkat Bencana</p>
                    @if(!empty($laporan['tingkat_bencana']))
                        <div class="inline-block px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium text-slate-800">
                            {{ $laporan['tingkat_bencana'] }}
                        </div>
                    @else
                        <p class="text-sm text-slate-400 italic">Belum ditentukan</p>
                    @endif
                </div>

                <div class="border-t border-slate-100 my-5"></div>

                @if(strtolower($laporan['status']) == 'pending')
                    
                    <p class="text-sm text-slate-600 mb-4 leading-relaxed">Tinjau laporan ini dan tentukan tingkat keparahan sebelum disetujui.</p>

                    <form action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="status" value="Verified">
                        
                        <div class="mb-4">
                            <label for="tingkat_bencana" class="block text-sm font-medium text-slate-900 mb-1.5">Pilih Tingkat <span class="text-red-500">*</span></label>
                            <select name="tingkat_bencana" id="tingkat_bencana" required
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all bg-white text-slate-700">
                                <option value="">Pilih tingkat darurat...</option>
                                <option value="Awas">Awas (Kritis)</option>
                                <option value="Siaga 1">Siaga 1 (Sangat Bahaya)</option>
                                <option value="Siaga 2">Siaga 2 (Bahaya)</option>
                                <option value="Waspada">Waspada (Potensi)</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                            Setujui Laporan
                        </button>
                    </form>

                    <form action="{{ route('laporan.update_status', $laporan['id']) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="Decline">
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-200 rounded-lg hover:bg-red-50 transition-colors shadow-sm">
                            Tolak Laporan
                        </button>
                    </form>

                @else
                    
                    <div class="text-center p-4 rounded-lg border {{ strtolower($laporan['status']) == 'verified' ? 'bg-emerald-50 border-emerald-100' : 'bg-red-50 border-red-100' }}">
                        @if(strtolower($laporan['status']) == 'verified')
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-2 text-emerald-600">
                                <i class="bi bi-check-lg text-xl"></i>
                            </div>
                            <p class="text-sm font-medium text-emerald-900">Laporan Telah Disetujui</p>
                        @else
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-2 text-red-600">
                                <i class="bi bi-x-lg text-xl"></i>
                            </div>
                            <p class="text-sm font-medium text-red-900">Laporan Ditolak</p>
                        @endif
                    </div>

                @endif

            </div>
        </div>

        {{-- Map Lokasi --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900">Lokasi pada Peta</h3>
            </div>
            <div class="p-5">
                <div id="detailMap" class="w-full h-64 rounded-lg border border-slate-200"></div>
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
                'Bantul': { lat: -7.8897, lng: 110.3289 },
                'Sleman': { lat: -7.7233, lng: 110.3650 },
                'Kulon Progo': { lat: -7.8333, lng: 110.1583 },
                'Gunung Kidul': { lat: -7.9999, lng: 110.6000 },
                'Yogyakarta': { lat: -7.7956, lng: 110.3695 }
            };

            const lokasi = "{{ $laporan['lokasi'] }}";
            const center = geoMapping[lokasi] || { lat: -7.7956, lng: 110.3695 };
            
            const map = new google.maps.Map(document.getElementById("detailMap"), {
                zoom: 13,
                center: center,
                disableDefaultUI: true,
                zoomControl: true,
            });

            new google.maps.Marker({
                position: center,
                map: map,
                title: "{{ $laporan['judul'] }}"
            });
        }
    </script>
@endsection