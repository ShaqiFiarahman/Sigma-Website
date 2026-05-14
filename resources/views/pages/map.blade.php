@extends('layouts.app')
@section('title', 'Peta Bencana Interaktif')

@section('content')
<style>
    .map-container {
        width: 100%;
        height: calc(100vh - 200px);
        min-height: 500px;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(202, 196, 208, 0.55);
        box-shadow: 0 4px 24px rgba(102, 80, 164, 0.08);
    }

    .legend-card {
        background: #FFFFFF;
        border: 1px solid rgba(202, 196, 208, 0.55);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: #1D1B20;
        padding: 0.25rem 0;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .info-window-title {
        font-weight: 700;
        font-size: 14px;
        color: #1D1B20;
        margin-bottom: 4px;
    }
    .info-window-status {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        margin-bottom: 6px;
    }
    .info-window-desc {
        font-size: 12px;
        color: #625b71;
        margin-bottom: 6px;
    }
    .info-window-meta {
        font-size: 11px;
        color: #9e9e9e;
    }
</style>

{{-- Back link --}}
<div class="mb-4">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold transition-colors hover:opacity-70" style="color: #6650a4;">
        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

{{-- Title --}}

{{-- Legend --}}
<div class="legend-card mb-4">
    <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color: #6650a4;">Status Bencana</p>
    <div class="flex flex-wrap gap-x-5 gap-y-1">
        <div class="legend-item">
            <div class="legend-dot" style="background: #D32F2F;"></div>
            <span>Zona Darurat (Awas)</span>
        </div>
        <div class="legend-item">
            <div class="legend-dot" style="background: #1565C0;"></div>
            <span>Zona Bahaya (Siaga 1)</span>
        </div>
        <div class="legend-item">
            <div class="legend-dot" style="background: #616161;"></div>
            <span>Zona Waspada (Siaga 2)</span>
        </div>
        <div class="legend-item">
            <div class="legend-dot" style="background: #FFA000;"></div>
            <span>Pending (Belum diverifikasi)</span>
        </div>
        <div class="legend-item">
            <div class="legend-dot" style="background: #2E7D32;"></div>
            <span>Resolved (Selesai)</span>
        </div>
        <div class="legend-item">
            <div class="legend-dot" style="background: #6650a4; border-radius: 50%;"></div>
            <span>Titik Pengungsian</span>
        </div>
    </div>
</div>

{{-- Map --}}
<div class="map-container" id="map"></div>

@endsection

@section('scripts')
<script>
    let map;
    let markers = [];
    let infoWindow;

    // Color mapping sesuai Android
    const statusColors = {
        'AWAS':     '#D32F2F',
        'SIAGA_1':  '#1565C0',
        'SIAGA_2':  '#616161',
        'PENDING':  '#FFA000',
        'RESOLVED': '#2E7D32',
    };

    function initMap() {
        // Center: Surakarta
        const center = { lat: -7.5505, lng: 110.8063 };

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: center,
            mapTypeControl: true,
            fullscreenControl: true,
            streetViewControl: false,
            styles: [
                { featureType: "poi", stylers: [{ visibility: "off" }] },
                { featureType: "transit", stylers: [{ visibility: "off" }] },
            ]
        });

        infoWindow = new google.maps.InfoWindow();

        // Load both disasters and shelters
        loadDisasters();
        loadShelters();
    }

    async function loadDisasters() {
        try {
            const response = await fetch('{{ route("api.disasters") }}');
            const disasters = await response.json();

            disasters.forEach(d => {
                const color = statusColors[d.status] || '#FFA000';

                const marker = new google.maps.Marker({
                    position: { lat: d.lat, lng: d.lng },
                    map: map,
                    title: d.title,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: color,
                        fillOpacity: 0.9,
                        strokeColor: '#FFFFFF',
                        strokeWeight: 2,
                        scale: 10,
                    },
                });

                // Status badge color for info window
                let badgeBg, badgeColor;
                switch(d.status) {
                    case 'AWAS':     badgeBg = '#FFEBEE'; badgeColor = '#B71C1C'; break;
                    case 'SIAGA_1':  badgeBg = '#E3F2FD'; badgeColor = '#0D47A1'; break;
                    case 'SIAGA_2':  badgeBg = '#F5F5F5'; badgeColor = '#424242'; break;
                    case 'RESOLVED': badgeBg = '#E8F5E9'; badgeColor = '#1B5E20'; break;
                    default:         badgeBg = '#FFF3E0'; badgeColor = '#E65100'; break;
                }

                const content = `
                    <div style="max-width: 250px; padding: 4px;">
                        <p class="info-window-title">${d.title}</p>
                        <span class="info-window-status" style="background:${badgeBg}; color:${badgeColor};">
                            ${d.statusLabel}
                        </span>
                        <p class="info-window-desc">${d.description}</p>
                        <p class="info-window-meta">
                            <i class="bi bi-person"></i> ${d.reporter} &middot;
                            <i class="bi bi-clock"></i> ${d.date}
                        </p>
                        <a href="/laporan/detail/${d.id}" style="font-size:12px; color:#6650a4; font-weight:600; text-decoration:none;">
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
            const response = await fetch('{{ route("api.shelters") }}');
            const shelters = await response.json();

            shelters.forEach(s => {
                const marker = new google.maps.Marker({
                    position: { lat: s.lat, lng: s.lng },
                    map: map,
                    title: s.name,
                    icon: {
                        path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                        fillColor: '#6650a4',
                        fillOpacity: 0.9,
                        strokeColor: '#FFFFFF',
                        strokeWeight: 2,
                        scale: 6,
                    },
                });

                const statusBg = s.status === 'Penuh' ? '#FCE4EC' : '#E8F5E9';
                const statusColor = s.status === 'Penuh' ? '#C62828' : '#2E7D32';
                const logisticsHtml = s.logistics.map(l =>
                    `<span style="display:inline-block; background:#EADDFF; color:#6650a4; font-size:10px; font-weight:600; padding:2px 6px; border-radius:4px; margin:2px;">${l}</span>`
                ).join('');

                const content = `
                    <div style="max-width: 260px; padding: 4px;">
                        <p class="info-window-title">${s.name}</p>
                        <span class="info-window-status" style="background:${statusBg}; color:${statusColor};">
                            ${s.status}
                        </span>
                        <p style="font-size:12px; color:#1D1B20; margin:4px 0;">
                            Kapasitas: <b>${s.capacity}</b> orang
                        </p>
                        <p style="font-size:11px; color:#625b71; margin-bottom:4px;">Kebutuhan Logistik:</p>
                        <div style="margin-bottom:6px;">${logisticsHtml}</div>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=${s.lat},${s.lng}"
                           target="_blank"
                           style="font-size:12px; color:#6650a4; font-weight:600; text-decoration:none;">
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
@endsection
