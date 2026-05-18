@extends('layouts.app')
@section('title', 'Lapor Bencana')
@section('subtitle', 'Kirim laporan kejadian bencana di sekitar Anda.')

@section('content')
    <div class="max-w-7xl mx-auto mb-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI: FORM --}}
            <div class="lg:col-span-2">
                {{-- ═══════════════════════════════════════════
                FORM LAPORAN
                ═══════════════════════════════════════════ --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
                    style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

                    {{-- Form header --}}
                    <div class="px-8 py-6 border-b border-slate-100"
                        style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 55%, #0f1f4a 100%);">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                                style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                                <i class="bi bi-megaphone-fill text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-white">Formulir Laporan Baru</h2>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.6);">Lengkapi semua kolom yang
                                    diperlukan</p>
                            </div>
                        </div>
                    </div>

                    {{-- Validation errors --}}
                    @if($errors->any())
                        <div class="mx-7 mt-6 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
                            <p class="font-bold mb-1 flex items-center gap-2"><i class="bi bi-exclamation-circle-fill"></i>
                                Terdapat kesalahan:</p>
                            <ul class="list-disc list-inside space-y-0.5 text-xs">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- enctype WAJIB untuk file upload --}}
                    <form action="{{ route('laporan.store') }}" method="POST" id="laporanForm"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="p-7 space-y-6">

                            {{-- Judul --}}
                            <div>
                                <label for="judul"
                                    class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Judul Laporan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="judul" id="judul" required autocomplete="off"
                                    value="{{ old('judul') }}"
                                    placeholder="Contoh: Banjir bandang di kawasan Perumahan Indah"
                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white">
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <label for="deskripsi"
                                    class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Deskripsi Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea name="deskripsi" id="deskripsi" rows="4" required
                                    placeholder="Ceritakan detail kejadian secara kronologis..."
                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all resize-y placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white">{{ old('deskripsi') }}</textarea>
                            </div>

                            {{-- Map Picker --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Pilih Lokasi di Peta <span class="text-red-500">*</span>
                                </label>
                                <button type="button" id="btnMyLocation" class="w-full mb-3 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 flex items-center justify-center gap-2 hover:-translate-y-0.5" style="background: linear-gradient(135deg, #5046E5 0%, #3B82F6 100%); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);">
                                    <i class="bi bi-geo-alt-fill"></i> Gunakan Lokasi Saya
                                </button>
                                <div id="mapContainer"
                                    class="w-full h-96 rounded-xl border border-slate-200 overflow-hidden bg-slate-100 flex items-center justify-center"
                                    style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                                    <p class="text-slate-400 text-sm"><i class="bi bi-map me-1"></i> Memuat peta...</p>
                                </div>
                                <div id="locationDisplay" class="hidden mt-2 px-4 py-3 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-800 flex flex-col gap-1">
                                    <div class="flex items-center gap-2 font-bold">
                                        <i class="bi bi-geo-alt-fill text-blue-600"></i>
                                        <span>Lokasi Terpilih</span>
                                    </div>
                                    <div class="text-xs text-blue-600/80 flex gap-3 ml-6">
                                        <span id="coordLat"></span>
                                        <span id="coordLong"></span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1">
                                    <i class="bi bi-info-circle"></i>
                                    Klik pada peta untuk menentukan lokasi kejadian
                                </p>
                            </div>

                            {{-- Hidden Latitude & Longitude --}}
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">

                            {{-- Upload Foto --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Dokumentasi Foto <span class="text-red-500">*</span>
                                </label>
                                <label for="foto"
                                    class="flex flex-col items-center justify-center w-full h-32 px-4 transition-all duration-200 border-2 border-slate-200 border-dashed rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/30"
                                    style="background: #F8FAFC;">
                                    <input type="file" id="foto" name="foto[]" accept="image/*" class="hidden" multiple required>

                                    <div id="uploadPlaceholder" class="flex flex-col items-center space-y-1.5 text-center">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                            style="background: #E4F0F6;">
                                            <i class="bi bi-camera-fill text-lg" style="color: #3B6FE8;"></i>
                                        </div>
                                        <span class="text-sm text-slate-600 font-medium">Tambah Foto Kejadian</span>
                                        <span class="text-xs text-slate-400">Maksimal 3 foto, Total ukuran maks 25MB</span>
                                    </div>

                                    <div id="uploadPreview"
                                        class="hidden flex-col items-center justify-center w-full h-full">
                                        <i class="bi bi-file-earmark-image text-3xl mb-1" style="color: #3B6FE8;"></i>
                                        <p id="previewName" class="text-sm font-medium truncate max-w-xs"
                                            style="color: #3B6FE8;"></p>
                                        <p class="text-xs text-slate-400 mt-0.5">Klik untuk mengganti</p>
                                    </div>
                                </label>
                            </div>

                        </div>

                        {{-- Footer --}}
                        <div class="px-7 py-5 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl"
                            style="background: #FAFBFD;">
                            <a href="{{ route('dashboard') }}"
                                class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-blue-50 hover:text-blue-600 hover:border-blue-300 transition-all duration-200">
                                Batal
                            </a>
                            <button type="submit" id="submitBtn"
                                class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-2"
                                style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(30,58,138,0.3);">
                                <i class="bi bi-send-fill"></i> Kirim Laporan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-8">
                    <h2 class="flex items-center gap-2 text-lg font-bold mb-4" style="color: #0A0F1E;">
                        <i class="bi bi-clock-history" style="color: #3B6FE8;"></i>
                        Riwayat Laporan Anda
                    </h2>

                    @if($riwayat->isEmpty())
                        <div class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-200/80 rounded-2xl bg-slate-50/50">
                            <div class="w-14 h-14 rounded-full bg-white shadow-sm flex items-center justify-center mb-3 border border-slate-100">
                                <i class="bi bi-journal-text text-xl text-slate-300"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Laporan</h3>
                            <p class="text-xs text-slate-500 text-center max-w-xs leading-relaxed">Semua laporan kejadian yang Anda kirimkan akan direkam dan ditampilkan di sini.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($riwayat as $item)
                                @php
                                    $statusColor = match ($item->status) {
                                        'AWAS' => ['bg' => '#FFEBEE', 'text' => '#B71C1C', 'label' => 'Awas'],
                                        'SIAGA_1' => ['bg' => '#FFF3E0', 'text' => '#E65100', 'label' => 'Siaga 1'],
                                        'SIAGA_2' => ['bg' => '#E3F2FD', 'text' => '#0D47A1', 'label' => 'Siaga 2'],
                                        'RESOLVED' => ['bg' => '#E8F5E9', 'text' => '#1B5E20', 'label' => 'Resolved'],
                                        'DECLINE' => ['bg' => '#FCE4EC', 'text' => '#880E4F', 'label' => 'Decline'],
                                        default => ['bg' => '#FFF8E1', 'text' => '#F57F17', 'label' => 'Pending'],
                                    };
                                @endphp
                                <a href="{{ route('laporan.show', $item->id) }}"
                                    class="group relative block bg-white border border-slate-200/70 rounded-2xl p-4 hover:border-blue-400/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/10 overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    
                                    <div class="relative z-10">
                                        <div class="flex items-start justify-between gap-4 mb-2">
                                            <h3 class="font-bold text-sm text-slate-800 leading-snug group-hover:text-blue-600 transition-colors duration-200 line-clamp-1">{{ $item->title }}</h3>
                                            <div class="shrink-0 mt-0.5">
                                                <span class="inline-flex items-center justify-center text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider ring-1 ring-inset"
                                                    style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; --tw-ring-color: {{ $statusColor['text'] }}40;">
                                                    {{ $statusColor['label'] }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <p class="text-xs text-slate-500 line-clamp-1 mb-3 leading-relaxed">{{ $item->description }}</p>
                                        
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-medium text-slate-500 border-t border-slate-100/80 pt-3">
                                            <span class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-md border border-slate-100/50">
                                                <i class="bi bi-calendar-event text-blue-500/70"></i>
                                                {{ $item->created_at->format('d M Y, H:i') }}
                                            </span>
                                            
                                            @if($item->location)
                                                <span class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-md border border-slate-100/50 truncate max-w-[200px] lg:max-w-[300px]">
                                                    <i class="bi bi-geo-alt-fill text-red-400/80"></i>
                                                    {{ \Illuminate\Support\Str::limit($item->location, 35) }}
                                                </span>
                                            @elseif($item->latitude && $item->longitude)
                                                <span class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-md border border-slate-100/50 truncate">
                                                    <i class="bi bi-geo-alt-fill text-red-400/80"></i>
                                                    {{ number_format($item->latitude, 4) }}, {{ number_format($item->longitude, 4) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6 lg:sticky lg:top-24 self-start">

                {{-- Card 1: Tips & Panduan --}}
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
                    style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                            <i class="bi bi-lightbulb-fill text-amber-500"></i>
                            Tips Pelaporan
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        {{-- List of Tips --}}
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Lokasi Akurat</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Pastikan Anda mengklik peta atau menggunakan tombol deteksi lokasi sesuai dengan kejadian sebenarnya.</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Deskripsi Jelas</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Ceritakan kronologi singkat, perkiraan jumlah korban (jika ada), dan jenis bantuan yang mendesak.</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Foto Pendukung</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Unggah foto yang memperjelas kondisi di lapangan agar petugas lebih mudah melakukan validasi.</p>
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        {{-- Panduan Upload --}}
                        <div>
                            <h4 class="text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                                <i class="bi bi-info-circle text-slate-400"></i>
                                Panduan Upload Foto
                            </h4>
                            <div class="bg-slate-50 rounded-lg p-3 text-[11px] text-slate-600 space-y-1">
                                <div class="flex justify-between">
                                    <span>Format file:</span>
                                    <span class="font-medium text-slate-800">JPG, PNG, WEBP</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Jumlah & Ukuran:</span>
                                    <span class="font-medium text-slate-800">Maks 3 foto, total 25 MB</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Kualitas:</span>
                                    <span class="font-medium text-slate-800">Pastikan tidak buram</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
                    style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                            <i class="bi bi-telephone-fill text-red-500"></i>
                            Nomor Darurat
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-600">Panggilan Darurat</span>
                                <span class="font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-lg">112</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-600">Ambulans</span>
                                <span class="font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-lg">119</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-600">BASARNAS</span>
                                <span class="font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-lg">115</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-600">Pemadam Kebakaran</span>
                                <span class="font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-lg">113</span>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"
        async defer></script>
    <script>
        let map, marker;

        window.initMap = function () {
            const defaultLocation = { lat: -7.5505, lng: 110.8063 }; // Surakarta

            map = new google.maps.Map(document.getElementById('mapContainer'), {
                zoom: 13,
                center: defaultLocation,
                disableDefaultUI: true,
                gestureHandling: 'greedy',
                styles: [
                    { featureType: "poi", stylers: [{ visibility: "off" }] },
                    { featureType: "transit", stylers: [{ visibility: "off" }] },
                ]
            });

            map.addListener('click', (e) => {
                const lat = e.latLng.lat();
                const lng = e.latLng.lng();
                placeMarker(lat, lng);
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                const locDisplay = document.getElementById('locationDisplay');

                document.getElementById('coordLat').textContent = `Lat: ${lat.toFixed(4)}`;
                document.getElementById('coordLong').textContent = `Long: ${lng.toFixed(4)}`;
                locDisplay.classList.remove('hidden');
                locDisplay.classList.add('flex');


            });
        };

        // Button Gunakan Lokasi Saya
        document.getElementById('btnMyLocation')?.addEventListener('click', function() {
            if (navigator.geolocation) {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split"></i> Mendeteksi...';
                this.disabled = true;
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        placeMarker(lat, lng);
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                        
                        const locDisplay = document.getElementById('locationDisplay');

                        document.getElementById('coordLat').textContent = `Lat: ${lat.toFixed(4)}`;
                        document.getElementById('coordLong').textContent = `Long: ${lng.toFixed(4)}`;
                        locDisplay.classList.remove('hidden');
                        locDisplay.classList.add('flex');
                        

                        
                        this.innerHTML = originalText;
                        this.disabled = false;
                    },
                    (error) => {
                        alert('Gagal mendeteksi lokasi: ' + error.message);
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                );
            } else {
                alert('Browser Anda tidak mendukung geolokasi.');
            }
        });

        function placeMarker(lat, lng) {
            if (marker) marker.setMap(null);
            marker = new google.maps.Marker({
                position: { lat, lng },
                map,
                title: 'Lokasi Kejadian',
                animation: google.maps.Animation.DROP,
            });
            map.panTo({ lat, lng });
        }

        const fileInput = document.getElementById('foto');
        const placeholder = document.getElementById('uploadPlaceholder');
        const previewBox = document.getElementById('uploadPreview');
        const previewName = document.getElementById('previewName');

        fileInput?.addEventListener('change', function () {
            const files = this.files;
            if (files.length > 3) {
                alert('Maksimal 3 foto yang boleh diunggah.');
                this.value = ''; // Reset
                placeholder.classList.remove('hidden');
                previewBox.classList.add('hidden');
                previewBox.classList.remove('flex');
                return;
            }
            
            let totalSize = 0;
            for (let i = 0; i < files.length; i++) {
                totalSize += files[i].size;
            }
            
            const totalSizeMB = totalSize / (1024 * 1024);
            if (totalSizeMB > 25) {
                alert('Total ukuran file melebihi 25MB.');
                this.value = ''; // Reset
                placeholder.classList.remove('hidden');
                previewBox.classList.add('hidden');
                previewBox.classList.remove('flex');
                return;
            }
            
            if (files.length > 0) {
                previewName.textContent = `${files.length} file terpilih (${totalSizeMB.toFixed(2)} MB)`;
                placeholder.classList.add('hidden');
                previewBox.classList.remove('hidden');
                previewBox.classList.add('flex');
            } else {
                placeholder.classList.remove('hidden');
                previewBox.classList.add('hidden');
                previewBox.classList.remove('flex');
            }
        });

        const form = document.getElementById('laporanForm');
        const submitBtn = document.getElementById('submitBtn');
        form?.addEventListener('submit', (e) => {
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            if (!lat || !lng) {
                e.preventDefault();
                alert('Silakan klik pada peta untuk menentukan lokasi kejadian terlebih dahulu.');
                return;
            }
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim...';
            submitBtn.disabled = true;
        });
    </script>
@endsection