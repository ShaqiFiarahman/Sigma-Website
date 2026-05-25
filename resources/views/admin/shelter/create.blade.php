@extends('layouts.app')
@section('title', 'Tambah Posko')
@section('subtitle', 'Tambahkan posko pengungsian baru ke dalam sistem')

@section('page-actions')
    <x-ui.back-button :useHistory="true" />
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

        <form action="{{ route('admin.shelter.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Photo Banner --}}
            <div class="relative h-36 bg-slate-100 overflow-hidden group">
                <div class="w-full h-full flex items-center justify-center" id="photoPlaceholder">
                    <div class="text-center">
                        <i class="bi bi-camera text-2xl text-slate-300"></i>
                        <p class="text-xs text-slate-400 mt-1">Klik untuk menambahkan foto</p>
                    </div>
                </div>
                <img src="" alt="" class="w-full h-full object-cover hidden" id="photoPreview">
                <label for="photoInput" class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/30 transition-all cursor-pointer">
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity text-xs font-semibold text-white bg-black/50 px-3 py-1.5 rounded-lg">Pilih Foto</span>
                </label>
                <input type="file" name="photo" id="photoInput" accept="image/*" class="hidden">
            </div>

            <div class="p-6 space-y-6">

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                        <p class="text-xs font-semibold text-red-700 mb-1">Terdapat kesalahan:</p>
                        <ul class="text-xs text-red-600 list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Section: Informasi Dasar --}}
                <div>
                    <p class="text-xs font-bold text-slate-700 mb-3">Informasi Dasar</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Nama Posko</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Posko Evakuasi Kelurahan Maju"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Alamat</label>
                            <input type="text" name="address" id="addressInput" value="{{ old('address') }}" placeholder="Ketik alamat atau cari di peta..."
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                        </div>

                        {{-- Hidden coordinates --}}
                        <input type="hidden" name="latitude" id="latitudeInput" value="{{ old('latitude', '-7.5755') }}">
                        <input type="hidden" name="longitude" id="longitudeInput" value="{{ old('longitude', '110.8243') }}">

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
                            <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" placeholder="6281234567890"
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
                            <input type="number" name="capacity_current" value="{{ old('capacity_current', 0) }}" required min="0"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Maksimal</label>
                            <input type="number" name="capacity_max" value="{{ old('capacity_max', 100) }}" required min="1"
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
                                <option value="Tersedia" {{ old('status') === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="Penuh" {{ old('status') === 'Penuh' ? 'selected' : '' }}>Penuh</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Kebutuhan Logistik <span class="text-slate-400">(pisahkan dengan koma)</span></label>
                            <input type="text" name="logistics" value="{{ old('logistics') }}" placeholder="Sembako, Air Mineral, Selimut"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2" style="background: #FAFBFD;">
                <button type="button" onclick="history.back()" class="px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer">Batal</button>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl cursor-pointer" style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(30,58,138,0.25);">
                    <i class="bi bi-plus-lg mr-1"></i> Tambah Posko
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
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

    // Google Map Picker
    let map, marker;
    const defaultLat = parseFloat(document.getElementById('latitudeInput')?.value) || -7.5755;
    const defaultLng = parseFloat(document.getElementById('longitudeInput')?.value) || 110.8243;

    function initMapPicker() {
        const location = { lat: defaultLat, lng: defaultLng };
        map = new google.maps.Map(document.getElementById('mapPicker'), {
            center: location,
            zoom: 13,
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

        google.maps.event.addListener(marker, 'dragend', function() {
            const pos = marker.getPosition();
            updateCoordinates(pos.lat(), pos.lng(), true);
        });

        google.maps.event.addListener(map, 'click', function(event) {
            marker.setPosition(event.latLng);
            updateCoordinates(event.latLng.lat(), event.latLng.lng(), true);
        });

        // Places Autocomplete
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

        if (shouldGeocode) {
            const addressInput = document.getElementById('addressInput');
            if (addressInput) {
                if (window.google && google.maps && google.maps.Geocoder) {
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ location: new google.maps.LatLng(lat, lng) }, (results, status) => {
                        if (status === "OK" && results[0]) {
                            addressInput.value = results[0].formatted_address;
                        } else {
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
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`, {
            headers: { 'Accept-Language': 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7' }
        })
        .then(r => r.json())
        .then(data => { if (data && data.display_name) element.value = data.display_name; })
        .catch(err => console.error('Nominatim fallback failed:', err));
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMapPicker" async defer></script>
@endsection
