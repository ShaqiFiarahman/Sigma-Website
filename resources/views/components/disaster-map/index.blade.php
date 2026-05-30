<section class="animate-fade-up" style="animation-delay: 0.2s;">
    <div class="mb-4 px-1">
        <h2 class="text-lg font-bold text-slate-900">Peta Bencana</h2>
        <p class="text-xs text-slate-500 mt-0.5">Pantau kondisi terkini di sekitar Anda</p>
    </div>

    <div class="relative">
        {{-- Legend --}}
        @include('components.disaster-map.legend')

        {{-- Map Container --}}
        <div class="w-full rounded-2xl overflow-hidden border border-slate-200/60"
            style="height: 550px; box-shadow: 0 4px 24px rgba(10,15,30,0.08);" id="map"></div>
    </div>
</section>

<style>
    @keyframes slideIn {
        from {
            transform: translateY(-20px) scale(0.95);
            opacity: 0;
        }

        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        to {
            transform: translateY(10px) scale(0.9);
            opacity: 0;
        }
    }

    .toast-slide-in {
        animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .toast-fade-out {
        animation: fadeOut 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Custom GM InfoWindow styling */
    .gm-style-iw {
        padding: 0px !important;
    }

    .gm-style-iw-c {
        border-radius: 16px !important;
        box-shadow: 0 10px 40px rgba(10, 15, 30, 0.12) !important;
        padding: 0px !important;
        overflow: hidden !important;
        min-width: 240px !important;
        max-width: 240px !important;
    }

    .gm-style-iw-d {
        overflow: hidden !important;
        max-height: none !important;
        padding: 0px !important;
        min-width: 240px !important;
        max-width: 240px !important;
    }

    .gm-style-iw-d>div,
    .gm-style-iw-d>div>div {
        padding: 0px !important;
        margin: 0px !important;
        overflow: hidden !important;
    }

    .gm-style-iw-c button.gm-ui-hover-effect {
        width: 22px !important;
        height: 22px !important;
        opacity: 0.75 !important;
        background-color: rgba(255, 255, 255, 0.9) !important;
        box-shadow: 0 2px 6px rgba(10, 15, 30, 0.15) !important;
        border-radius: 50% !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease !important;
        z-index: 99999 !important;
    }

    .gm-style-iw-c:has(.disaster-popup-content) button.gm-ui-hover-effect {
        top: 28px !important;
        right: 6px !important;
    }

    .gm-style-iw-c:has(.shelter-popup-content) button.gm-ui-hover-effect {
        top: 30px !important;
        right: 4px !important;
    }

    .gm-style-iw-c button.gm-ui-hover-effect:hover {
        opacity: 0.95 !important;
        background-color: rgba(255, 255, 255, 1) !important;
        transform: scale(1.08);
    }

    .gm-style-iw-c button.gm-ui-hover-effect span {
        margin: 0 !important;
        width: 10px !important;
        height: 10px !important;
    }
</style>

{{-- Utility functions: formatLocalDate, getShortReporter, statusColors, typePaths --}}
@include('components.disaster-map.map-helpers')

<script>
    let map;
    let disasterMarkers = {};
    let shelterMarkers = [];
    let infoWindow;
    let initialLoad = true;
    let hasZoomedToNewest = false;

    function initMap() {
        const center = { lat: -7.5505, lng: 110.8063 };

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 11,
            center: center,
            mapTypeId: 'terrain', // Tipe peta fisik / medan (Terrain)
            disableDefaultUI: true,
            gestureHandling: 'greedy',
            styles: [
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#c8dff0" }] },
                { featureType: "poi", stylers: [{ visibility: "off" }] },
                { featureType: "transit", stylers: [{ visibility: "off" }] },
            ]
        });

        infoWindow = new google.maps.InfoWindow();
        loadDisasters();
        loadShelters();

        // Start polling disasters every 10 seconds for real-time updates
        setInterval(loadDisasters, 10000);
    }

    function buildDisasterPopup(d, badgeBg, badgeColor, shortReporter, displayDate) {
        const photoHtml = d.photo ? `
            <div style="height: 110px; overflow: hidden; position: relative; margin: 0;">
                <img src="${d.photo}" style="width: 100%; height: 100%; display: block; object-fit: cover;" alt="Thumbnail">
                <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 35px; background: linear-gradient(to top, rgba(0,0,0,0.35) 0%, transparent 100%);"></div>
            </div>` : '';

        return `
            <div class="disaster-popup-content" style="width: 240px; font-family: sans-serif; padding: 0; margin: 0; overflow: hidden;">
                ${photoHtml}
                <div style="padding: 12px;">
                    <div style="margin-bottom: 6px;">
                        <span style="display:inline-block; font-size:9px; font-weight:600; padding:2px 7px; border-radius:6px; background:${badgeBg}; color:${badgeColor}; text-transform: uppercase; letter-spacing: 0.3px;">
                            ${d.statusLabel}
                        </span>
                    </div>
                    <p style="font-weight: 600; font-size: 12.5px; color: #0F172A; margin: 0 0 5px 0; line-height: 1.35; letter-spacing: -0.01em;">${d.title}</p>
                    <p style="font-size: 11px; color: #475569; margin: 0 0 8px 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                        ${d.description}
                    </p>
                    <p style="font-size: 9.5px; color: #94a3b8; margin: 0 0 8px 0; display: flex; align-items: center; gap: 4px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="bi bi-person-fill"></i> ${shortReporter} &middot;
                        <i class="bi bi-clock-fill"></i> ${displayDate}
                    </p>
                    <a href="/laporan/detail/${d.id}" class="popup-detail-link" style="font-size: 11px; font-weight: 700; color: #2563EB; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                        Lihat Detail
                    </a>
                </div>
            </div>`;
    }

    function buildShelterPopup(s) {
        const statusColor = s.status === 'Penuh' ? '#B91C1C' : '#15803D';
        const statusDot = s.status === 'Penuh' ? '#EF4444' : '#10B981';
        const logisticsHtml = (s.logistics || []).map(l =>
            `<span style="display:inline-block; background:#F0FDFA; color:#0D9488; font-size:9px; font-weight:600; padding:2px 6px; border-radius:4px; border:1px solid #CCFBF1;">${l}</span>`
        ).join('');

        const photoHtml = s.photo_url ? `
            <div style="height:100px; overflow:hidden; position:relative; margin:0;">
                <img src="${s.photo_url}" style="width:100%; height:100%; display:block; object-fit:cover;" alt="${s.name}">
                <div style="position:absolute; bottom:0; left:0; right:0; height:30px; background:linear-gradient(to top, rgba(0,0,0,0.3) 0%, transparent 100%);"></div>
            </div>` : '';

        return `
            <div class="shelter-popup-content" style="width:240px; font-family:sans-serif; padding:0; margin:0; overflow:hidden;">
                ${photoHtml}
                <div style="padding:${s.photo_url ? '12px' : '18px'} 14px 14px;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:#ECFDF5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="#10B981"><path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-weight:600;font-size:12.5px;color:#0F172A;margin:0;line-height:1.35;">${s.name}</p>
                            <div style="display:flex;align-items:center;gap:5px;margin-top:3px;">
                                ${(s.status !== 'Penuh' && s.status !== 'Tersedia') ? `<span style="width:6px;height:6px;border-radius:50%;background:${statusDot};"></span>` : ''}
                                <span style="font-size:9.5px;font-weight:600;color:${statusColor};">${s.status}</span>
                                <span style="font-size:9.5px;color:#94A3B8;">·</span>
                                <span style="font-size:9.5px;color:#64748B;">${s.capacity} orang</span>
                            </div>
                        </div>
                    </div>
                    ${logisticsHtml ? `<div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:8px;">${logisticsHtml}</div>` : ''}
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${s.lat},${s.lng}"
                       target="_blank"
                       style="font-size:11px;font-weight:700;color:#3B6FE8;text-decoration:none;display:inline-flex;align-items:center;gap:3px;">
                        Petunjuk Arah
                    </a>
                </div>
            </div>`;
    }

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

    function showDisasterToast(d) {
        if (typeof window.showDisasterToast === 'function') {
            window.showDisasterToast(d);
        }
    }

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

{{-- API Loaders: loadDisasters, loadShelters --}}
@include('components.disaster-map.map-loaders')

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"
    async defer></script>