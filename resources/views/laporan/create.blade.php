@extends('layouts.app')
@section('title', 'Lapor Bencana')
@section('subtitle', 'Kirim laporan kejadian bencana di sekitar Anda.')

@section('content')
<div class="max-w-3xl space-y-8">

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
                    <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.6);">Lengkapi semua kolom yang diperlukan</p>
                </div>
            </div>
        </div>

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="mx-7 mt-6 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
                <p class="font-bold mb-1 flex items-center gap-2"><i class="bi bi-exclamation-circle-fill"></i> Terdapat kesalahan:</p>
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
                    <label for="judul" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                        Judul Laporan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="judul" id="judul" required
                           value="{{ old('judul') }}"
                           placeholder="Contoh: Banjir bandang di kawasan Perumahan Indah"
                           class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white">
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="deskripsi" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                        Deskripsi Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" required
                              placeholder="Ceritakan detail kejadian secara kronologis..."
                              class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all resize-y placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white">{{ old('deskripsi') }}</textarea>
                </div>

                {{-- Map Picker --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                        Pilih Lokasi di Peta <span class="text-red-500">*</span>
                    </label>
                    <div id="mapContainer" class="w-full h-72 rounded-xl border border-slate-200 overflow-hidden bg-slate-100 flex items-center justify-center"
                         style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                        <p class="text-slate-400 text-sm"><i class="bi bi-map me-1"></i> Memuat peta...</p>
                    </div>
                    <div id="coordDisplay" class="hidden mt-2 px-3 py-2 rounded-lg bg-purple-50 border border-purple-100 text-xs text-purple-700 font-medium flex items-center gap-2">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span id="coordText"></span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1">
                        <i class="bi bi-info-circle"></i>
                        Klik pada peta untuk menentukan lokasi kejadian
                    </p>
                </div>

                {{-- Hidden Latitude & Longitude --}}
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                {{-- Lokasi (auto-filled by reverse geocode) --}}
                <input type="hidden" name="lokasi" id="lokasi" value="{{ old('lokasi', 'Tidak diketahui') }}">

                {{-- Upload Foto --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                        Dokumentasi Foto <span class="text-slate-400 font-normal normal-case">(opsional)</span>
                    </label>
                    <label for="foto"
                           class="flex flex-col items-center justify-center w-full h-32 px-4 transition-all duration-200 border-2 border-slate-200 border-dashed rounded-xl cursor-pointer hover:border-purple-400 hover:bg-purple-50/30"
                           style="background: #F8FAFC;">
                        <input type="file" id="foto" name="foto" accept="image/*" class="hidden">

                        <div id="uploadPlaceholder" class="flex flex-col items-center space-y-1.5 text-center">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: #E4F0F6;">
                                <i class="bi bi-camera-fill text-lg" style="color: #3B6FE8;"></i>
                            </div>
                            <span class="text-sm text-slate-600 font-medium">Tambah Foto Kejadian</span>
                            <span class="text-xs text-slate-400">PNG, JPG, WEBP maks 5MB</span>
                        </div>

                        <div id="uploadPreview" class="hidden flex-col items-center justify-center w-full h-full">
                            <i class="bi bi-file-earmark-image text-3xl mb-1" style="color: #3B6FE8;"></i>
                            <p id="previewName" class="text-sm font-medium truncate max-w-xs" style="color: #3B6FE8;"></p>
                            <p class="text-xs text-slate-400 mt-0.5">Klik untuk mengganti</p>
                        </div>
                    </label>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-7 py-5 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl"
                 style="background: #FAFBFD;">
                <a href="{{ route('dashboard') }}"
                   class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all duration-200">
                    Batal
                </a>
                <button type="submit" id="submitBtn"
                        class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-2"
                        style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(102,80,164,0.3);">
                    <i class="bi bi-send-fill"></i> Kirim Laporan
                </button>
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════
         RIWAYAT LAPORAN (sesuai Android)
    ═══════════════════════════════════════════ --}}
    <div>
        <h2 class="flex items-center gap-2 text-lg font-bold mb-4" style="color: #0A0F1E;">
            <i class="bi bi-clock-history" style="color: #3B6FE8;"></i>
            Riwayat Laporan Anda
        </h2>

        @if($riwayat->isEmpty())
            <div class="bg-white border border-slate-200 rounded-2xl p-10 text-center text-slate-400">
                <i class="bi bi-inbox text-4xl mb-3 block" style="color: #CAC4D0;"></i>
                <p class="text-sm font-medium text-slate-500">Belum ada laporan yang dikirim</p>
                <p class="text-xs mt-1">Laporan yang Anda kirim akan muncul di sini</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($riwayat as $item)
                    @php
                        $statusColor = match($item->status) {
                            'AWAS'     => ['bg' => '#FFEBEE', 'text' => '#B71C1C', 'label' => 'Awas'],
                            'SIAGA_1'  => ['bg' => '#FFF3E0', 'text' => '#E65100', 'label' => 'Siaga 1'],
                            'SIAGA_2'  => ['bg' => '#E3F2FD', 'text' => '#0D47A1', 'label' => 'Siaga 2'],
                            'RESOLVED' => ['bg' => '#E8F5E9', 'text' => '#1B5E20', 'label' => 'Resolved'],
                            'DECLINE'  => ['bg' => '#FCE4EC', 'text' => '#880E4F', 'label' => 'Decline'],
                            default    => ['bg' => '#F3EDF7', 'text' => '#0A0F1E', 'label' => 'Pending'],
                        };
                        $acceptColor = match($item->status) {
                            'RESOLVED' => ['bg' => '#E8F5E9', 'text' => '#2E7D32', 'label' => 'Accepted'],
                            'DECLINE'  => ['bg' => '#FCE4EC', 'text' => '#C62828', 'label' => 'Rejected'],
                            'PENDING'  => ['bg' => '#FFF8E1', 'text' => '#F57F17', 'label' => 'Pending'],
                            default    => ['bg' => '#E8F5E9', 'text' => '#2E7D32', 'label' => 'Accepted'],
                        };
                    @endphp
                    <a href="{{ route('laporan.show', $item->id) }}"
                       class="block bg-white border border-slate-200 rounded-2xl p-4 hover:border-purple-300 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <p class="font-bold text-sm text-slate-900 leading-snug">{{ $item->title }}</p>
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($item->status !== 'PENDING')
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                          style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }};">
                                        {{ $statusColor['label'] }}
                                    </span>
                                @endif
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                      style="background: {{ $acceptColor['bg'] }}; color: {{ $acceptColor['text'] }};">
                                    {{ $acceptColor['label'] }}
                                </span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mb-2">
                            <i class="bi bi-clock me-1"></i>{{ $item->created_at->format('d M Y, H:i') }}
                        </p>
                        <p class="text-xs text-slate-500 line-clamp-2 mb-2">{{ $item->description }}</p>
                        @if($item->latitude && $item->longitude)
                            <p class="text-xs text-slate-400 flex items-center gap-1">
                                <i class="bi bi-geo-alt-fill" style="color: #3B6FE8;"></i>
                                Lat: {{ number_format($item->latitude, 7) }}, Long: {{ number_format($item->longitude, 7) }}
                            </p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap" async defer></script>
<script>
    let map, marker;

    window.initMap = function () {
        const defaultLocation = { lat: -7.5505, lng: 110.8063 }; // Surakarta

        map = new google.maps.Map(document.getElementById('mapContainer'), {
            zoom: 13,
            center: defaultLocation,
            mapTypeControl: false,
            fullscreenControl: true,
            streetViewControl: false,
        });

        map.addListener('click', (e) => {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();
            placeMarker(lat, lng);
            document.getElementById('latitude').value  = lat;
            document.getElementById('longitude').value = lng;

            // Show coordinate display
            const coordDisplay = document.getElementById('coordDisplay');
            document.getElementById('coordText').textContent =
                `Lat: ${lat.toFixed(7)}, Long: ${lng.toFixed(7)}`;
            coordDisplay.classList.remove('hidden');
            coordDisplay.classList.add('flex');

            // Reverse geocode
            new google.maps.Geocoder().geocode({ location: { lat, lng } }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    document.getElementById('lokasi').value = results[0].formatted_address;
                }
            });
        });
    };

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

    // File upload preview
    const fileInput   = document.getElementById('foto');
    const placeholder = document.getElementById('uploadPlaceholder');
    const previewBox  = document.getElementById('uploadPreview');
    const previewName = document.getElementById('previewName');

    fileInput?.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            previewName.textContent = file.name;
            placeholder.classList.add('hidden');
            previewBox.classList.remove('hidden');
            previewBox.classList.add('flex');
        }
    });

    // Submit validation
    const form      = document.getElementById('laporanForm');
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
        submitBtn.disabled  = true;
    });
</script>
@endsection
