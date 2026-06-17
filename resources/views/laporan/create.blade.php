@extends('layouts.app')
@section('title', 'Lapor Bencana')
@section('subtitle', 'Kirim laporan kejadian bencana di sekitar Anda.')

@section('page-actions')
    <x-ui.back-button :route="auth()->check() && strtolower(auth()->user()->role) === 'admin' ? route('admin.dashboard') : route('dashboard')" />
@endsection

@section('content')
    <div class="max-w-7xl mx-auto mb-10">
        @guest
            {{-- prompt login buat guest --}}
            <div class="max-w-md mx-auto my-12 text-center animate-fade-up">
                <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-sm overflow-hidden"
                     style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 10px 30px -10px rgba(10,15,30,0.08);">
                    <i class="bi bi-megaphone-fill text-4xl text-blue-600 mb-5 block"></i>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Lapor Bencana</h2>
                    <p class="text-sm text-slate-500 mb-8 leading-relaxed">Anda harus login ke dalam akun SIGMA Anda terlebih dahulu untuk membuat laporan bencana.</p>
                    <a href="{{ route('login') }}" class="btn-primary w-full flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5"
                       style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 4px 12px rgba(30,58,138,0.25);">
                        <i class="bi bi-arrow-right text-base"></i> Login
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2">
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
                    style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

                    <div class="pl-6 pr-8 py-5 border-l-4 border-l-[#3B6FE8] border-b border-slate-100 bg-white">
                        <div class="flex items-center gap-2.5">
                            <i class="bi bi-megaphone-fill text-lg text-blue-600 shrink-0"></i>
                            <div>
                                <h2 class="text-base font-bold text-slate-800">Formulir Laporan Baru</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Lengkapi semua kolom yang diperlukan</p>
                            </div>
                        </div>
                    </div>

                    {{-- error validasi --}}
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

                    {{-- wajib pake enctype buat upload file --}}
                    <form action="{{ route('laporan.store') }}" method="POST" id="laporanForm"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="p-7 space-y-6">

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

                            <div>
                                <label for="deskripsi"
                                    class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Deskripsi Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea name="deskripsi" id="deskripsi" rows="4" required
                                    placeholder="Ceritakan detail kejadian secara kronologis..."
                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all resize-y placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white">{{ old('deskripsi') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Pilih Lokasi di Peta <span class="text-red-500">*</span>
                                </label>
                                <button type="button" id="btnMyLocation" class="w-full mb-3 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 flex items-center justify-center gap-2 hover:-translate-y-0.5" style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%); box-shadow: 0 4px 12px rgba(10,15,30,0.25);">
                                    <i class="bi bi-cursor-fill"></i> Gunakan Lokasi Saya
                                </button>
                                <div id="mapContainer"
                                    class="w-full h-72 sm:h-96 rounded-xl border border-slate-200 overflow-hidden bg-slate-100 flex items-center justify-center"
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
                                <p id="mapHelpText" class="text-xs text-slate-400 mt-1.5 flex items-center gap-1 transition-all duration-300">
                                    <i id="mapHelpIcon" class="bi bi-info-circle"></i>
                                    <span id="mapHelpSpan">Klik pada peta untuk menentukan lokasi kejadian</span>
                                </p>
                            </div>

                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Dokumentasi Foto <span class="text-red-500">*</span>
                                </label>
                                <label for="foto" id="photoUploadBox"
                                    class="flex flex-col items-center justify-center w-full h-32 px-4 transition-all duration-200 border-2 border-slate-200 border-dashed rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/30"
                                    style="background: #F8FAFC;">
                                    <input type="file" id="foto" name="foto[]" accept="image/*" class="hidden" multiple required>

                                    <div id="uploadPlaceholder" class="flex flex-col items-center space-y-1.5 text-center">
                                        <i class="bi bi-camera-fill text-2xl text-blue-600 mb-0.5 shrink-0"></i>
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
                                <p id="photoHelpText" class="text-xs text-slate-400 mt-1.5 flex items-center gap-1 transition-all duration-300">
                                    <i id="photoHelpIcon" class="bi bi-info-circle"></i>
                                    <span id="photoHelpSpan">Pilih berkas foto dokumentasi kejadian yang valid</span>
                                </p>
                            </div>

                        </div>

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


                @include('laporan._riwayat')
            </div>

            @include('laporan._tips')
            </div>
        @endguest
    </div>
@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"
        async defer></script>
    <script>
        (function() {
            const form = document.getElementById('laporanForm');
            if (!form) return;

            let map, marker;

            window.initMap = function () {
                const mapEl = document.getElementById('mapContainer');
                if (!mapEl) return;

                const defaultLocation = { lat: -7.5505, lng: 110.8063 }; // Surakarta

                map = new google.maps.Map(mapEl, {
                    zoom: 13,
                    center: defaultLocation,
                    disableDefaultUI: true,
                    gestureHandling: 'cooperative',
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

                    // reset styling warning
                    resetWarning();
                });
            };

            // tombol buat gunain lokasi user saat ini
            document.getElementById('btnMyLocation')?.addEventListener('click', function() {
                if (navigator.geolocation) {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Mendeteksi...';
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
                            
                            // reset styling warning
                            resetWarning();
                            
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

            function resetWarning() {
                const mapContainer = document.getElementById('mapContainer');
                const mapHelpText = document.getElementById('mapHelpText');
                const mapHelpIcon = document.getElementById('mapHelpIcon');
                const mapHelpSpan = document.getElementById('mapHelpSpan');

                if (mapContainer.classList.contains('shake-highlight')) {
                    mapContainer.classList.remove('shake-highlight');
                }
                if (mapHelpText && mapHelpIcon && mapHelpSpan) {
                    mapHelpText.className = "text-xs text-slate-400 mt-1.5 flex items-center gap-1 transition-all duration-300";
                    mapHelpIcon.className = "bi bi-info-circle";
                    mapHelpSpan.textContent = "Klik pada peta untuk menentukan lokasi kejadian";
                }
            }

            const fileInput = document.getElementById('foto');
            const placeholder = document.getElementById('uploadPlaceholder');
            const previewBox = document.getElementById('uploadPreview');
            const previewName = document.getElementById('previewName');

            fileInput?.addEventListener('change', function () {
                const files = this.files;
                const photoUploadBox = document.getElementById('photoUploadBox');
                const photoHelpText = document.getElementById('photoHelpText');
                const photoHelpIcon = document.getElementById('photoHelpIcon');
                const photoHelpSpan = document.getElementById('photoHelpSpan');

                // reset warning alert
                if (photoUploadBox.classList.contains('shake-highlight')) {
                    photoUploadBox.classList.remove('shake-highlight');
                }
                if (photoHelpText && photoHelpIcon && photoHelpSpan) {
                    photoHelpText.className = "text-xs text-slate-400 mt-1.5 flex items-center gap-1 transition-all duration-300";
                    photoHelpIcon.className = "bi bi-info-circle";
                    photoHelpSpan.textContent = "Pilih berkas foto dokumentasi kejadian yang valid";
                }

                if (files.length > 3) {
                    this.value = ''; // Reset
                    placeholder.classList.remove('hidden');
                    previewBox.classList.add('hidden');
                    previewBox.classList.remove('flex');

                    // efek getar dan highlight border
                    photoUploadBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    photoUploadBox.classList.remove('shake-highlight');
                    void photoUploadBox.offsetWidth; // trigger reflow
                    photoUploadBox.classList.add('shake-highlight');

                    // set teks bantuan buat warning
                    if (photoHelpText && photoHelpIcon && photoHelpSpan) {
                        photoHelpText.className = "text-xs text-red-500 font-bold mt-1.5 flex items-center gap-1.5 animate-pulse transition-all duration-300";
                        photoHelpIcon.className = "bi bi-exclamation-triangle-fill text-red-500";
                        photoHelpSpan.textContent = "Maksimal 3 foto yang boleh diunggah!";
                    }
                    return;
                }
                
                let totalSize = 0;
                for (let i = 0; i < files.length; i++) {
                    totalSize += files[i].size;
                }
                
                const totalSizeMB = totalSize / (1024 * 1024);
                if (totalSizeMB > 25) {
                    this.value = ''; // Reset
                    placeholder.classList.remove('hidden');
                    previewBox.classList.add('hidden');
                    previewBox.classList.remove('flex');

                    // efek getar dan highlight border
                    photoUploadBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    photoUploadBox.classList.remove('shake-highlight');
                    void photoUploadBox.offsetWidth; // trigger reflow
                    photoUploadBox.classList.add('shake-highlight');

                    // set teks bantuan buat warning
                    if (photoHelpText && photoHelpIcon && photoHelpSpan) {
                        photoHelpText.className = "text-xs text-red-500 font-extrabold mt-1.5 flex items-center gap-1.5 animate-pulse transition-all duration-300";
                        photoHelpIcon.className = "bi bi-exclamation-triangle-fill text-red-500";
                        photoHelpSpan.textContent = "Total ukuran file melebihi 25MB!";
                    }
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

            // Fungsi kompresi gambar menggunakan Canvas
            function compressImage(file, maxWidth, maxHeight, quality) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = (event) => {
                        const img = new Image();
                        img.src = event.target.result;
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;

                            // Atur ukuran proporsional gambar
                            if (width > height) {
                                if (width > maxWidth) {
                                    height = Math.round((height * maxWidth) / width);
                                    width = maxWidth;
                                }
                            } else {
                                if (height > maxHeight) {
                                    width = Math.round((width * maxHeight) / height);
                                    height = maxHeight;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;

                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                if (blob) {
                                    resolve(blob);
                                } else {
                                    reject(new Error('Gagal melakukan kompresi canvas'));
                                }
                            }, 'image/jpeg', quality);
                        };
                        img.onerror = (err) => reject(err);
                    };
                    reader.onerror = (err) => reject(err);
                });
            }

            const submitBtn = document.getElementById('submitBtn');
            form.addEventListener('submit', async (e) => {
                e.preventDefault(); // Hentikan kiriman form bawaan

                const lat = document.getElementById('latitude').value;
                const lng = document.getElementById('longitude').value;
                if (!lat || !lng) {
                    const mapContainer = document.getElementById('mapContainer');
                    const mapHelpText = document.getElementById('mapHelpText');
                    const mapHelpIcon = document.getElementById('mapHelpIcon');
                    const mapHelpSpan = document.getElementById('mapHelpSpan');

                    // scroll ke peta secara smooth
                    mapContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // trigger efek getar dan highlight border
                    mapContainer.classList.remove('shake-highlight');
                    void mapContainer.offsetWidth; // trigger reflow
                    mapContainer.classList.add('shake-highlight');

                    // ubah teks instruksi jadi warning merah kedip-kedip
                    if (mapHelpText && mapHelpIcon && mapHelpSpan) {
                        mapHelpText.className = "text-xs text-red-500 font-extrabold mt-1.5 flex items-center gap-1.5 animate-pulse transition-all duration-300";
                        mapHelpIcon.className = "bi bi-exclamation-triangle-fill text-red-500";
                        mapHelpSpan.textContent = "Silakan klik pada peta untuk menentukan lokasi kejadian terlebih dahulu!";
                    }
                    return;
                }

                // Ubah status tombol menjadi mengompres gambar
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengompres Gambar...';
                submitBtn.disabled = true;

                try {
                    const files = fileInput.files;
                    if (files && files.length > 0) {
                        const dt = new DataTransfer();
                        for (let i = 0; i < files.length; i++) {
                            const file = files[i];
                            // Kompres jika file adalah gambar
                            if (file.type.startsWith('image/')) {
                                try {
                                    const compressedBlob = await compressImage(file, 1200, 1200, 0.7);
                                    const compressedFile = new File([compressedBlob], file.name, {
                                        type: 'image/jpeg',
                                        lastModified: Date.now()
                                    });
                                    dt.items.add(compressedFile);
                                } catch (err) {
                                    console.error('Gagal mengompres gambar:', err);
                                    dt.items.add(file); // fallback ke file asli jika gagal kompres
                                }
                            } else {
                                dt.items.add(file);
                            }
                        }
                        fileInput.files = dt.files;
                    }
                } catch (err) {
                    console.error('Error saat proses kompresi:', err);
                }

                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim...';
                form.submit();
            });
        })();
    </script>
@endsection
