@extends('layouts.app')
@section('title', 'Edit Posko')
@section('subtitle', 'Perbarui informasi posko ' . $shelter->name)

@section('page-actions')
    <button type="button" onclick="history.back()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 shadow-sm cursor-pointer">
        <i class="bi bi-arrow-left text-xs"></i> Kembali
    </button>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Kiri: Form (3/5) --}}
        <div class="lg:col-span-3">
            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

                <form action="{{ route('admin.shelter.update', $shelter->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Photo Banner --}}
                    <div class="relative h-36 bg-slate-100 overflow-hidden group">
                        @if($shelter->photo_url)
                            <img src="{{ $shelter->photo_url }}" alt="{{ $shelter->name }}" class="w-full h-full object-cover" id="photoPreview">
                        @else
                            <div class="w-full h-full flex items-center justify-center" id="photoPlaceholder">
                                <p class="text-xs text-slate-400">Belum ada foto</p>
                            </div>
                            <img src="" alt="" class="w-full h-full object-cover hidden" id="photoPreview">
                        @endif
                        <label for="photoInput" class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/30 transition-all cursor-pointer">
                            <span class="opacity-0 group-hover:opacity-100 transition-opacity text-xs font-semibold text-white bg-black/50 px-3 py-1.5 rounded-lg">Ganti Foto</span>
                        </label>
                        <input type="file" name="photo" id="photoInput" accept="image/*" class="hidden">
                    </div>

                    <div class="p-6 space-y-6">

                        {{-- Section: Informasi Dasar --}}
                        <div>
                            <p class="text-xs font-bold text-slate-700 mb-3">Informasi Dasar</p>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Nama Posko</label>
                                    <input type="text" name="name" value="{{ old('name', $shelter->name) }}" required
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Alamat</label>
                                    <input type="text" name="address" id="addressInput" value="{{ old('address', $shelter->address) }}" placeholder="Ketik alamat atau cari di peta..."
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                                </div>
                                
                                {{-- Hidden coordinates inputs --}}
                                <input type="hidden" name="latitude" id="latitudeInput" value="{{ old('latitude', $shelter->latitude) }}">
                                <input type="hidden" name="longitude" id="longitudeInput" value="{{ old('longitude', $shelter->longitude) }}">

                                {{-- Map Location Picker --}}
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Pilih Lokasi Koordinat Posko</label>
                                    <div id="mapPicker" class="w-full rounded-xl border border-slate-200 overflow-hidden" style="height: 220px; box-shadow: 0 2px 8px rgba(10,15,30,0.04);"></div>
                                    <p class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1">
                                        <i class="bi bi-info-circle"></i>
                                        <span>Geser penanda merah atau klik pada peta untuk memposisikan koordinat posko secara akurat.</span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Nomor Telepon Kontak</label>
                                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $shelter->contact_phone) }}" placeholder="6281234567890"
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                                </div>
                            </div>
                        </div>

                        {{-- Section: Kapasitas --}}
                        <div>
                            <p class="text-xs font-bold text-slate-700 mb-3">Kapasitas</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Saat Ini</label>
                                    <input type="number" name="capacity_current" value="{{ old('capacity_current', $shelter->capacity_current) }}" required min="0"
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Maksimal</label>
                                    <input type="number" name="capacity_max" value="{{ old('capacity_max', $shelter->capacity_max) }}" required min="1"
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                                </div>
                            </div>
                        </div>

                        {{-- Section: Operasional --}}
                        <div>
                            <p class="text-xs font-bold text-slate-700 mb-3">Operasional</p>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Status</label>
                                    <select name="status" class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                                        <option value="Tersedia" {{ $shelter->status === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="Penuh" {{ $shelter->status === 'Penuh' ? 'selected' : '' }}>Penuh</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-500 mb-1">Kebutuhan Logistik <span class="text-slate-400">(pisahkan dengan koma)</span></label>
                                    <input type="text" name="logistics" value="{{ old('logistics', implode(', ', $shelter->logistics ?? [])) }}" placeholder="Sembako, Air Mineral, Selimut"
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2" style="background: #FAFBFD;">
                        <button type="button" onclick="history.back()" class="px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl cursor-pointer" style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(30,58,138,0.25);">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Kanan: Info Posko (2/5) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Status Posko --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                <p class="text-xs font-bold text-slate-700 mb-3">Status Posko</p>
                <div class="space-y-2.5">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Status</span>
                        <span class="font-semibold {{ $shelter->status === 'Tersedia' ? 'text-emerald-600' : 'text-red-600' }}">{{ $shelter->status }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Kapasitas</span>
                        <span class="font-semibold text-slate-800">{{ $shelter->capacity_current }}/{{ $shelter->capacity_max }} orang</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Koordinat</span>
                        <span class="font-mono text-slate-600 text-[11px]" id="coordStatusText">{{ $shelter->latitude }}, {{ $shelter->longitude }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Terakhir update</span>
                        <span class="text-slate-600">{{ $shelter->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            {{-- Relawan Bertugas --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <p class="text-xs font-bold text-slate-700">Relawan Bertugas</p>
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $assignedVolunteers->count() }}</span>
                </div>
                <div class="p-4">
                    @if($assignedVolunteers->count() > 0)
                        <div class="space-y-3">
                            @foreach($assignedVolunteers as $vol)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                         style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
                                        {{ strtoupper(substr($vol->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 truncate">{{ $vol->name }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $vol->skill }} · {{ $vol->phone_number }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada relawan ditugaskan.</p>
                    @endif
                </div>
            </div>

            {{-- Logistik Saat Ini --}}
            @if(!empty($shelter->logistics))
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                    <p class="text-xs font-bold text-slate-700 mb-3">Kebutuhan Logistik</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($shelter->logistics as $item)
                            <span class="text-[11px] font-medium px-2.5 py-1 rounded-lg border border-teal-100 text-teal-700" style="background: #F0FDFA;">{{ $item }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Hapus Posko --}}
            <div class="bg-white border border-red-100/50 rounded-2xl p-5" style="box-shadow: 0 1px 3px rgba(10,15,30,0.04);">
                <p class="text-xs font-semibold text-slate-800 mb-2">Hapus Posko</p>
                <p class="text-[11px] text-slate-500 mb-3">Tindakan ini tidak bisa dibatalkan. Semua data posko akan dihapus permanen.</p>
                <button type="button" onclick="document.getElementById('deleteSection').classList.toggle('hidden')"
                        class="w-full py-2 text-xs font-semibold text-red-600 bg-red-50/30 border border-red-100/60 rounded-xl hover:bg-red-50/70 transition-all duration-200 cursor-pointer">
                    Hapus Posko Ini
                </button>

                {{-- Inline confirmation --}}
                <div id="deleteSection" class="hidden mt-4 pt-4 border-t border-red-100">
                    <p class="text-[11px] text-slate-500 mb-2">Ketik <strong class="text-slate-800">{{ $shelter->name }}</strong> untuk konfirmasi:</p>
                    <input type="text" id="deleteConfirmInput" placeholder="Ketik nama posko..."
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-500/20 bg-white text-slate-800 mb-3">
                    <form action="{{ route('admin.shelter.delete', $shelter->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" id="deleteBtn" disabled
                                class="w-full py-2 text-xs font-semibold text-white bg-red-400 rounded-lg cursor-not-allowed opacity-50">
                            Konfirmasi Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.getElementById('photoInput')?.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.getElementById('photoPreview');
                const placeholder = document.getElementById('photoPlaceholder');
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Delete confirmation
    const expectedName = @json($shelter->name);
    document.getElementById('deleteConfirmInput')?.addEventListener('input', function() {
        const btn = document.getElementById('deleteBtn');
        if (this.value === expectedName) {
            btn.disabled = false;
            btn.classList.remove('bg-red-400', 'cursor-not-allowed', 'opacity-50');
            btn.classList.add('bg-red-600', 'hover:bg-red-700', 'cursor-pointer');
        } else {
            btn.disabled = true;
            btn.classList.add('bg-red-400', 'cursor-not-allowed', 'opacity-50');
            btn.classList.remove('bg-red-600', 'hover:bg-red-700', 'cursor-pointer');
        }
    });

    // Google Map Picker
    let map, marker;
    const defaultLat = parseFloat(document.getElementById('latitudeInput')?.value) || -7.5755;
    const defaultLng = parseFloat(document.getElementById('longitudeInput')?.value) || 110.8243;

    function initMapPicker() {
        const location = { lat: defaultLat, lng: defaultLng };
        map = new google.maps.Map(document.getElementById('mapPicker'), {
            center: location,
            zoom: 15,
            disableDefaultUI: true,
            gestureHandling: 'greedy',
            styles: [
                { featureType: "water",     elementType: "geometry", stylers: [{ color: "#c8dff0" }] },
                { featureType: "landscape", elementType: "geometry", stylers: [{ color: "#f0f4f8" }] },
                { featureType: "poi",       stylers: [{ visibility: "off" }] },
                { featureType: "transit",   stylers: [{ visibility: "off" }] },
            ]
        });

        marker = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true,
            title: "Lokasi Posko"
        });

        // Listen to marker dragend
        google.maps.event.addListener(marker, 'dragend', function() {
            const pos = marker.getPosition();
            updateCoordinates(pos.lat(), pos.lng(), true);
        });

        // Listen to map click
        google.maps.event.addListener(map, 'click', function(event) {
            marker.setPosition(event.latLng);
            updateCoordinates(event.latLng.lat(), event.latLng.lng(), true);
        });

        // Initialize Places Autocomplete if places library is loaded
        const addressInput = document.getElementById('addressInput');
        if (addressInput && window.google && google.maps && google.maps.places) {
            const autocomplete = new google.maps.places.Autocomplete(addressInput, {
                types: ['geocode', 'establishment'],
                componentRestrictions: { country: 'id' }
            });
            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (place.geometry && place.geometry.location) {
                    const loc = place.geometry.location;
                    map.setCenter(loc);
                    marker.setPosition(loc);
                    updateCoordinates(loc.lat(), loc.lng(), false);
                }
            });
        }
    }

    function updateCoordinates(lat, lng, shouldGeocode = true) {
        const latInput = document.getElementById('latitudeInput');
        const lngInput = document.getElementById('longitudeInput');
        if (latInput) latInput.value = lat.toFixed(6);
        if (lngInput) lngInput.value = lng.toFixed(6);
        
        const coordText = document.getElementById('coordStatusText');
        if (coordText) {
            coordText.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
        }

        // Reverse geocode: Coordinates -> Address String
        if (shouldGeocode) {
            const addressInput = document.getElementById('addressInput');
            if (addressInput) {
                if (window.google && google.maps && google.maps.Geocoder) {
                    const geocoder = new google.maps.Geocoder();
                    const latLngObj = new google.maps.LatLng(lat, lng);
                    geocoder.geocode({ location: latLngObj }, (results, status) => {
                        if (status === "OK" && results[0]) {
                            addressInput.value = results[0].formatted_address;
                        } else {
                            console.warn('Google reverse geocoding failed (' + status + '), calling Nominatim fallback...');
                            fallbackReverseGeocode(lat, lng, addressInput);
                        }
                    });
                } else {
                    fallbackReverseGeocode(lat, lng, addressInput);
                }
            }
        }
    }

    function fallbackReverseGeocode(lat, lng, element) {
        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
        fetch(url, {
            headers: {
                'Accept-Language': 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                element.value = data.display_name;
            }
        })
        .catch(err => {
            console.error('Nominatim fallback failed:', err);
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMapPicker" async defer></script>
@endsection
