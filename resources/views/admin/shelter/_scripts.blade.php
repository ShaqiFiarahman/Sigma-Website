{{-- Shelter Edit: Scripts --}}
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

        const coordText = document.getElementById('coordStatusText');
        if (coordText) coordText.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);

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
