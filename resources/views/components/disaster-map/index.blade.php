<section class="animate-fade-up" style="animation-delay: 0.2s;">
    <div class="mb-4 px-1">
        <h2 class="text-lg font-bold text-slate-900">Peta Bencana</h2>
        <p class="text-xs text-slate-500 mt-0.5">Pantau kondisi terkini di sekitar Anda</p>
    </div>

    <div class="relative">
        {{-- legend --}}
        @include('components.disaster-map.legend')

        {{-- container map --}}
        <div class="w-full h-[350px] sm:h-[450px] lg:h-[550px] rounded-2xl overflow-hidden border border-slate-200/60"
            style="box-shadow: 0 4px 24px rgba(10,15,30,0.08);" id="map"></div>
    </div>

    {{-- legend mobile view --}}
    <div class="sm:hidden mt-3 flex flex-wrap items-center justify-center gap-x-4 gap-y-2.5 p-3.5 bg-white border border-slate-200/60 rounded-2xl shadow-sm">
        <div class="flex items-center gap-1.5 text-xs text-slate-700 font-semibold">
            <div class="w-2.5 h-2.5 rounded-full" style="background: #D32F2F;"></div>
            <span>Awas</span>
        </div>
        <div class="flex items-center gap-1.5 text-xs text-slate-700 font-semibold">
            <div class="w-2.5 h-2.5 rounded-full" style="background: #EA580C;"></div>
            <span>Siaga 1</span>
        </div>
        <div class="flex items-center gap-1.5 text-xs text-slate-700 font-semibold">
            <div class="w-2.5 h-2.5 rounded-full" style="background: #7C3AED;"></div>
            <span>Siaga 2</span>
        </div>
        <div class="w-px h-3.5 bg-slate-200"></div>
        <div class="flex items-center gap-1.5 text-xs text-slate-700 font-semibold">
            <i class="bi bi-house-door-fill text-[#10B981] text-xs"></i>
            <span>Posko Shelter</span>
        </div>
    </div>
</section>


{{-- utility helper functions --}}
@include('components.disaster-map.map-helpers')

<script>
    let map;
    let disasterMarkers = {};
    let shelterMarkers = [];
    let infoWindow;
    let initialLoad = true;
    let hasZoomedToNewest = false;

    // inisialisasi google maps dengan koordinat default
    function initMap() {
        const center = { lat: -7.5505, lng: 110.8063 };

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 11,
            center: center,
            mapTypeId: 'terrain', // Tipe peta fisik / medan (Terrain)
            disableDefaultUI: true,
            gestureHandling: 'cooperative',
            styles: [
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#c8dff0" }] },
                { featureType: "poi", stylers: [{ visibility: "off" }] },
                { featureType: "transit", stylers: [{ visibility: "off" }] },
            ]
        });

        infoWindow = new google.maps.InfoWindow();
        loadDisasters();
        loadShelters();

        // polling data bencana tiap 10 detik biar realtime
        setInterval(loadDisasters, 10000);
    }

    // bikin template popup html buat marker bencana
    function buildDisasterPopup(d, badgeBg, badgeColor, shortReporter, displayDate) {
        const photoHtml = d.photo ? `
            <div class="popup-photo-wrap">
                <img src="${d.photo}" class="popup-photo" alt="Thumbnail">
                <div class="popup-photo-overlay"></div>
            </div>` : '';

        return `
            <div class="disaster-popup-content popup-card ${d.photo ? 'has-photo' : 'no-photo'}">
                ${photoHtml}
                <div class="popup-body">
                    <div class="popup-badge-row">
                        <span class="popup-badge" style="background:${badgeBg}; color:${badgeColor};">
                            ${d.statusLabel}
                        </span>
                    </div>
                    <p class="popup-title">${d.title}</p>
                    <p class="popup-desc">${d.description}</p>
                    <p class="popup-meta">
                        <i class="bi bi-person-fill"></i> ${shortReporter} &middot;
                        <i class="bi bi-clock-fill"></i> ${displayDate}
                    </p>
                    <a href="/laporan/detail/${d.id}" class="popup-link">
                        Lihat Detail
                    </a>
                </div>
            </div>`;
    }

    // bikin template popup html buat marker posko shelter
    function buildShelterPopup(s) {
        const statusColor = s.status === 'Penuh' ? '#B91C1C' : '#15803D';
        const statusDot = s.status === 'Penuh' ? '#EF4444' : '#10B981';
        const logisticsHtml = (s.logistics || []).map(l =>
            `<span class="popup-logistics-tag">${l}</span>`
        ).join('');

        const photoHtml = s.photo_url ? `
            <div class="popup-photo-wrap shelter-photo-wrap">
                <img src="${s.photo_url}" class="popup-photo" alt="${s.name}">
                <div class="popup-photo-overlay"></div>
            </div>` : '';

        return `
            <div class="shelter-popup-content popup-card ${s.photo_url ? 'has-photo' : 'no-photo'}">
                ${photoHtml}
                <div class="popup-body ${s.photo_url ? 'has-photo-body' : 'no-photo-body'}">
                    <div class="popup-shelter-header">
                        <div class="popup-shelter-icon-box">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="#10B981"><path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></svg>
                        </div>
                        <div class="popup-shelter-info">
                            <p class="popup-title">${s.name}</p>
                            <div class="popup-shelter-sub">
                                ${(s.status !== 'Penuh' && s.status !== 'Tersedia') ? `<span class="popup-status-dot" style="background:${statusDot};"></span>` : ''}
                                <span class="popup-status-text" style="color:${statusColor};">${s.status}</span>
                                <span class="popup-status-sep">·</span>
                                <span class="popup-capacity-text">${s.capacity} orang</span>
                            </div>
                        </div>
                    </div>
                    ${logisticsHtml ? `<div class="popup-logistics-row">${logisticsHtml}</div>` : ''}
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${s.lat},${s.lng}"
                       target="_blank"
                       class="popup-link">
                        Petunjuk Arah
                    </a>
                </div>
            </div>`;
    }

    // pasang zoom map otomatis biar pas dengan sebaran marker yang ada
    function fitBounds() {
        const allMarkers = [...Object.values(disasterMarkers).map(dm => dm.marker), ...shelterMarkers];
        if (allMarkers.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            allMarkers.forEach(m => bounds.extend(m.getPosition()));
            map.fitBounds(bounds);

            const listener = google.maps.event.addListener(map, 'idle', () => {
                if (map.getZoom() > 15) map.setZoom(15);
                google.maps.event.removeListener(listener);
            });
        }
    }

    // tampilin toast notifikasi bencana baru
    function showDisasterToast(d) {
        if (typeof window.showDisasterToast === 'function') {
            window.showDisasterToast(d);
        }
    }

    // arahin map dan zoom ke marker bencana tertentu pas diklik
    function focusOnDisaster(id) {
        const item = disasterMarkers[id];
        if (item) {
            map.panTo(item.marker.getPosition());
            map.setZoom(15);

            infoWindow.setContent(buildDisasterPopup(
                item.data, item.badgeBg, item.badgeColor, item.shortReporter, item.displayDate
            ));
            infoWindow.open(map, item.marker);

            item.marker.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(() => item.marker.setAnimation(null), 1400);
        }
    }
</script>

{{-- map loader functions --}}
@include('components.disaster-map.map-loaders')

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"
    async defer></script>