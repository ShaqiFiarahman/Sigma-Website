<section class="animate-fade-up" style="animation-delay: 0.2s;">
    <div class="mb-4 px-1">
        <h2 class="text-lg font-bold text-slate-900">Peta Bencana</h2>
        <p class="text-xs text-slate-500 mt-0.5">Pantau kondisi terkini di sekitar Anda</p>
    </div>

    <div class="relative">
        {{-- Legend --}}
        <div class="absolute bottom-6 right-4 z-10 shadow-lg bg-white/90 border border-slate-200/60 rounded-2xl p-4">
            <p class="text-xs font-bold uppercase tracking-wider mb-2 text-slate-500">Legenda</p>
            <div class="flex flex-col gap-y-1.5">
                <div class="flex items-center gap-2 text-xs text-slate-700">
                    <div class="w-3 h-3 rounded-full" style="background: #D32F2F;"></div>
                    <span>Awas</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-700">
                    <div class="w-3 h-3 rounded-full" style="background: #EA580C;"></div>
                    <span>Siaga 1</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-700">
                    <div class="w-3 h-3 rounded-full" style="background: #7C3AED;"></div>
                    <span>Siaga 2</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-700">
                    <i class="bi bi-house-door-fill text-[#10B981] text-sm" style="width: 12px; text-align: center;"></i>
                    <span>Posko</span>
                </div>
            </div>
        </div>

        {{-- Map Container --}}
        <div class="w-full rounded-2xl overflow-hidden border border-slate-200/60" style="height: 550px; box-shadow: 0 4px 24px rgba(10,15,30,0.08);" id="map"></div>
    </div>
</section>

<script>
    let map;
    let markers = [];
    let infoWindow;

    const statusColors = {
        'AWAS': '#D32F2F',
        'SIAGA_1': '#EA580C',
        'SIAGA_2': '#7C3AED',
        'PENDING': '#FFA000',
        'RESOLVED': '#2E7D32',
    };

    function initMap() {
        const center = { lat: -7.5505, lng: 110.8063 };

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: center,
            disableDefaultUI: true,
            gestureHandling: 'greedy',
            styles: [
                { featureType: "poi", stylers: [{ visibility: "off" }] },
                { featureType: "transit", stylers: [{ visibility: "off" }] },
            ]
        });

        infoWindow = new google.maps.InfoWindow();
        loadDisasters();
        loadShelters();
    }

    async function loadDisasters() {
        try {
            const response = await fetch('/api/disasters');
            const disasters = await response.json();

            disasters.forEach(d => {
                const color = statusColors[d.status] || '#FFA000';

                const marker = new google.maps.Marker({
                    position: { lat: d.lat, lng: d.lng },
                    map: map,
                    title: d.title,
                    icon: {
                        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
                              <style>
                                @keyframes pulse { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(3); opacity: 0; } }
                                .pulse { animation: pulse 1.5s ease-out infinite; transform-origin: 20px 20px; }
                              </style>
                              <circle cx="20" cy="20" r="6" fill="${color}" />
                              <circle cx="20" cy="20" r="6" fill="none" stroke="${color}" stroke-width="2" class="pulse" />
                              <circle cx="20" cy="20" r="6" fill="none" stroke="#FFFFFF" stroke-width="1" />
                            </svg>
                        `),
                        scaledSize: new google.maps.Size(40, 40),
                        anchor: new google.maps.Point(20, 20),
                    },
                    optimized: false
                });

                let badgeBg, badgeColor;
                switch (d.status) {
                    case 'AWAS': badgeBg = '#FFEBEE'; badgeColor = '#B71C1C'; break;
                    case 'SIAGA_1': badgeBg = '#FFF7ED'; badgeColor = '#C2410C'; break;
                    case 'SIAGA_2': badgeBg = '#F5F3FF'; badgeColor = '#6D28D9'; break;
                    case 'RESOLVED': badgeBg = '#E8F5E9'; badgeColor = '#1B5E20'; break;
                    default: badgeBg = '#FFF3E0'; badgeColor = '#E65100'; break;
                }

                const content = `
                    <div style="max-width: 250px; padding: 4px;">
                        <p style="font-weight:700; font-size:14px; color:#1D1B20; margin-bottom:4px;">${d.title}</p>
                        <span style="display:inline-block; font-size:11px; font-weight:700; padding:2px 8px; border-radius:6px; margin-bottom:6px; background:${badgeBg}; color:${badgeColor};">
                            ${d.statusLabel}
                        </span>
                        <p style="font-size:12px; color:#625b71; margin-bottom:6px;">${d.description}</p>
                        <p style="font-size:11px; color:#9e9e9e;">
                            <i class="bi bi-person"></i> ${d.reporter} &middot;
                            <i class="bi bi-clock"></i> ${d.date}
                        </p>
                        <a href="/laporan/detail/${d.id}" style="font-size:12px; color:#3B6FE8; font-weight:600; text-decoration:none;">
                            Lihat Detail →
                        </a>
                    </div>
                `;

                marker.addListener('click', () => {
                    infoWindow.setContent(content);
                    infoWindow.open(map, marker);
                });

                markers.push(marker);
            });

            fitBounds();
        } catch (error) {
            console.error('Failed to load disasters:', error);
        }
    }

    async function loadShelters() {
        try {
            const response = await fetch('/api/shelters');
            const shelters = await response.json();

            shelters.forEach(s => {
                const marker = new google.maps.Marker({
                    position: { lat: s.lat, lng: s.lng },
                    map: map,
                    title: s.name,
                    icon: {
                        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#10B981" viewBox="0 0 16 16"><path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></svg>'),
                        scaledSize: new google.maps.Size(24, 24),
                    },
                });

                const statusBg = s.status === 'Penuh' ? '#FCE4EC' : '#E8F5E9';
                const statusColor = s.status === 'Penuh' ? '#C62828' : '#2E7D32';
                const logisticsHtml = (s.logistics || []).map(l =>
                    `<span style="display:inline-block; background:#E6FFFA; color:#0d9488; font-size:10px; font-weight:600; padding:2px 6px; border-radius:4px; margin:2px;">${l}</span>`
                ).join('');

                const content = `
                    <div style="max-width: 260px; padding: 4px;">
                        <p style="font-weight:700; font-size:14px; color:#1D1B20; margin-bottom:4px;">${s.name}</p>
                        <span style="display:inline-block; font-size:11px; font-weight:700; padding:2px 8px; border-radius:6px; margin-bottom:6px; background:${statusBg}; color:${statusColor};">
                            ${s.status}
                        </span>
                        <p style="font-size:12px; color:#1D1B20; margin:4px 0;">
                            Kapasitas: <b>${s.capacity}</b> orang
                        </p>
                        <p style="font-size:11px; color:#625b71; margin-bottom:4px;">Kebutuhan Logistik:</p>
                        <div style="margin-bottom:6px;">${logisticsHtml}</div>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=${s.lat},${s.lng}"
                           target="_blank"
                           style="font-size:12px; color:#3B6FE8; font-weight:600; text-decoration:none;">
                            Petunjuk Arah →
                        </a>
                    </div>
                `;

                marker.addListener('click', () => {
                    infoWindow.setContent(content);
                    infoWindow.open(map, marker);
                });

                markers.push(marker);
            });

            fitBounds();
        } catch (error) {
            console.error('Failed to load shelters:', error);
        }
    }

    function fitBounds() {
        if (markers.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            markers.forEach(m => bounds.extend(m.getPosition()));
            map.fitBounds(bounds);

            const listener = google.maps.event.addListener(map, 'idle', () => {
                if (map.getZoom() > 15) map.setZoom(15);
                google.maps.event.removeListener(listener);
            });
        }
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap" async defer></script>
